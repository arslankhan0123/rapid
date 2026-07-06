<?php
/**
 * Robust script to rename branch_docs files to correct Arabic names
 */

$folderPath = __DIR__ . '/public/uploads/public/branch_docs/';

// DB config
$pdo = new PDO('mysql:host=127.0.0.1;dbname=rapidsma_pms_main;charset=utf8mb4', 
    'rapidsma_pms_main', 'ALMD?BUqD!?H', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
]);

// Fetch all files from database
$rows = $pdo->query("SELECT id, file FROM branch_docs")->fetchAll(PDO::FETCH_ASSOC);

// Build mapping: id => correct file name
$map = [];
foreach ($rows as $row) {
    $map[$row['id']] = $row['file'];
}

// Scan folder
$files = scandir($folderPath);

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;

    $filePath = $folderPath . $file;

    // Extract ID prefix from filename (digits before underscore)
    if (preg_match('/^(\d+)_/', $file, $matches)) {
        $id = $matches[1];
        if (isset($map[$id])) {
            $newName = $map[$id];
            $newPath = $folderPath . $newName;

            if ($filePath !== $newPath) {
                if (!file_exists($newPath)) {
                    if (rename($filePath, $newPath)) {
                        echo "✅ Renamed '$file' → '$newName'<br>";
                    } else {
                        echo "❌ Failed: '$file'<br>";
                    }
                } else {
                    echo "⚠️ Already exists: '$newName'<br>";
                }
            }
        } else {
            echo "⚠️ ID $id not found in DB: '$file'<br>";
        }
    } else {
        echo "⚠️ No ID prefix: '$file'<br>";
    }
}

echo "<h3>Done renaming files.</h3>";
