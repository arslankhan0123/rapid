<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Queries\RestoreDataTable;
use Yajra\DataTables\DataTables;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Restore;
use App\Models\Backup;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Laracasts\Flash\Flash;

class RestoreController extends AppBaseController
{





    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new RestoreDataTable())->get($request->only(['group'])))->make(true);
        }

        return view('restore.index');
    }

    public function fromFile(Request $request)
    {
        // Get the file name from the request
        $fileName = $request->input('file');
        $id = $request->input('id');

        // Define the path to the backups directory
        $filePath = public_path('uploads/backups/' . $fileName);

        // Check if the file exists
        if (file_exists($filePath)) {
            // Create a new request instance for the upload method
            $newRequest = new Request();

            // Add the file to the new request
            $newRequest->files->set('restore_file', new \Illuminate\Http\UploadedFile(
                $filePath,
                $fileName,
                'application/zip', // MIME type of the file
                null,
                true // Mark the file as already moved (since it's in the uploads directory)
            ));


            // Backup::where('id', $id)->update([
            //     'restored_by' => Auth::user()->id,  // Set the restored_by field to the authenticated user's ID
            //     'restored_at' => Carbon::now()      // Set the restored_at field to the current timestamp
            // ]);

            // Call the upload method and return its result
            return $this->upload($newRequest);
        } else {
            // Return an error response
            return response()->json(['message' => 'Backup file not found!'], 404);
        }
    }



    public function upload(Request $request)
    {
        // Validate that the uploaded file is a ZIP file
        $request->validate([
            'restore_file' => 'required|file|mimes:zip', // Checking for a ZIP file
        ]);

        // Handle the uploaded ZIP file
        $file = $request->file('restore_file');
        $originalFileName = $file->getClientOriginalName(); // Get the original file name



        $tmpFileName = 'restore-' . time() . '.zip';
        $zipFilePath = $file->storeAs('restores', $tmpFileName); // Store the zip file in 'restores' folder

        // Get the full path using the Storage facade
        $fullZipPath = Storage::path($zipFilePath);

        // Create a temporary directory to extract the zip file
        $extractPath = storage_path('app/temp_restore_' . time());

        // Create the directory if it doesn't exist
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        // Initialize the ZipArchive class to open and extract the file
        $zip = new ZipArchive;
        if ($zip->open($fullZipPath) === TRUE) {
            // Extract all files to the temporary path
            $zip->extractTo($extractPath);
            $zip->close();

            // Scan the extracted directory for SQL files
            $extractedFiles = scandir($extractPath);
            $sqlFile = null;

            // Look for the .sql file in the extracted files
            foreach ($extractedFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $sqlFile = $file;
                    break;
                }
            }

            // If an SQL file is found, call the import method
            if ($sqlFile) {
                $sqlFilePath = $extractPath . '/' . $sqlFile;
                $this->importSqlFile($sqlFilePath); // Call the import method

                // --------------------------------------------------
                // Create a new Restore model instance
                // Use the create method to insert the record into the database
                // Restore::create([
                //     'file_name' => $tmpFileName, // Use the original file name
                //     'user_id' => auth()->id(), // Get the ID of the currently authenticated user
                // ]);

                app(PermissionRegistrar::class)->forgetCachedPermissions();
                // Extract the part before the first space
                $parts = explode(' ', $originalFileName, 2);
                $baseFileName = $parts[0];

                // First try an exact match
                $exactMatch = Backup::where('name', '=', $originalFileName)->first();
                if ($exactMatch) {
                    $exactMatch->update([
                        'restored_by' => Auth::user()->id,
                        'restored_at' => Carbon::now()
                    ]);
                } else {
                    Backup::where('name', 'like', $baseFileName . '%')->update([
                        'restored_by' => Auth::user()->id,
                        'restored_at' => Carbon::now()
                    ]);
                }

                // Delete the temporary directory and the original ZIP file
                Storage::deleteDirectory($extractPath);
                Storage::delete($zipFilePath);

                Flash::success("Restored Successfully");
                return $this->sendSuccess(__('Restored Successfully'));
            } else {
                return $this->sendError('Failed To Restore!! File Problem');
            }
        } else {
            return $this->sendError('Failed to open the ZIP file.');
        }
    }


    protected function importSqlFile($filePath)
    {
        // Get the content of the SQL file
        $sql = file_get_contents($filePath);

        // Break the SQL content into individual queries
        $queries = explode(";", $sql);

        // List of tables to be ignored during the import
        $ignoreTables = ['article_groups', 'lead_statuses', 'leads', 'restores', 'migrations', 'failed_jobs', 'backup_schedules', 'backups'];
        // Get the list of tables from the database
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map('current', $tables); // Extract table names from the result

        // Get foreign key constraints
        $foreignKeys = DB::select(
            '
        SELECT
            TABLE_NAME AS child_table,
            REFERENCED_TABLE_NAME AS parent_table
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_NAME IS NOT NULL
            AND TABLE_SCHEMA = ?',
            [config('database.connections.mysql.database')]
        );

        // Build table relationships
        $relationships = [];
        foreach ($foreignKeys as $fk) {
            $relationships[$fk->child_table][] = $fk->parent_table;
        }

        // Determine the order of tables (parent tables first)
        $sortedTables = $this->sortTablesByDependencies($tableNames, $relationships);

        // Determine which tables to delete from (all tables except the ones to be ignored)
        $tablesToDelete = array_diff($sortedTables, $ignoreTables);

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables in the sorted order
        foreach ($tablesToDelete as $table) {
            DB::statement("TRUNCATE TABLE `{$table}`");
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Execute each query from the SQL file using prepared statements
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                // Skip queries for tables that should be ignored
                $tableName = $this->getTableNameFromQuery($query);
                if (!in_array($tableName, $ignoreTables)) {
                    try {
                        DB::statement($query);
                    } catch (\Exception $e) {
                        // Log the error for debugging
                        \Log::error("Failed to execute query: $query. Error: " . $e->getMessage());
                    }
                }
            }
        }

        return true;
    }

    /**
     * Sort tables based on their dependencies.
     *
     * @param array $tables List of table names.
     * @param array $relationships Array of table relationships.
     * @return array Sorted list of table names.
     */
    protected function sortTablesByDependencies($tables, $relationships)
    {
        $sorted = [];
        $visited = [];

        $visitTable = function ($table) use (&$visitTable, &$sorted, &$visited, $relationships) {
            if (isset($visited[$table])) {
                return;
            }

            // Mark table as visiting
            $visited[$table] = true;

            // Visit parent tables first
            if (isset($relationships[$table])) {
                foreach ($relationships[$table] as $parentTable) {
                    if (!isset($visited[$parentTable])) {
                        $visitTable($parentTable);
                    }
                }
            }

            // Mark table as visited
            $visited[$table] = false;

            // Add to sorted list
            $sorted[] = $table;
        };

        foreach ($tables as $table) {
            if (!isset($visited[$table])) {
                $visitTable($table);
            }
        }

        return $sorted;
    }

    /**
     * Extracts the table name from a query string.
     * Assumes the query is a simple INSERT statement.
     */
    protected function getTableNameFromQuery($query)
    {
        // Remove extra spaces and extract table name from INSERT statements
        if (preg_match('/INSERT\s+INTO\s+`?(\w+)`?/i', $query, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
