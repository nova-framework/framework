<?php

/**
 * Console - register the Forge Commands and Schedule
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 *
 */


/**
 * Schedule the Mailer Spool queue flushing.
 */
Schedule::command('mailer:spool:send')->everyMinute();
