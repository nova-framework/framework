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
        'platform' => array(
            'name'     => 'Modules/Platform',
            'basename' => 'Platform',
            'enabled'  => true,
            'order'    => 7001,
        ),
        'fields' => array(
            'name'     => 'Modules/Fields',
            'basename' => 'Fields',
            'enabled'  => true,
            'order'    => 7002,
        ),
        'permissions' => array(
            'name'     => 'Modules/Permissions',
            'basename' => 'Permissions',
            'enabled'  => true,
            'order'    => 8001,
        ),
        'roles' => array(
            'name'     => 'Modules/Roles',
            'basename' => 'Roles',
            'enabled'  => true,
            'order'    => 8002,
        ),
        'users' => array(
            'name'     => 'Modules/Users',
            'basename' => 'Users',
            'enabled'  => true,
            'order'    => 8003,
        ),
        'chat' => array(
            'name'     => 'Modules/Chat',
            'basename' => 'Chat',
            'enabled'  => true,
            'order'    => 9001,
        ),
        'files' => array(
            'name'     => 'Modules/Files',
            'basename' => 'Files',
            'enabled'  => true,
            'order'    => 9001,
        ),
        'messages' => array(
            'name'     => 'Modules/Messages',
            'basename' => 'Messages',
            'enabled'  => true,
            'order'    => 9001,
        ),
    ),
);
