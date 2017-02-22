<?php

$basePath = str_replace('/', DS, APPDIR .'Modules/Files/Lib/elFinder/');

require $basePath .'elFinderConnector.class.php';
require $basePath .'elFinder.class.php';
require $basePath .'elFinderVolumeDriver.class.php';
require $basePath .'elFinderVolumeLocalFileSystem.class.php';

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

// Retrieve the elFinder options.
$options = Config::get('files::elFinder');

// Create a elFinder instance.
$elFinder = new elFinder($options);

// Create a elFinder Connector instance.
$connector = new elFinderConnector($elFinder, true);

// Run the elFinder Connector.
$connector->run();
