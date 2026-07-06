<?php
/**
 * Safe script to rename corrupted branch_docs files
 * to the correct names stored in the database.
 *
 * Place this file in your Laravel project root and run once.
 */

// ---------------- CONFIG ----------------
$folderPath = __DIR__ . '/public/uploads/public/branch_docs/'; // folder with files

// Database connection (from your .env)
$dbHost = '127.0.0.1';
$dbName = 'rapidsma_pms_main';
$dbUser = 'rapidsma_pms_main';
$dbPass = 'ALMD?BUqD!?H';
$table = 'branch_docs';
$column = 'file'; // column storing file names
$idColumn = 'id'; // primary key column
// ---------------------------------------

// Ensure UTF-8 support
ini_set('default_charset', 'UTF-8');
setlocale(LC_ALL, 'en_US.UTF-8');

// Connect to DB
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Starting file rename...</h2>";

    // Fetch all files from database
    $stmt = $pdo->query("SELECT $idColumn, $column FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $correctFile = $row[$column]; // Arabic name from DB
        $id = $row[$idColumn];

        // Search file in folder by ID (partial match)
        $allFiles = glob($folderPath . "*"); // get all files in folder
        foreach ($allFiles as $filePath) {
            $baseName = basename($filePath);

            // If filename contains the ID
            if (strpos($baseName, $id) !== false) {
                $newPath = $folderPath . $correctFile;

                if ($filePath !== $newPath) {
                    if (!file_exists($newPath)) {
                        if (rename($filePath, $newPath)) {
                            echo "✅ Renamed '$baseName' → '$correctFile'<br>";
                        } else {
                            echo "❌ Failed to rename '$baseName'<br>";
                        }
                    } else {
                        echo "⚠️ '$correctFile' already exists, skipping.<br>";
                    }
                }
                break; // move to next row
            }
        }
    }

    echo "<h3>All done!</h3>";

} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage();
}
