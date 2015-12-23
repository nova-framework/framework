<?php
/**
 * Bootstrap handler - perform Nova Framework's Module bootstrap stage.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 23th, 2015
 */

$configDir = dirname(__FILE__) .DS;

//
//include $configDir .'constants.php';
include $configDir .'config.php';
include $configDir .'routes.php';
