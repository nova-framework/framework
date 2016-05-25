<?php
/**
 * Session Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


Config::set('session', array(
    'handler' => '\Session\FileSessionHandler', // The default Session Handler, using files for Session cache.

    // Storage configuration.
    'lifetime' => 180,           // Number of minutes the Session is allowed to remain idle before it expires.
    'files'    => SESSION_PATH,  // File Session Handler - the location where the Session files may be stored.
    'lottery'  => array(2, 100), // Option used by the Garbage Collector, to remove the stalled Session files.

    // Cookie configuration.
    'name'     => PREFIX .'session',
    'path'     => '/',
    'domain'   => null,
    'secure'   => false,

    // Wheter or not will be used the Cookies encryption.
    'encrypt'  => true
));
