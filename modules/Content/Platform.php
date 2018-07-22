<?php

use Modules\Content\Support\Facades\PostType;
use Modules\Content\Support\Facades\TaxonomyType;


/**
 * Register the Taxonomy Types.
 */
TaxonomyType::register('category', array(
    'labels' => array(
        'name'        => __d('content', 'Category'),
        'title'       => __d('content', 'Categories'),

        'searchItems' => __d('content', 'Search Categories'),
        'allItems'    => __d('content', 'All Category'),

        'parentItem'      => __d('content', 'Parent Category'),
        'parentItemColon' => __d('content', 'Parent Category:'),

        'editItem'    => __d('content', 'Edit Category'),
        'updateItem'  => __d('content', 'Update Category'),
        'deleteItem'  => __d('content', 'Delete Category'),
        'addNewItem'  => __d('content', 'Add New Category'),
        'newItemName' => __d('content', 'New Category Name'),

        'menuName'    => __d('content', 'Categories'),
    ),

    'label'       => __d('content', 'posts'),
    'description' => __d('content', 'A type of hierarchical taxonomy.'),

    'hierarchical' => true,

    'rewrite' => array(
        'slug' => 'posts'
    ),
));

TaxonomyType::register('tag', array(
    'labels' => array(
        'name'        => __d('content', 'Tag'),
        'title'       => __d('content', 'Tags'),

        'searchItems' => __d('content', 'Search Tags'),
        'allItems'    => __d('content', 'All Tag'),

        'parentItem'      => null,
        'parentItemColon' => null,

        'editItem'    => __d('content', 'Edit Tag'),
        'updateItem'  => __d('content', 'Update Tag'),
        'deleteItem'  => __d('content', 'Delete Tag'),
        'addNewItem'  => __d('content', 'Add New Tag'),
        'newItemName' => __d('content', 'New Tag Name'),

        'menuName'    => __d('content', 'Tags'),
    ),

    'label'       => __d('content', 'tags'),
    'description' => __d('content', 'A type of non-hierarchical taxonomy.'),

    'hierarchical' => false,

    'rewrite' => array(
        'slug' => 'tags'
    ),
));


/**
 * Register the Post Types.
 */

PostType::register('post', array(
    'model' => 'Modules\Content\Models\Post',

    'view'  => 'Modules/Content::Content/Post',

    'labels' => array(
        'name'        => __d('content', 'Post'),
        'title'       => __d('content', 'Posts'),

        'searchItems' => __d('content', 'Search Posts'),
        'allItems'    => __d('content', 'All Posts'),
        'notFound'    => __d('content', 'No posts found'),

        'parentItem'      => null,
        'parentItemColon' => null,

        'addNew'      => __d('content', 'Add New'),
        'addNewItem'  => __d('content', 'Add New Post'),
        'editItem'    => __d('content', 'Edit Post'),
        'updateItem'  => __d('content', 'Update Post'),
        'deleteItem'  => __d('content', 'Delete Post'),
        'newItem'     => __d('content', 'New Post'),
        'viewItem'    => __d('content', 'View Post'),
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
        'title'       => __d('content', 'Pages'),

        'searchItems' => __d('content', 'Search Pages'),
        'allItems'    => __d('content', 'All Pages'),
        'notFound'    => __d('content', 'No pages found'),

        'parentItem'      => __d('content', 'Parent Page'),
        'parentItemColon' => __d('content', 'Parent Page:'),

        'addNew'      => __d('content', 'Add New'),
        'addNewItem'  => __d('content', 'Add New Page'),
        'editItem'    => __d('content', 'Edit Page'),
        'updateItem'  => __d('content', 'Update Page'),
        'deleteItem'  => __d('content', 'Delete Page'),
        'newItem'     => __d('content', 'New Page'),
        'viewItem'    => __d('content', 'View Page'),
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

    'view'  => null, // The Blocks are rendered via the Modules\Content\Widgets\BlockHandler.

    'labels' => array(
        'name'        => __d('content', 'Block'),
        'title'       => __d('content', 'Blocks'),

        'searchItems' => __d('content', 'Search Blocks'),
        'allItems'    => __d('content', 'All Blocks'),
        'notFound'    => __d('content', 'No blocks found'),

        'parentItem'      => null,
        'parentItemColon' => null,

        'addNew'      => __d('content', 'Add New'),
        'addNewItem'  => __d('content', 'Add New Block'),
        'editItem'    => __d('content', 'Edit Block'),
        'updateItem'  => __d('content', 'Update Block'),
        'deleteItem'  => __d('content', 'Delete Block'),
        'newItem'     => __d('content', 'New Block'),
        'viewItem'    => __d('content', 'View Block'),
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

