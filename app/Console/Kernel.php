<?php

namespace App\Console;

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
        Commands\MakeJsxComponent::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();


        // Send appointment reminders every 5 minutes
        $schedule->command('appointments:send-reminders')->everyFiveMinutes();

        // Also run at specific times for different reminder types
        $schedule->command('appointments:send-reminders')->dailyAt('09:00');
        $schedule->command('appointments:send-reminders')->dailyAt('15:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
