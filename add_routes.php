<?php

$file = __DIR__ . '/routes/web.php';
$content = file_get_contents($file);

$useImport = "use App\Http\Controllers\JobRecruiterController;\n";
if (strpos($content, $useImport) === false) {
    $content = preg_replace("/use App\\\\Http\\\\Controllers\\\\JobSourceController;/", "use App\Http\Controllers\JobSourceController;\n" . $useImport, $content);
}

$routes = "
    // Job Recruiters routes
    Route::group(['middleware' => ['permission:view_job_recruiters|create_job_recruiters|update_job_recruiters|delete_job_recruiters']], function () {
        Route::get('job-recruiters', [JobRecruiterController::class, 'index'])->name('job-recruiters.index');
        Route::get('job-recruiters/create', [JobRecruiterController::class, 'create'])->middleware('permission:create_job_recruiters')->name('job-recruiters.create');
        Route::post('job-recruiters', [JobRecruiterController::class, 'store'])->middleware('permission:create_job_recruiters')->name('job-recruiters.store');
        Route::get('job-recruiters/{jobRecruiter}/view', [JobRecruiterController::class, 'view'])->middleware('permission:view_job_recruiters')->name('job-recruiters.view');
        Route::get('job-recruiters/{jobRecruiter}/edit', [JobRecruiterController::class, 'edit'])->middleware('permission:update_job_recruiters')->name('job-recruiters.edit');
        Route::put('job-recruiters/{jobRecruiter}', [JobRecruiterController::class, 'update'])->middleware('permission:update_job_recruiters')->name('job-recruiters.update');
        Route::delete('job-recruiters/{jobRecruiter}', [JobRecruiterController::class, 'destroy'])->middleware('permission:delete_job_recruiters')->name('job-recruiters.destroy');
    });
";

if (strpos($content, 'job-recruiters') === false) {
    // find job-sources group and append
    $content = preg_replace("/Route::delete\('job-sources\/\{jobSource\}', \[JobSourceController::class, 'destroy'\]\)->middleware\('permission:delete_job_sources'\)->name\('job-sources\.destroy'\);\n    \}\);/", "$0\n" . $routes, $content);
}

file_put_contents($file, $content);

echo "Routes added.\n";
