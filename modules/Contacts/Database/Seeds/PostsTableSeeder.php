<?php

namespace Modules\Contacts\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use Modules\Content\Models\Menu;
use Modules\Content\Models\Post;


class PostsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // The Contact Block.
        $block = Post::updateOrCreate(array('name' => 'block-contact'), array(
            'author_id'      => 1,
            'content'        => null,
            'title'          => 'Contact Form',
            'excerpt'        => null,
            'status'         => 'publish',
            'menu_order'     => 0,
            'type'           => 'block',
            'comment_status' => 'closed',
        ));

        $block->guid = url('content', $block->id);

        // Setup the Metadata.
        $block->meta->block_show_title        = 0;
        $block->meta->block_visibility_mode   = 'show';
        $block->meta->block_visibility_path   = 'content/contact-us';
        $block->meta->block_visibility_filter = 'any';
        $block->meta->block_widget_position   = 'content-footer';

        $block->meta->block_handler_class = 'Modules\Contacts\Blocks\Message';
        $block->meta->block_handler_param = null;

        $block->save();

        //
        // The Contact Page.
        $page = Post::updateOrCreate(array('name' => 'contact-us'), array(
            'author_id'      => 1,
            'content'        => 'Please complete the following form to send us a message.',

            'title'          => $title = 'Contact Us',

            'excerpt'        => null,
            'status'         => 'publish',
            'type'           => 'page',
            'comment_status' => 'closed',
        ));

        $page->guid = url('content', $page->name);

        $page->meta->page_template = 'default';

        $page->save();

        //
        // The Contact MenuItem.
        $menuItem = Post::where('type', 'nav_menu_item')->whereHas('meta', function ($query) use ($page)
        {
            $query->where('key', 'menu_item_object')->where('value', 'page');

        })->whereHas('meta', function ($query) use ($page)
        {
            $query->where('key', 'menu_item_object_id')->where('value', $page->id);

        })->first();

        if (! is_null($menuItem)) {
            // The associated Menu Item exists, then there is no need to add it.
            return;
        }

        $menuItem = Post::create(array(
            'author_id'      => 1,
            'content'        => null,
            'title'          => null,
            'excerpt'        => null,
            'status'         => 'publish',
            'menu_order'     => 1,
            'type'           => 'nav_menu_item',
            'comment_status' => 'closed',
        ));

        $menuItem->name = $id = $menuItem->id;

        $menuItem->guid = url('content', $id);

        // Setup the Metadata.
        $menuItem->meta->menu_item_type             = 'page';
        $menuItem->meta->menu_item_menu_item_parent = 0;
        $menuItem->meta->menu_item_object           = 'page';
        $menuItem->meta->menu_item_object_id        = $page->id;
        $menuItem->meta->menu_item_target           = null;
        $menuItem->meta->menu_item_url              = null;

        $menuItem->save();

        // Update the Taxonomy.
        $taxonomy = Menu::slug('main-menu')->firstOrFail();

        $taxonomy->items()->attach($menuItem);

        $taxonomy->updateCount();
    }
}
