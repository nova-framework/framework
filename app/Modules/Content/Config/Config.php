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
    | The Frontpage Name
    |--------------------------------------------------------------------------
    |
    */

    'frontpage' => null,

    /*
    |--------------------------------------------------------------------------
    | The Attachments Configuration
    |--------------------------------------------------------------------------
    |
    */

    'attachments' => array(
        // Where the uploaded files are stored.
        'path'      => base_path('assets/files'),

        // Where the (generated) thumbnails are stored.
        'thumbPath' => base_path('assets/files/thumbnails'),
    ),

    /*
    |--------------------------------------------------------------------------
    | Registered Post Types
    |--------------------------------------------------------------------------
    |
    */

    'postTypes' => array(
        'post' => array(
            'model' => 'App\Modules\Content\Models\Post',

            'labels' => array(
                'name'        => __d('content', 'Post'),
                'namePlural'  => __d('content', 'Posts'),
                'menuName'    => __d('content', 'Posts'),

                'addNew'      => __d('content', 'Add New'),
                'addNewItem'  => __d('content', 'Create a new Post'),
                'editItem'    => __d('content', 'Edit Post'),
                'updateItem'  => __d('content', 'Update Post'),
                'deleteItem'  => __d('content', 'Delete Post'),
                'newItem'     => __d('content', 'New Post'),
                'allItems'    => __d('content', 'All Posts'),
                'viewItem'    => __d('content', 'View Post'),
                'searchItems' => __d('content', 'Search Posts'),
                'notFound'    => __d('content', 'No posts found'),
            ),

            'label'       => __d('content', 'posts'),
            'description' => __d('content', 'A type of content for blogging, featuring categories, tags and comments.'),

            'hierarchical' => false,
            'hasArchive'   => true,

            'rewrite' => array(
                'slug' => 'posts'
            ),
        ),
        'page' => array(
            'model' => 'App\Modules\Content\Models\Page',

            'labels' => array(
                'name'        => __d('content', 'Page'),
                'namePlural'  => __d('content', 'Pages'),
                'menuName'    => __d('content', 'Pages'),

                'addNew'      => __d('content', 'Add New'),
                'addNewItem'  => __d('content', 'Create a new Page'),
                'editItem'    => __d('content', 'Edit Page'),
                'updateItem'  => __d('content', 'Update Page'),
                'deleteItem'  => __d('content', 'Delete Page'),
                'newItem'     => __d('content', 'New Page'),
                'allItems'    => __d('content', 'All Pages'),
                'viewItem'    => __d('content', 'View Page'),
                'searchItems' => __d('content', 'Search Pages'),
                'notFound'    => __d('content', 'No pages found'),
            ),

            'label'       => __d('content', 'pages'),
            'description' => __d('content', 'A stand-alone page, optionally with menu items.'),

            'hierarchical' => true,
            'hasArchive'   => false,

            'rewrite' => array(
                'slug' => 'pages'
            ),
        ),
        'block' => array(
            'model' => 'App\Modules\Content\Models\Block',

            'labels' => array(
                'name'        => __d('content', 'Block'),
                'namePlural'  => __d('content', 'Blocks'),
                'menuName'    => __d('content', 'Blocks'),

                'addNew'      => __d('content', 'Add New'),
                'addNewItem'  => __d('content', 'Create a new Block'),
                'editItem'    => __d('content', 'Edit Block'),
                'updateItem'  => __d('content', 'Update Block'),
                'deleteItem'  => __d('content', 'Delete Block'),
                'newItem'     => __d('content', 'New Block'),
                'allItems'    => __d('content', 'All Blocks'),
                'viewItem'    => __d('content', 'View Block'),
                'searchItems' => __d('content', 'Search Blocks'),
                'notFound'    => __d('content', 'No blocks found'),
            ),

            'label'       => __d('content', 'blocks'),
            'description' => __d('content', 'A content element which is displayed in one of the Widget Positions.'),

            'hierarchical' => false,
            'hasArchive'   => false,

            'rewrite' => array(
                'slug' => 'blocks'
            ),
        ),
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
        'block' => array(
            'name'  => __d('content', 'Block'),
            'title' => __d('content', 'Blocks'),
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

        // Custom Link
        'custom' => array(
            'name'  => __d('content', 'Custom Link'),
            'title' => __d('content', 'Custom Links'),
        ),
    ),

    'statuses' => array(
        'draft'           => __d('content', 'Draft'),
        'publish'         => __d('content', 'Published'),
        'password'        => __d('content', 'Password protected'),
        'private'         => __d('content', 'Private'),
        'private-draft'   => __d('content', 'Draft'),
        'private-review'  => __d('content', 'Pending Review'),
        'review'          => __d('content', 'Pending Review'),
    ),

);
