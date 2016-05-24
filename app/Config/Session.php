<?php
/**
 * Session Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


Config::set('session', array(
    'lifetime' => 180,          // The number of minutes the session is allowed to remain idle before it expires.
    'files'    => SESSION_PATH, // The location where the session files may be stored.
    'lottery' => array(2, 100),
    // Cookie configuration
    'cookie'   => PREFIX .'session',
    'path'     => '/',
    'domain'   => null,
    'secure'   => false,
));
