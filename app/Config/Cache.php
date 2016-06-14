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
    /*
     * Default storage
     * if you set this storage => 'files', then $cache = phpFastCache(); <-- will be files cache
     */
    'storage' => 'files', // ssdb, predis, redis, mongodb, files, sqlite, auto, apc, wincache, xcache, memcache, memcached,

    /*
     * Default Path for Cache on HDD
     * Use full PATH like /home/username/cache
     * Keep it blank '', it will automatic setup for you
     */
    'path'        =>  STORAGE_PATH .'Cache' , // default path for files
    'securityKey' =>  '',                     // default will good. It will create a path by PATH/securityKey

    /*
     * FallBack Driver
     * Example, in your code, you use memcached, apc..etc, but when you moved your web hosting
     * Until you setup your new server caching, use 'overwrite' => 'files'
     */
    'overwrite' => 'files', // whatever caching will change to 'files' and you don't need to change ur code

    /*
     * .htaccess protect
     * default will be  true
     */
    'htaccess'  =>  true,

    /*
     * Default Memcache Server for all $cache = phpFastCache('memcache');
     */
    'server'    =>  array(
        array('127.0.0.1',11211,1),
        //array('new.host.ip',11211,1),
    ),


    'memcache' => array(
        array('127.0.0.1', 11211, 1),
        //array('new.host.ip',11211,1),
    ),
    'redis' => array(
        'host' => '127.0.0.1',
        'port' => '',
        'password' => '',
        'database' => '',
        'timeout' => '',
    ),
    'ssdb' => array(
        'host' => '127.0.0.1',
        'port' => 8888,
        'password' => '',
        'timeout' => '',
    ),
    // use 1 as normal traditional, 2 = phpfastcache fastest as default, 3 = phpfastcache memory stable
    'caching_method' => 2,
));
