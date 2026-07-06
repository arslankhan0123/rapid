<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BackupController;
use App\Repositories\SettingRepository;

class DatabaseBackup extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Run the database backup.';

    public function handle()
    {
        $this->info('Starting database backup...');

        try {
            // Create the SettingRepository instance FIRST
            $settingRepository = app(SettingRepository::class);

            // Then pass it to the controller
            $backupController = new BackupController($settingRepository);

            // Call the backup method
            $backupController->backup();

            $this->info('✅ Database backup completed successfully.');
        } catch (\Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            \Log::error('Backup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
