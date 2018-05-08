<?php

use Modules\Content\Support\Facades\PostType;


/**
 * Register the Post Types.
 */

PostType::register('post', array(
    'model' => 'Modules\Content\Models\Post',

    'view'  => 'Modules/Content::Content/Post',

    'labels' => array(
        'name'        => __d('content', 'Post'),
        'items'       => __d('content', 'Posts'),

        'addNew'      => __d('content', 'Add New'),
        'addNewItem'  => __d('content', 'Add New Post'),
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

    'public'       => true,
    'hierarchical' => false,
    'hasArchive'   => true,

    'rewrite' => array(
        'slug' => 'posts'
    ),
));

PostType::register('page', array(
    'model' => 'Modules\Content\Models\Page',

    'view'  => 'Modules/Content::Content/Page',

    'labels' => array(
        'name'        => __d('content', 'Page'),
        'items'       => __d('content', 'Pages'),

        'addNew'      => __d('content', 'Add New'),
        'addNewItem'  => __d('content', 'Add New Page'),
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

    'public'       => true,
    'hierarchical' => true,
    'hasArchive'   => false,

    'rewrite' => array(
        'slug' => 'pages'
    ),
));

PostType::register('block', array(
    'model' => 'Modules\Content\Models\Block',

    'view'  => null, // The Blocks are rendered via the Modules\Content\BlockHandler.

    'labels' => array(
        'name'        => __d('content', 'Block'),
        'items'       => __d('content', 'Blocks'),

        'addNew'      => __d('content', 'Add New'),
        'addNewItem'  => __d('content', 'Add New Block'),
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

    'public'       => false,
    'hierarchical' => false,
    'hasArchive'   => false,

    'rewrite' => array(
        'slug' => 'blocks'
    ),
));

