<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/upgrade-to-v4-0-0', function () {
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path' => 'database/migrations/2021_09_03_000000_add_uuid_to_failed_jobs_table.php',
        ]);
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path' => 'database/migrations/2021_09_11_113710_add_conversions_disk_column_in_media_table.php',
        ]);
});

Route::get('/upgrade-to-v4-1-0', function () {
    try {
        Artisan::call('migrate',
            [
                '--force' => true,
                '--path' => 'database/migrations/2022_04_27_062115_add_is_admin_field_in_users_table.php',
            ]);

        Artisan::call('db:seed', ['--class' => 'SetIsAdminSeeder', '--force' => true]);

        return 'You are successfully migrate and seeded to v4.1.0';
    } catch (Exception $exception) {
        return $exception->getMessage();
    }
});

Route::get('/upgrade-to-v4-1-1', function () {
    try {
        Artisan::call('migrate',
            [
                '--force' => true,
                '--path' => 'database/migrations/2022_05_24_073300_change_properties_field_type_in_activity_log_table.php',
            ]);

        return 'You are migrate successfully';
    } catch (Exception $exception) {
        return $exception->getMessage();
    }
});

Route::get('/upgrade-to-v4-1-3', function () {
    try {
        Artisan::call('migrate',
            [
                '--force' => true,
                '--path' => 'database/migrations/2022_07_27_055736_add_hsn_tax_field_in_invoices_and_proposals_and_estimates.php',
            ]);

        return 'You are migrate successfully';
    } catch (Exception $exception) {
        return $exception->getMessage();
    }
});

Route::get('upgrade/database', function () {
    if (config('app.upgrade_mode')) {
        Artisan::call('migrate', ['--force' => true]);
    }
});
