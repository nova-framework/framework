<?php
/**
 * Active Modules
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Config\Config;


Config::set('modules', array(
    //--------------------------------------------------------------------------
    // Path to Modules
    //--------------------------------------------------------------------------

    'path' => APPDIR .'Modules',

    //--------------------------------------------------------------------------
    // Modules Base Namespace
    //--------------------------------------------------------------------------

    'namespace' => 'App\Modules\\',

    //--------------------------------------------------------------------------
    // Registered Modules
    //--------------------------------------------------------------------------

    'repository' => array(
        'Demos'  => array(
            'enabled' => true,
            'order'   => 10001,
        ),
        'Files' => array(
            'enabled'  => true,
            'order'    => 9001,
            'autoload' => array('config', 'routes'),
        ),
        'System' => array(
            'enabled'  => true,
            'order'    => 8001,
            'autoload' => array('config', 'routes'),
        ),
        'Users' => array(
            'enabled'  => true,
            'order'    => 9001,
            'autoload' => array('routes'),
        ),
    ),
));
