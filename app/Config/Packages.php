<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Author Information For Package Generation
    |--------------------------------------------------------------------------
    |
    */

    'author' => array(
        'name'     => 'John Doe',
        'email'    => 'john.doe@novaframework.dev',
        'homepage' => 'http://novaframework.dev',
    ),

    //--------------------------------------------------------------------------
    // Path To The Cache File
    //--------------------------------------------------------------------------

    'cache' => STORAGE_PATH .'framework' .DS .'packages.php',

    /*
    |--------------------------------------------------------------------------
    | Loading Options For The Installed Packages
    |--------------------------------------------------------------------------
    |
    */

    'options' => array(
        /*
        'backend' => array(
            'order'      => 9001,
            'enabled'    => true,
        ),
        */
    ),
);
