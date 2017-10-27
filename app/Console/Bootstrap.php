<?php

/**
 * Bootstrap - register the Forge Commands and Schedule
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
Forge::command('hello', function ()
{
    $this->comment('Hello, World!');

})->describe('Display a Hello World message');


/**
 * Schedule the Mailer Spool queue flushing.
 */
Schedule::command('mailer:spool:send')->everyMinute();

/**
 * Schedule the flushing of expired password reminders.
 */
Schedule::command('auth:clear-reminders')->daily();

/**
 * Schedule the Database Backup.
 */
Schedule::command('db:backup')->dailyAt('4:30');
