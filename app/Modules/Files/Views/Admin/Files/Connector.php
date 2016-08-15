<?php

$elFinderPath = APPDIR .'Modules/Files/Lib/elFinder/';

require $elFinderPath .'elFinderConnector.class.php';
require $elFinderPath .'elFinder.class.php';
require $elFinderPath .'elFinderVolumeDriver.class.php';
require $elFinderPath .'elFinderVolumeLocalFileSystem.class.php';

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/

function access($attr, $path, $data, $volume)
{
    return (strpos(basename($path), '.') === 0)       // if file/folder begins with '.' (dot)
            ? ! ($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            :  null;                                  // else elFinder decide it itself
}

// Retrieve the options.
$options = Config::get('elfinder');

// Create a elFinder instance.
$elfinder = new elFinder($options);

// Create a elFinder Connector instance.
$connector = new elFinderConnector($elfinder, true);

// Run the elFinder Connector.
$connector->run();
