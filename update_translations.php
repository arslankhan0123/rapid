<?php

$file = __DIR__ . '/lang/en/messages.php';
$content = file_get_contents($file);

$insert = "
    'job_sources' => [
        'states' => 'Job Sources',
        'name' => 'Source Name',
        'description' => 'Source Description',
        'add' => 'Add Job Source',
        'edit' => 'Edit Job Source',
        'view' => 'View Job Source',
        'delete' => 'Delete Job Source',
        'saved' => 'Job Source Saved Successfully.',
        'list' => 'Job Sources List',
    ],
";

// Insert right before the end of the array, or replace locations
$content = preg_replace("/(\s*)'locations' => \[.*?\],/s", "$0" . $insert, $content, 1);
file_put_contents($file, $content);

echo "Translations added.\n";
