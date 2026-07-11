<?php

$src = __DIR__ . '/resources/views/states';
$dest = __DIR__ . '/resources/views/job_sources';

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
        
        // Custom replacements for JobSource
        $content = str_replace('route(\'states.', 'route(\'job-sources.', $content);
        $content = str_replace('messages.states', 'messages.job_sources', $content);
        $content = str_replace('states.table', 'job_sources.table', $content);
        $content = str_replace('states.index', 'job_sources.index', $content);
        
        $content = str_replace('State', 'Job Source', $content);
        $content = str_replace('States', 'Job Sources', $content);
        
        // Variable replacements
        $content = str_replace('$state', '$jobSource', $content);
        
        // Field replacements
        $content = str_replace('country_id', 'description', $content);
        
        // Specific adjustments for JobSource description
        // In the views, country_id was a select. We will need to change description to a textarea or input.
        
        file_put_contents($destPath, $content);
        echo "Created: $destPath\n";
    }
}
