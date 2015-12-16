<?php

// This file allows us to emulate Apache's "mod_rewrite" functionality from the built-in PHP web server.
// Provides a convenient way to test the MVC application without having installed a "real" web server software.

define('DS', DIRECTORY_SEPARATOR);

//
$public_path = dirname(__DIR__).DS.'public'.DS;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = urldecode($uri);

//
$requested = $public_path .$uri;

if (($uri !== '/') && file_exists($requested)) {
    return false;
}

require_once $public_path .'index.php';
