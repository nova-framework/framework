#!/usr/bin/env php
<?php

define("DS", DIRECTORY_SEPARATOR);

define("BASEPATH", dirname(dirname(__FILE__)) .DS);

define("WEBROOT", BASEPATH.'public'.DS);

// Init the Composer autoloading.
require BASEPATH .'vendor/autoload.php';

//
use Smvc\Helpers\Inflector;


//
function mkdirs($dir, $mode = 0777, $recursive = true) {
  if( is_null($dir) || $dir === "" ) {
    return false;
  }

  if( is_dir($dir) || $dir === "/" ) {
    return true;
  }

  if( mkdirs(dirname($dir), $mode, $recursive) ) {
    return mkdir($dir, $mode);
  }

  return false;
}
//
function phpGrep($path) {
    $ret = array();

    $fp = opendir($path);

    while($f = readdir($fp)) {
        if( preg_match("#^\.+$#", $f) ) continue; // ignore symbolic links

        $file_full_path = $path.DS.$f;

        if(is_dir($file_full_path)) {
            $ret = array_unique(array_merge($ret, phpGrep($file_full_path)));
        }
        else {
            $ret[] = $file_full_path;
        }
    }

    return $ret;
}

function makeSymlink($path) {
    if(preg_match('#^assets/(.*)$#i', $path, $matches)) {
        $filePath = 'assets/'.$matches[1];
    }
    else if(preg_match('#^app/(templates|modules)/(.+)/assets/(.*)$#i', $path, $matches)) {
        // We need to classify the second match string (the Module/Template name).
        $module = Inflector::tableize($matches[2]);

        $filePath = $matches[1].'/'.$module.'/assets/'.$matches[3];
    }

    //
    $linkPath = WEBROOT.$filePath;

    $dirPath = WEBROOT.dirname($filePath);

    $targetPath = str_repeat('../', count(explode('/', dirname($path))) + 1).$path;

    // Check if our target is an already existing symlink or directory, with cleanup.
    if(is_link($linkPath)) {
        $target = readlink($linkPath);

        $target = str_replace('../', '', $target);

        if($target == $filePath) {
            return;
        }

        unlink($linkPath);
    }
    else if(is_dir($linkPath)) {
        recursiveRemoveDirs($linkPath);
    }

    mkdirs($dirPath);

    @unlink($linkPath);
    symlink($targetPath, $linkPath);
}

function recursiveRemoveDirs($dir)
{
    if(! is_dir($dir)) {
        return;
    }

    $files = array_diff(scandir($dir), array('..', '.'));

    foreach ($files as $file) {
        $filePath = $dir.'/'.$file;

        if( is_dir($filePath) ) {
            recursiveRemoveDirs($filePath);
        }
        else {
            unlink($filePath);
        }
    }

    rmdir($dir);
}

function removeInvalidSymlinks($path)
{
    $files = array_diff(scandir($path), array(".", ".."));

    foreach ($files as $file) {
        $filePath = $path.DS.$file;

        if (is_dir($filePath)) {
            removeInvalidSymlinks($filePath);
        }
        else if(is_link($filePath)) {
            $target = readlink($filePath);

            $target = BASEPATH.str_replace('../', '', $target);

            if(is_readable(realpath($target))) {
                continue;
            }

            unlink($filePath);
        }
    }
}

function removeEmptySubDirs($path)
{
    $empty = true;

    foreach (glob($path .DS ."*") as $file) {
        if (is_dir($file)) {
            if (! removeEmptySubDirs($file)) $empty = false;
        }
        else {
            $empty = false;
        }
    }

    if ($empty) rmdir($path);

    return $empty;
}

//
$workPaths = array(
    'assets',
);

//
if(is_dir(BASEPATH .'app'.DS.'Modules')) {
    $path = str_replace('/', DS, BASEPATH .'app/Modules/*');

    $dirs = glob($path , GLOB_ONLYDIR);

    foreach($dirs as $module) {
        $workPaths[] = str_replace('/', DS, 'app/Modules/'.$module);
    }
}

if(is_dir(BASEPATH .'app'.DS.'Templates')) {
    $path = str_replace('/', DS, BASEPATH .'app/Templates/*');

    $dirs = glob($path , GLOB_ONLYDIR);

    foreach($dirs as $template) {
        $workPaths[] = str_replace('/', DS, 'app/Templates/'.$template);
    }
}

//
$options = getopt('', array('path::'));

if(! empty($options['path'])) {
    $worksPaths = array_map('trim', explode(',', $options['path']));
}

//
foreach($workPaths as $workPath) {
    $searchPath = BASEPATH .$workPath .(($workPath == 'assets') ? '' : '/Assets');

    if(! is_dir($searchPath)) {
	continue;
    }

    $results = phpGrep($searchPath);

    if(empty($results)) {
        continue;
    }

    foreach($results as $path) {
        $filePath = str_replace(array(BASEPATH, '//'), array('', '/'), $path);

        switch($fileExt = pathinfo($filePath, PATHINFO_EXTENSION)) {
            case 'css':
            case 'js':
            case 'png':
            case 'gif':
            case 'jpg':
            case 'JPG':
            case 'ogg':
            case 'mp3':
            case 'pdf':
                makeSymlink($filePath);
                break;
            default:
                break;
        }
    }
}

echo 'Asset Symlinks created, cleaning up... ';

foreach(array('assets', 'modules', 'templates') as $path) {
    $path = WEBROOT.$path;

    removeInvalidSymlinks($path);
    removeEmptySubDirs($path);
}

echo 'Done.'.PHP_EOL;


