<?php

$file = __DIR__ . '/lang/en/messages.php';
$content = file_get_contents($file);

$insert = "
    'job_recruiters' => [
        'states' => 'Job Recruiters',
        'name' => 'Recruiter Name',
        'add' => 'Add Job Recruiter',
        'edit' => 'Edit Job Recruiter',
        'view' => 'View Job Recruiter',
        'delete' => 'Delete Job Recruiter',
        'saved' => 'Job Recruiter Saved Successfully.',
        'list' => 'Job Recruiters List',
    ],
";

if (strpos($content, "'job_recruiters' =>") === false) {
    $content = preg_replace("/(\s*)'job_sources' => \[.*?\],/s", "$0" . $insert, $content, 1);
    file_put_contents($file, $content);
    echo "Translations added.\n";
} else {
    echo "Already exists.\n";
}
