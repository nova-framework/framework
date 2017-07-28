<?php
/**
 * View Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Framework view path has already been registered for you.
    |
    */

    'paths' => array(
        APPPATH .'Views'
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
