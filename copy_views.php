<?php

$src = __DIR__ . '/resources/views/states';
$dest = __DIR__ . '/resources/views/locations';

if (!is_dir($dest)) {
    mkdir($dest, 0777, true);
}

$files = scandir($src);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }
    
    $srcPath = $src . '/' . $file;
    $destPath = $dest . '/' . $file;
    
    if (is_file($srcPath)) {
        $content = file_get_contents($srcPath);
        
        // Replace words
        $content = str_replace('states', 'locations', $content);
        $content = str_replace('States', 'Locations', $content);
        $content = str_replace('state', 'location', $content);
        $content = str_replace('State', 'Location', $content);
        
        // If there is an issue where messages.states should be messages.locations (I did states -> locations, so it's correct)
        
        file_put_contents($destPath, $content);
        echo "Created: $destPath\n";
    }
}
