<?php

namespace App\Http\Controllers;

use App\Mail\BackupCreatedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\BackupSchedule;
use App\Models\Backup;
use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BackupController extends AppBaseController
{

    protected $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    public function index()
    {
        // Fetch backup records along with the user information from the Backup model
        $backups = Backup::with(['user', 'restoredBy']) // Eager load the user relationship
            ->orderBy('updated_at', 'desc')
            ->get();

        // Format the created_at date and map the backup details with user information
        $backups = $backups->map(function ($backup) {

            $f_name = $backup->user->first_name ?? '';
            $l_name = $backup->user->last_name ?? '';
            return [
                'file_name' => $backup->name,
                'created_at' => Carbon::parse($backup->created_at)->format('d-m-Y h:i A'),
                'user_name' => $backup->user ? $backup->user->first_name . " " . $backup->user->last_name : 'Automatic',
                'id' => $backup->id,
                'restored_at' => $backup->restored_at ? Carbon::parse($backup->restored_at)->format('d-m-Y h:i A') : '',
                'restored_by' => $f_name . " " . $l_name
                // Fetch the user's name,
            ];
        });
        // Get the current backup schedule
        $currentSchedule = BackupSchedule::limit(1)->first();
        return view('db_backup.index', compact(['backups', 'currentSchedule']));
    }


    // public function backup()
    // {
    //     // Fetch the tables from the database
    //     $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

    //     // List of tables to be ignored
    //     $ignoreTables = ['article_groups', 'lead_statuses', 'leads', 'restores', 'migrations', 'failed_jobs', 'backup_schedules', 'backups'];

    //     // Get foreign key constraints
    //     $foreignKeys = DB::select(
    //         '
    //     SELECT
    //         TABLE_NAME AS child_table,
    //         REFERENCED_TABLE_NAME AS parent_table
    //     FROM
    //         INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    //     WHERE
    //         REFERENCED_TABLE_NAME IS NOT NULL
    //         AND TABLE_SCHEMA = ?',
    //         [config('database.connections.mysql.database')]
    //     );

    //     // Build table relationships
    //     $relationships = [];
    //     foreach ($foreignKeys as $fk) {
    //         $relationships[$fk->child_table][] = $fk->parent_table;
    //     }

    //     // Determine the order of tables (parent tables first)
    //     $sortedTables = $this->sortTablesByDependencies($tables, $relationships);

    //     // Retrieve the last ID from the backups table
    //     $lastBackupId = DB::table('backups')->max('id') + 1; // If there's no backup yet, start from 1
    //     // Format the date and time as per your requirements
    //     $date = date('d-m-Y_h_i_A');
    //     // Generate the SQL dump filename
    //     $fileName = 'DB_Backup_' . $lastBackupId . '_' . $date . '.sql';
    //     // Generate the ZIP filename
    //     $zipFileName = 'DB_Backup_' . $lastBackupId . '_' . $date . '.zip';

    //     $sqlDump = '';

    //     foreach ($sortedTables as $table) {
    //         // Check if the table is in the ignore list
    //         if (in_array($table, $ignoreTables)) {
    //             continue; // Skip this table
    //         }

    //         $rows = DB::table($table)->get();

    //         foreach ($rows as $row) {
    //             // Escape column names by wrapping them in backticks
    //             $columns = implode(', ', array_map(function ($column) {
    //                 return "`" . addslashes($column) . "`";
    //             }, array_keys((array)$row)));

    //             // Escape values
    //             $values = implode(', ', array_map(function ($value) {
    //                 return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
    //             }, array_values((array)$row)));

    //             // Add to SQL dump
    //             $sqlDump .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n";
    //         }
    //     }

    //     // Path to save the SQL dump
    //     $sqlFilePath = public_path("uploads/backups/{$fileName}");

    //     // Save the SQL dump to a file
    //     file_put_contents($sqlFilePath, $sqlDump);

    //     // Path to save the ZIP file
    //     $zipFilePath = public_path("uploads/backups/{$zipFileName}");

    //     // Create a new ZIP file
    //     $zip = new ZipArchive;
    //     if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
    //         $zip->addFile($sqlFilePath, $fileName);
    //         $zip->close();
    //     } else {
    //         return response()->json(['error' => 'Failed to create ZIP file.'], 500);
    //     }

    //     // Get the authenticated user
    //     if (Auth::user()) {
    //         $user_id = Auth::user()->id;
    //     }

    //     // Save the ZIP file's name (not the SQL file name) and user ID to the Backup model
    //     Backup::create([
    //         'name' => basename($zipFilePath), // store the ZIP file's name
    //         'user_id' => $user_id ?? null
    //     ]);

    //     // Delete the SQL file after adding it to the ZIP
    //     unlink($sqlFilePath);

    //     // Log activity
    //     activity()->causedBy(getLoggedInUser())
    //         ->useLog('Database Backup created.')
    //         ->log('Database Backup Created.');
    //     Flash::success("Created Successfully");

    //     return $this->sendResponse($sqlFilePath, __('messages.success.success'));
    // }


    public function backup()
    {
        // Fetch the tables from the database
        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        // List of tables to be ignored
        $ignoreTables = ['article_groups', 'lead_statuses', 'leads', 'restores', 'migrations', 'failed_jobs', 'backup_schedules', 'backups'];

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
        $sortedTables = $this->sortTablesByDependencies($tables, $relationships);

        // Retrieve the last ID from the backups table
        $lastBackupId = DB::table('backups')->max('id') + 1; // If there's no backup yet, start from 1
        // Format the date and time as per your requirements
        $date = date('d-m-Y_h_i_A');
        // Generate the SQL dump filename
        $fileName = 'DB_Backup_' . $lastBackupId . '_' . $date . '.sql';
        // Generate the ZIP filename
        $zipFileName = 'DB_Backup_' . $lastBackupId . '_' . $date . '.zip';

        $sqlDump = '';

        foreach ($sortedTables as $table) {
            // Check if the table is in the ignore list
            if (in_array($table, $ignoreTables)) {
                continue; // Skip this table
            }

            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                // Escape column names by wrapping them in backticks
                $columns = implode(', ', array_map(function ($column) {
                    return "`" . addslashes($column) . "`";
                }, array_keys((array)$row)));

                // Escape values
                $values = implode(', ', array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, array_values((array)$row)));

                // Add to SQL dump
                $sqlDump .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n";
            }
        }

        // Path to save the SQL dump
        $sqlFilePath = public_path("uploads/backups/{$fileName}");

        // Save the SQL dump to a file
        file_put_contents($sqlFilePath, $sqlDump);

        // Path to save the ZIP file
        $zipFilePath = public_path("uploads/backups/{$zipFileName}");

        // Create a new ZIP file
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($sqlFilePath, $fileName);
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create ZIP file.'], 500);
        }

        // Get the authenticated user
        if (Auth::user()) {
            $user_id = Auth::user()->id;
        }

        // Save the ZIP file's name (not the SQL file name) and user ID to the Backup model
        Backup::create([
            'name' => basename($zipFilePath), // store the ZIP file's name
            'user_id' => $user_id ?? null
        ]);

        // Delete the SQL file after adding it to the ZIP
        unlink($sqlFilePath);

        // Fetch company email from settings
        $settings = $this->settingRepository->getSyncList('company'); // pass correct groupName if needed
        $companyEmail = $settings['email'] ?? null;

        if ($companyEmail) {
            try {
                $downloadUrl = url("uploads/backups/" . basename($zipFilePath));
                Mail::to($companyEmail)->send(new BackupCreatedMail($downloadUrl, $zipFilePath));
            } catch (\Exception $e) {
                \Log::error("Backup email failed: " . $e->getMessage());
            }
        }


        // Log activity
        activity()->causedBy(getLoggedInUser())
            ->useLog('Database Backup created.')
            ->log('Database Backup Created.');
        Flash::success("Created Successfully");

        return $this->sendResponse($sqlFilePath, __('messages.success.success'));
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

    public function delete(Backup $backup)
    {
        // Get the file name from the backup record
        $fileName = $backup->name;
        $filePath = public_path('uploads/backups/' . $fileName);

        // Check if the file exists and delete it
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete the backup record from the database
        $backup->delete();

        Flash::success("Deleted Successfully");
        // Return success response
        return response()->json(['success' => true, 'message' => 'Backup deleted successfully.']);
    }



    public function download($file)
    {
        // Define the correct file path
        $filePath = public_path('uploads/backups/' . $file);

        // Check if the file exists
        if (File::exists($filePath)) {
            return Response::download($filePath);
        }

        // Handle case where file does not exist
        return redirect()->back()->with('error', 'File not found.');
    }


    public function setBackupSchedule(Request $request)
    {

        $frequency = $request->input('backup_frequency');
        // Update or insert the backup schedule
        BackupSchedule::updateOrCreate(
            ['id' => 1], // Assuming there's only one backup schedule (you can modify this if needed)
            [
                'schedule_name' => $this->getScheduleName($frequency),
                'frequency' => $frequency,
                'time' => $request['time'],
                'day' => $request['day']
            ]
        );

        return redirect()->back()->with('success', 'Backup schedule updated successfully!');
    }

    // Get the human-readable schedule name based on frequency
    protected function getScheduleName($frequency)
    {
        switch ($frequency) {
            case 1:
                return 'Every';
            case 2:
                return 'Daily';
            case 3:
                return 'Weekly';
            case 4:
                return 'Monthly';
            default:
                return 'Unknown';
        }
    }
    public function createBackup()
    {
        $lastBackup = BackupSchedule::first();
        if ($lastBackup) {
            $frequency = $lastBackup->frequency; // 1 for daily, 2 for weekly, etc.


            $updated_at = $lastBackup->last_backup_at; // Last backup time
            if (is_null($updated_at)) {
                // Set to the same date but in the previous year
                $updated_at = Carbon::now()->subYear()->startOfYear();
            }

            return $this->shouldRunBackup($lastBackup->time, $lastBackup->day, $frequency, $updated_at);
        }
        return $this->sendError('Not Initialized');
    }

    // public function shouldRunBackup($time, $day, $frequency, $updated_at)
    // {


    //     $backupTime = $time;
    //     $time = Carbon::createFromFormat('H:i:s', $time)->format('H:i');
    //     $currentTime = now()->format('H:i'); // Get current time in 'H:i:s' format

    //     $lastBackup = Carbon::parse($updated_at); // Parse last backup date
    //     $status = false;
    //     switch ($frequency) {
    //         case 2: // Daily

    //             if ($currentTime === $time && !$lastBackup->isToday()) {
    //                 $this->backup();
    //                 $status = true;
    //             }
    //             break;

    //         case 3: // Weekly (e.g., every Monday)
    //             $currentDay = now()->format('l'); // Get the current day of the week (e.g., 'Monday', 'Saturday')
    //             if ($currentDay === $day && $currentTime === $time && !$lastBackup->isCurrentWeek()) {
    //                 $this->backup();
    //                 $status = true;
    //             }
    //             break;

    //         case 4: // Monthly (e.g., 1st of every month)
    //             if (now()->isSameDay(now()->endOfMonth()) && $currentTime === $time && !$lastBackup->isCurrentMonth()) {
    //                 $this->backup();
    //                 $status = true;
    //             }
    //             break;

    //         case 5: // Yearly (e.g., on a specific day and time once a year)
    //             $scheduledDate = Carbon::create(now()->year, 1, 1)->format('m-d'); // Example: January 1st
    //             if ($currentTime === $time && now()->format('m-d') === $scheduledDate && !$lastBackup->isCurrentYear()) {
    //                 $this->backup();
    //                 $status = true;
    //             }
    //             break;

    //         default:
    //             // Handle invalid frequencies or other custom logic
    //             break;
    //     }

    //     if ($status) {
    //         // Update or create the backup schedule
    //         BackupSchedule::updateOrCreate(
    //             ['id' => 1], // Assuming there's only one backup schedule (you can modify this if needed)
    //             [
    //                 'schedule_name' => $this->getScheduleName($frequency),
    //                 'frequency' => $frequency,
    //                 'time' => $time, // Make sure this is the correct time format
    //                 'last_backup_at' => Carbon::now()
    //             ]
    //         );
    //     }

    //     // Calculate the remaining time for the next backup
    //     $remainingTime = $this->calculateRemainingTime($backupTime, $frequency);

    //     return [
    //         'status' => $status,
    //         'next_backup_time' => $remainingTime,
    //         'time' => $currentTime . "|" . $time
    //     ];
    // }

    public function shouldRunBackup($time, $day, $frequency, $updated_at)
    {
        \Log::info('Backup check started', [
            'input_time' => $time,
            'frequency' => $frequency,
            'last_backup' => $updated_at
        ]);

        // Parse the time correctly
        try {
            $backupTime = Carbon::createFromFormat('H:i:s', $time);
        } catch (\Exception $e) {
            $backupTime = Carbon::createFromFormat('H:i', $time);
        }

        $currentTime = now();
        $lastBackup = $updated_at ? Carbon::parse($updated_at) : Carbon::now()->subYear();

        // EXACT MINUTE MATCHING - Remove the flexible window!
        $timeMatches = $currentTime->format('H:i') === $backupTime->format('H:i');

        \Log::info('Time comparison', [
            'current_time' => $currentTime->format('H:i:s'),
            'backup_time' => $backupTime->format('H:i:s'),
            'time_matches' => $timeMatches,
            'current_H:i' => $currentTime->format('H:i'),
            'backup_H:i' => $backupTime->format('H:i')
        ]);

        $status = false;

        switch ($frequency) {
            case 2: // Daily
                \Log::info('Daily check', [
                    'time_matches' => $timeMatches,
                    'last_backup_today' => $lastBackup->isToday(),
                    'should_run' => $timeMatches && !$lastBackup->isToday()
                ]);

                if ($timeMatches && !$lastBackup->isToday()) {
                    \Log::info('DAILY BACKUP TRIGGERED');
                    $this->backup();
                    $status = true;
                }
                break;

            case 3: // Weekly
                $currentDay = now()->format('l');
                \Log::info('Weekly check', [
                    'current_day' => $currentDay,
                    'scheduled_day' => $day,
                    'day_matches' => $currentDay === $day,
                    'last_backup_this_week' => $lastBackup->isCurrentWeek()
                ]);

                if ($timeMatches && $currentDay === $day && !$lastBackup->isCurrentWeek()) {
                    $this->backup();
                    $status = true;
                }
                break;

            case 4: // Monthly
                \Log::info('Monthly check', [
                    'is_first_day' => now()->day === 1,
                    'last_backup_this_month' => $lastBackup->isCurrentMonth()
                ]);

                if ($timeMatches && now()->day === 1 && !$lastBackup->isCurrentMonth()) {
                    $this->backup();
                    $status = true;
                }
                break;

            default:
                \Log::info('Unknown frequency', ['frequency' => $frequency]);
                break;
        }

        if ($status) {
            BackupSchedule::updateOrCreate(
                ['id' => 1],
                [
                    'schedule_name' => $this->getScheduleName($frequency),
                    'frequency' => $frequency,
                    'time' => $time,
                    'last_backup_at' => Carbon::now()
                ]
            );
        }

        $remainingTime = $this->calculateRemainingTime($time, $frequency);

        \Log::info('Backup check completed', [
            'status' => $status,
            'next_backup' => $remainingTime
        ]);

        return [
            'status' => $status,
            'next_backup_time' => $remainingTime,
            'time' => $currentTime->format('H:i') . "|" . $backupTime->format('H:i')
        ];
    }

    public function calculateRemainingTime($time, $frequency)
    {
        $now = Carbon::now();
        $nextBackup = Carbon::createFromFormat('H:i:s', $time);

        switch ($frequency) {
            case 2: // Daily
                if ($now->greaterThanOrEqualTo($nextBackup)) {
                    $nextBackup->addDay();
                }
                break;

            case 3: // Weekly
                $nextBackup->next('saturday');
                if ($now->greaterThanOrEqualTo($nextBackup)) {
                    $nextBackup->addWeek();
                }
                break;

            case 4: // Monthly
                $nextBackup->day(1);
                if ($now->greaterThanOrEqualTo($nextBackup)) {
                    $nextBackup->addMonth();
                }
                break;

            case 5: // Yearly
                $nextBackup->month(1)->day(1);
                if ($now->greaterThanOrEqualTo($nextBackup)) {
                    $nextBackup->addYear();
                }
                break;

            default:
                // Handle invalid frequencies or other custom logic
                break;
        }

        $remainingTime = $now->diff($nextBackup);

        return $remainingTime->format('%a days, %H hours, %I minutes, %S seconds');
    }
}
