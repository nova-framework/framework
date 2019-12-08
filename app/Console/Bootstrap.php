<?php

/**
 * Console - register the Forge Commands and Schedule
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
Forge::command('app:install', function ()
{
    $this->call('migrate', array('--seed' => true));

    //
    $this->call('package:migrate');
    $this->call('package:seed');

})->describe('Run all database migrations and seed it with records');


Forge::command('app:refresh', function ()
{
    $this->call('migrate:refresh', array('--seed' => true));

    //
    $this->call('package:migrate:reset');
    $this->call('package:migrate');
    $this->call('package:seed');

})->describe('Reset and re-run all database migrations, then seed it with records');


Forge::command('queue:monitor', function ()
{
    $path = storage_path('queue.pid');

    if (is_readable($path) && ! empty($pid = file_get_contents($path))) {
        $command = sprintf("ps -p %d --no-heading | awk '{print $1}'", (int) $pid);

        if (! empty($result = exec($command)) && ($result == $pid)) {
            return;
        }
    }

    $command = sprintf('%s %s queue:work --daemon --tries=3 >/dev/null & echo $!', PHP_BINARY, base_path('forge'));

    // Store the Queue Worker PID for later checking.
    file_put_contents($path, exec($command));

})->describe('Monitor the Queue Worker execution');


/**
 * Schedule the Queue execution.
 */
//Schedule::command('queue:monitor')->everyMinute();

// To prevent long running cache expiries it is advised to match your cache cache expiry time with your task frequency.
Schedule::command('queue:batch --time-limit=175 --job-limit=300 --tries=3')->everyMinute()->withoutOverlapping(5)->runInBackground();

//Schedule::command('queue:work --daemon --tries=3')->everyMinute()->withoutOverlapping()->runInBackground();


/**
 * Schedule the flushing of expired password reminders.
 */
Schedule::command('auth:clear-reminders')->daily()->runInBackground();

/**
 * Schedule the Database Backup.
 */
Schedule::command('db:backup')->dailyAt('4:30')->runInBackground();
