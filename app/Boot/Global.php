<?php

//--------------------------------------------------------------------------
// Application Error Logger
//--------------------------------------------------------------------------

Log::useFiles(storage_path() .'Logs ' .DS .'error.log');

//--------------------------------------------------------------------------
// Application Error Handler
//--------------------------------------------------------------------------

App::error(function(Exception $exception, $code)
{
    Log::error($exception);
});

//--------------------------------------------------------------------------
// Require The Events File
//--------------------------------------------------------------------------

require app_path() .'Events.php';

// Load the Events defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Events.php';

    if (is_readable($path)) require $path;
}

//--------------------------------------------------------------------------
// Require The Filters File
//--------------------------------------------------------------------------

require app_path() .'Filters.php';

// Load the Filters defined on Modules.
foreach ($modules as $module) {
    $path = app_path() .'Modules' .DS .$module .DS .'Filters.php';

    if (is_readable($path)) require $path;
}
