<?php

/*
|--------------------------------------------------------------------------
| Module Configuration
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Configuration for the module.
*/

return array(

    /*
    |--------------------------------------------------------------------------
    | The Translated Names of the Post Types and Statuses
    |--------------------------------------------------------------------------
    |
    */

    'labels' => array(

        // Posts.
        'post' => array(
            'name'  => __d('content', 'Post'),
            'title' => __d('content', 'Posts'),
        ),
        'page' => array(
            'name'  => __d('content', 'Page'),
            'title' => __d('content', 'Pages'),
        ),

        // Taxonomies.
        'category' => array(
            'name'  => __d('content', 'Category'),
            'title' => __d('content', 'Categories'),
        ),
        'tag' => array(
            'name'  => __d('content', 'Tag'),
            'title' => __d('content', 'Tags'),
        ),
    ),

    'statuses' => array(
        'publish'  => __d('content', 'Published'),
        'password' => __d('content', 'Password protected'),
        'private'  => __d('content', 'Private'),
    ),

    /*
    |--------------------------------------------------------------------------
    | Registered Custom Post Types
    |--------------------------------------------------------------------------
    |
    */

    'postTypes' => arraY(
//        'video' => App\Models\Video::class,
    ),

    /*
    |--------------------------------------------------------------------------
    | Registered Shortcodes
    |--------------------------------------------------------------------------
    |
    */

    'shortcodes' => array(
//        'foo' => App\Shortcodes\FooShortcode::class,
    ),

);
