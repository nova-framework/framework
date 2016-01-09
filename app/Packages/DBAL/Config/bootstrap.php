<?php
/**
 * Bootstrap handler - perform Nova Framework's Package bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 9th, 2016
 */

$configDir = dirname(__FILE__) .DS;

/*
 * Check if the Doctrine DBAL is installed.
 */
if (! file_exists(BASEPATH .str_replace('/', DS, 'vendor/doctrine/dbal/composer.json'))) {
    echo "<h1>Please install Doctrine DBAL via composer.json</h1>";
    echo "<p>For properly work, this DBAL Package needs the Doctrine DBAL v2.5.x to be installed via composer.json</p>";
    exit;
}

//
include $configDir .'constants.php';
include $configDir .'config.php';
