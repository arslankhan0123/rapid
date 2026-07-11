<?php

$file = __DIR__ . '/routes/web.php';
$content = file_get_contents($file);

$useImport = "use App\Http\Controllers\JobSourceController;\n";
if (strpos($content, $useImport) === false) {
    $content = preg_replace("/use App\\\\Http\\\\Controllers\\\\LocationController;/", "use App\Http\Controllers\LocationController;\n" . $useImport, $content);
}

$routes = "
        // Job Sources
        Route::get('job-sources', [JobSourceController::class, 'index'])->name('job-sources.index');
        Route::get('job-sources/create', [JobSourceController::class, 'create'])->middleware('permission:create_job_sources')->name('job-sources.create');
        Route::post('job-sources', [JobSourceController::class, 'store'])->middleware('permission:create_job_sources')->name('job-sources.store');
        Route::get('job-sources/{jobSource}/view', [JobSourceController::class, 'view'])->middleware('permission:view_job_sources')->name('job-sources.view');
        Route::get('job-sources/{jobSource}/edit', [JobSourceController::class, 'edit'])->middleware('permission:update_job_sources')->name('job-sources.edit');
        Route::put('job-sources/{jobSource}', [JobSourceController::class, 'update'])->middleware('permission:update_job_sources')->name('job-sources.update');
        Route::delete('job-sources/{jobSource}', [JobSourceController::class, 'destroy'])->middleware('permission:delete_job_sources')->name('job-sources.destroy');
";

if (strpos($content, 'job-sources') === false) {
    // find location routes and append
    $content = preg_replace("/Route::delete\('locations\/\{location\}', \[LocationController::class, 'destroy'\]\)->middleware\('permission:delete_locations'\)->name\('locations\.destroy'\);/", "$0\n" . $routes, $content);
}

file_put_contents($file, $content);

echo "Routes added.\n";
