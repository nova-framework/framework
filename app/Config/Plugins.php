<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Author Information For Plugin Generation
    |--------------------------------------------------------------------------
    |
    */

    'author' => array(
        'name'        => 'John Doe',
        'email'        => 'john.doe@novaframework.dev',
        'homepage'    => 'http://novaframework.dev',
    ),

    /*
    |--------------------------------------------------------------------------
    | Loading Options For The Installed Plugins
    |--------------------------------------------------------------------------
    |
    */

    'options' => array(
        'file_field' => array(
            'order'        => 8001,
            'enabled'    => true,
        ),
        'widgets' => array(
            'order'        => 8001,
            'enabled'    => true,
        ),
        /*
        'bootstrap' => array(
            'order'        => 9001,
            'enabled'    => true,
        ),
        /*
        'backend' => array(
            'order'        => 9001,
            'enabled'    => true,
        ),
        'content' => array(
            'order'        => 9001,
            'enabled'    => true,
        ),
        */
    ),
);
