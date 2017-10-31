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
Forge::command('attachment:clear-stalled', function ()
{
    $attachments = Attachment::where('attachable_id', 0)
        ->where('created_at', '<', Carbon::parse('-3 hours'))
        ->get();

    foreach ($attachments as $attachment) {
        $attachment->delete();
    };

    $this->info('The stalled Attachments was cleared!');

})->describe('Clear all Attachments still not associated to an Attachable after 3 hours');


/**
 * Schedule the Mailer Spool queue flushing.
 */
Schedule::command('mailer:spool:send')->everyMinute();

/**
 * Schedule the stalled Attachments clearing.
 */
//Schedule::command('attachment:clear-stalled')->hourly();

/**
 * Schedule the flushing of expired password reminders.
 */
Schedule::command('auth:clear-reminders')->daily();

/**
 * Schedule the Database Backup.
 */
Schedule::command('db:backup')->dailyAt('4:30');
