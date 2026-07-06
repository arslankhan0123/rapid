<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\BackupController;
use App\Repositories\SettingRepository;

class RunBackupCheck extends Command
{
    protected $signature = 'backup:check';
    protected $description = 'Check if backup should run based on schedule';

    protected $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        parent::__construct();
        $this->settingRepository = $settingRepository;
    }

    public function handle()
    {
        // Create controller instance with the required dependency
        $backupController = new BackupController($this->settingRepository);
        $result = $backupController->createBackup();

        if (isset($result['status']) && $result['status']) {
            $this->info('Backup was executed successfully.');
        } else {
            $nextBackup = $result['next_backup_time'] ?? 'unknown';
            $this->info('Backup not required. Next backup: ' . $nextBackup);
        }
    }
}
