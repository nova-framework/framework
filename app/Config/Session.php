<?php
/**
 * Session Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    'driver' => 'file', // The Session Driver used for storing Session data; supported: 'file', 'database' or 'cookie'.

    // The Database Session Driver configuration.
    'table'      => 'sessions', // The Database Table hosting the Session data.
    'connection' => null,       // The Database Connection name used by driver.

    // Session Lifetime.
    'lifetime'      => 180,   // Number of minutes the Session is allowed to remain idle before it expires.
    'expireOnClose' => false, // If you want them to immediately expire on the browser closing, set that.

    // The File Session Driver configuration.
    'files'    => STORAGE_PATH .'sessions',   // File Session Handler - where the Session files may be stored.
    'lottery'  => array(2, 100), // Option used by the Garbage Collector, to remove the stalled Session files.

    // Cookie configuration.
    'cookie'   => PREFIX .'session',
    'path'     => '/',
    'domain'   => null,
    'secure'   => false,
);
