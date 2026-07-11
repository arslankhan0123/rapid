<?php

$src = __DIR__ . '/resources/views/job_sources';
$dest = __DIR__ . '/resources/views/job_recruiters';

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
        
        $content = str_replace('route(\'job-sources.', 'route(\'job-recruiters.', $content);
        $content = str_replace('messages.job_sources', 'messages.job_recruiters', $content);
        $content = str_replace('job_sources.table', 'job_recruiters.table', $content);
        $content = str_replace('job_sources.index', 'job_recruiters.index', $content);
        
        $content = str_replace('Job Source', 'Job Recruiter', $content);
        $content = str_replace('Job Sources', 'Job Recruiters', $content);
        
        $content = str_replace('$jobSource', '$jobRecruiter', $content);
        
        // Form name replacement if 'name' was used for recruiter_name
        $content = str_replace('\'name\'', '\'recruiter_name\'', $content);
        $content = str_replace('$jobRecruiter->name', '$jobRecruiter->recruiter_name', $content);
        $content = str_replace('row.name', 'row.recruiter_name', $content);
        
        file_put_contents($destPath, $content);
        echo "Created: $destPath\n";
    }
}
