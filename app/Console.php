<?php

/**
 * Register The Forge Commands and Schedule
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 *
 */


Schedule::call(function ()
{
    echo 'This is a sample command.' ."\n\n";

})->everyMinute();
