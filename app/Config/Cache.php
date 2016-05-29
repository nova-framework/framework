<?php
/**
 * Cache configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;


Config::set('cache', array(
    'storage'       => 'files', // Blank for auto
    'default_chmod' => 0777,    // 0777, 0666, 0644

    /*
      * Fall back when Driver is not supported.
      */
    'fallback'    => "files",

    'securityKey' => 'auto',
    'htaccess'    => true,
    'path'        => CACHEPATH,

    'memcache' => array(
        array("127.0.0.1",11211,1),
    ),
    'redis' => array(
        'host'     => '127.0.0.1',
        'port'     => '',
        'password' => '',
        'database' => '',
        'timeout'  => ''
    ),
    'ssdb' => array(
        'host'     => '127.0.0.1',
        'port'     => 8888,
        'password' => '',
        'timeout'  => ''
    ),
    'extensions' => array(),
));
