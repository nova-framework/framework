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
        'System' => array(
            'enabled'  => true,
            'autoload' => array('config', 'routes'),
        ),
        'Users' => array(
            'enabled'  => true,
            'autoload' => array('routes'),
        ),
        'Files' => array(
            'enabled'  => true,
            'autoload' => array('config', 'routes'),
        ),
        'Demos'  => array(
            'enabled' => true,
        ),
    ),
));
