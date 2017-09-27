<?php

namespace App\Console;

use Nova\Console\Scheduling\Schedule;
use Nova\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * The Forge commands provided by the application.
     *
     * @var array
     */
    protected $commands = array();


    /**
     * Define the application's command schedule.
     *
     * @param  \Nova\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         // Schedule the flushing of expired password reminders.
        $schedule->command('auth:clear-reminders')->daily();

        // Schedule the Database Backup.
        $schedule->command('db:backup')->dailyAt('4:30');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require app_path('Console.php');
    }
}
