#!/usr/bin/env php
<?php

define("DS", DIRECTORY_SEPARATOR);

define("BASEPATH", dirname(dirname(__FILE__)) .DS);

$workPaths = array(
    'app',
    'plugins',
    'scripts',
    'system',
    'webroot',
);

//
function starts_with($haystack, $needle) {
    return (($needle === '') || (strpos($haystack, $needle) === 0));
}

//
function searchPhpFiles($basePath) {
    $result = array();

    $fp = opendir($basePath);

    while($file = readdir($fp)) {
        if (preg_match("#^\.+$#", $file) === 1) {
            // Ignore the symbolic links
            continue;
        }

        $path = $basePath .DS .$file;

        if(is_dir($path)) {
            $result = array_merge($result, searchPhpFiles($path));

            continue;
        }

        if(preg_match("#^.+\.php$#", $path) === 1) {
            $result[] = $path;
        }
    }

    return array_unique($result);
}


foreach($workPaths as $workPath) {
    $basePath = BASEPATH .$workPath;

    if(! is_dir($basePath)) {
        continue;
    }

    $files = searchPhpFiles($basePath);

    foreach($files as $path) {
        echo $path . "\n";

        if (str_replace(BASEPATH, '', $path) == 'scripts/patch.php') {
            // Ignore this patch script.
            continue;
        }

        //$content = str_replace(array("    ", "\r"), array("\t", ""), file_get_contents($path));
        $content = str_replace(array("\t", "\r"), array("    ", ""), file_get_contents($path));

        file_put_contents($path, $content);
    }
}
