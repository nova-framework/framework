<?php
/**
 * Modules Configuration
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */


return array(
    //--------------------------------------------------------------------------
    // Path to Modules
    //--------------------------------------------------------------------------

    'path' => APPDIR .'Modules',

    //--------------------------------------------------------------------------
    // Modules Base Namespace
    //--------------------------------------------------------------------------

    'namespace' => 'App\Modules\\',

    //--------------------------------------------------------------------------
    // Path to Cache
    //--------------------------------------------------------------------------

    'cache' => STORAGE_PATH .'modules.php',

    //--------------------------------------------------------------------------
    // Registered Modules
    //--------------------------------------------------------------------------

    'modules' => array(
        'demos' => array(
            'name'     => 'Modules/Demos',
            'basename' => 'Demos',
            'enabled'  => true,
            'order'    => 10001,
        ),
        'files' => array(
            'name'     => 'Modules/Files',
            'basename' => 'Files',
            'enabled'  => true,
            'order'    => 9001,
        ),
        'system' => array(
            'name'     => 'Modules/System',
            'basename' => 'System',
            'enabled'  => true,
            'order'    => 8001,
        ),
        'users' => array(
            'name'     => 'Modules/Users',
            'basename' => 'Users',
            'enabled'  => true,
            'order'    => 9001,
        ),
    ),
);
