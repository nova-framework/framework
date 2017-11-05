<?php
/**
 * View Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Themes Configuration
    |--------------------------------------------------------------------------
    |
    */

    'themes' => array(
        'path'      => APPDIR .'Themes',

        'namespace' => 'App\Themes\\',
    ),

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Template files will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => STORAGE_PATH .'views',

);
