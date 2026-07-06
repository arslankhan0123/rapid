<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BackupController;

class DatabaseBackup extends Command
{
    protected $signature = 'backup:run';

    protected $description = 'Run the database backup.';

    public function handle()
    {
        // Call the backup method from BackupController
        $backupController = new BackupController();
        $backupController->backup();

        $this->info('Database backup completed successfully.');
    }
}
