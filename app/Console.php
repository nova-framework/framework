<?php

/**
 * Console - register the Forge Commands and Schedule
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 *
 */

use App\Modules\Attachments\Models\Attachment;

use Carbon\Carbon;

/**
 * Resolve the Forge commands from application.
 */
Forge::resolveCommands(array(
    //'App\Console\Commands\MagicWand',
));

/**
 * Add the Closure based commands.
 */
Forge::command('attachments:clear', function ()
{
    //Attachment::where('attachable_id', 0)->where('created_at', '<', Carbon::parse('-3 hours'))->delete();

    $this->comment('Attachments cleared!');

})->describe('Delete all Attachments which are still not attached after 3 hours');


/**
 * Schedule the Mailer Spool queue flushing.
 */
Schedule::command('mailer:spool:send')->everyMinute();

/**
 * Schedule the stalled Attachments clearing.
 */
Schedule::command('attachments:clear')->hourly();

/**
 * Schedule the flushing of expired password reminders.
 */
Schedule::command('auth:clear-reminders')->daily();

/**
 * Schedule the Database Backup.
 */
Schedule::command('db:backup')->dailyAt('4:30');
