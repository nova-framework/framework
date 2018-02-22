<?php

/**
 * Console - register the Forge Commands and Schedule
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 *
 */


/**
 * Resolve the Forge commands from application.
 */
Forge::resolveCommands(array(
    //'App\Console\Commands\MagicWand',
));


/**
 * Add the Closure based commands.
 */
Forge::command('queue:monitor', function ()
{
    if (! file_exists($pidFile = storage_path('queue.pid'))) {
        $runCommand = true;
    } else {
        $pid = file_get_contents($pidFile);

        $runCommand = empty(
            exec("ps -p $pid --no-heading | awk '{print $1}'")
        );
    }

    if ($runCommand) {
        $command = PHP_BINARY .' ' .base_path('forge') .' queue:work --daemon --tries=3 >/dev/null & echo $!';

        // Execute the command and retrieve the PID.
        $pid = exec($command);

        file_put_contents($pidFile, $pid);
    }

})->describe('Monitor the Queue Worker execution');


/**
 * Schedule the Queue execution.
 */
//Schedule::command('queue:monitor')->everyMinute()->runInBackground();

// To prevent long running cache expiries it is advised to match your cache cache expiry time with your task frequency.
Schedule::command('queue:batch --tries=3 --time-limit=55 --job-limit=100')->everyMinute()->withoutOverlapping(1)->runInBackground();

//Schedule::command('queue:work --daemon')->everyFiveMinutes()->withoutOverlapping(5)->runInBackground();


/**
 * Schedule the flushing of expired password reminders.
 */
Schedule::command('auth:clear-reminders')->daily();

/**
 * Schedule the Database Backup.
 */
Schedule::command('db:backup')->dailyAt('4:30');
