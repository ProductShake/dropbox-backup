<?php

namespace ProductShake\Backup\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class BackupConsoleProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);

                // Run backup daily at 01:00
                $schedule->command('backup:run')->dailyAt('01:00')
                    ->appendOutputTo(storage_path('logs/backup.log'));

                // Clean old backups daily at 02:00
                $schedule->command('backup:clean')->dailyAt('02:00')
                    ->appendOutputTo(storage_path('logs/backup-clean.log'));

                // Monitor backup health daily at 03:00
                $schedule->command('backup:monitor')->dailyAt('03:00')
                    ->appendOutputTo(storage_path('logs/backup-monitor.log'));
            });
        }
    }
}
