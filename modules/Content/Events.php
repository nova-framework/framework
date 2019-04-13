<?php

/*
|--------------------------------------------------------------------------
| Module Events
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Events for the module.
*/

use Modules\Content\Models\Menu;
use Modules\Content\Models\Post;

/**
 * Handle the Posts cache.
 */
Event::listen('content.post.updated', function (Post $post, $creating)
{
    Cache::section('content')->flush();
});

Event::listen('content.post.deleted', function (Post $post)
{
    Cache::section('content')->flush();
});

/**
 * Handle the Backend Menu Sidebar.
 */
Event::listen('backend.menu.sidebar', function ()
{
    return array(

        // Media.
        array(
            'url'    => site_url('admin/media'),
            'title'  => __d('content', 'Media'),
            'icon'   => 'picture-o',
            'weight' => 1,

            //
            'path'   => 'media',
        ),

        // Posts.
        array(
            'url'    => '#',
            'title'  => __d('content', 'Posts'),
            'icon'   => 'thumb-tack',
            'weight' => 1,

            //
            'path'   => 'posts',
        ),
        array(
            'url'    => site_url('admin/content/posts'),
            'title'  => __d('content', 'All Posts'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'posts.list',
            //'can'    => 'lists:' .Post::class,
        ),
        array(
            'url'    => site_url('admin/content/create/post'),
            'title'  => __d('content', 'Create a new Post'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'posts.create',
            //'can'    => 'create:' .Post::class,
        ),
        array(
            'url'    => site_url('admin/taxonomies/categories'),
            'title'  => __d('content', 'Categories'),
            'icon'   => 'circle-o',
            'weight' => 2,

            //
            'path'   => 'posts.categories',
            //'can'    => 'lists:' .Post::class,
        ),
        array(
            'url'    => site_url('admin/taxonomies/tags'),
            'title'  => __d('content', 'Tags'),
            'icon'   => 'circle-o',
            'weight' => 2,

            //
            'path'   => 'posts.tags',
            //'can'    => 'lists:' .Post::class,
        ),

        // Comments.
        array(
            'url'    => site_url('admin/comments'),
            'title'  => __d('content', 'Comments'),
            'icon'   => 'comments',
            'weight' => 4,

            //
            'path'   => 'comments',
            //'can'    => 'lists:' .Comment::class,
        ),

        // Pages.
        array(
            'url'    => '#',
            'title'  => __d('content', 'Pages'),
            'icon'   => 'files-o',
            'weight' => 2,

            //
            'path'   => 'pages',
        ),
        array(
            'url'    => site_url('admin/content/pages'),
            'title'  => __d('content', 'All Pages'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'pages.list',
            //'can'    => 'lists:' .Page::class,
        ),
        array(
            'url'    => site_url('admin/content/create/page'),
            'title'  => __d('content', 'Create a new Page'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'pages.create',
            //'can'    => 'create:' .page::class,
        ),

        // Menus.
        array(
            'url'    => site_url('admin/menus'),
            'title'  => __d('content', 'Menus'),
            'icon'   => 'bars',
            'weight' => 3,

            //
            'path'   => 'menus',
            //'can'    => 'lists:' .Menu::class,
        ),

        // Blocks.
        array(
            'url'    => '#',
            'title'  => __d('content', 'Blocks'),
            'icon'   => 'cubes',
            'weight' => 4,

            //
            'path'   => 'blocks',
        ),
        array(
            'url'    => site_url('admin/content/blocks'),
            'title'  => __d('content', 'All Blocks'),
            'icon'   => 'circle-o',
            'weight' => 0,

            //
            'path'   => 'blocks.list',
            //'can'    => 'lists:' .Block::class,
        ),
        array(
            'url'    => site_url('admin/content/create/block'),
            'title'  => __d('content', 'Create a new Block'),
            'icon'   => 'circle-o',
            'weight' => 1,

            //
            'path'   => 'blocks.create',
            //'can'    => 'create:' .Block::class,
        ),
        array(
            'url'    => site_url('admin/blocks'),
            'title'  => __d('content', 'Widget Positions'),
            'icon'   => 'circle-o',
            'weight' => 2,

            //
            'path'   => 'blocks.position',
            //'can'    => 'lists:' .Block::class,
        ),
    );
});

