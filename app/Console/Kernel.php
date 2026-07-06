<?php

namespace App\Console;

use App\Console\Commands\AnnouncementReminder;
use App\Console\Commands\ReminderMainSend;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AnnouncementReminder::class,
        ReminderMainSend::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(AnnouncementReminder::class)->hourly();
        $schedule->command(ReminderMainSend::class)->everyMinute();
        $schedule->command('backup:check')->everyMinute();
        $schedule->call(function () {
            // Mark users as offline if no activity for 30 minutes
            \App\Models\LoggedUser::where('last_activity_at', '<', now()->subMinutes(30))
                ->update(['status' => 'offline']);

            // Cleanup old records (older than 7 days)
            \App\Models\LoggedUser::where('created_at', '<', now()->subDays(7))->delete();
        })->everyFiveMinutes();

        $schedule->command('employees:process')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
