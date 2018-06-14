<?php

namespace Modules\Content\Widgets;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;
use Nova\Support\Collection;
use Nova\Support\Str;

use Shared\Widgets\Widget;

use Modules\Content\Models\Menu;
use Modules\Content\Models\MenuItem;


class MainMenu extends Widget
{

    public function render()
    {
        $siteUrl = Request::url();

        $items = Cache::remember('content.menus.main-menu', 1440, function ()
        {
            $menu = Menu::slug('main-menu')->first();

            $items = $menu->items->where('parent_id', 0);

            return $this->handleMenuItems($items);
        });

        $caret = true;

        $data = compact('items', 'siteUrl', 'caret');

        return View::make('Modules/Content::Widgets/MainMenuItems', $data)->render();
    }

    protected function handleMenuItems($items)
    {
        $result = array();

        $items->sort(function ($a, $b)
        {
            if ($a->menu_order === $b->menu_order) {
                return strcmp($a->title, $b->title);
            }

            return ($a->menu_order < $b->menu_order) ? -1 : 1;
        });

        foreach ($items as $item) {
            $type = $item->menu_item_type;

            if ($type !== 'custom') {
                $instance = $item->instance();

                if ($type == 'taxonomy') {
                    $title = $instance->name;

                    $taxonomy = ($instance->taxonomy == 'post_tag') ? 'tag' : $instance->taxonomy;

                    $url = url('content', array($taxonomy, $instance->slug));
                }

                // Everthing else is based on Posts or extended from them.
                else {
                    $title = $instance->title;

                    $url = site_url('content/' .$instance->name);
                }

                // If the user edited the title of the menu item, it will have its own title.

                if (! empty($item->title)) {
                    $title = $item->title;
                }
            }

            // Custom Link.
            else {
                $title = $item->title;

                $url = $item->menu_item_url;
            }

            $item->load('children');

            if (! $item->children->isEmpty()) {
                $items = $this->handleMenuItems($item->children);
            } else {
                $items = array();
            }

            $result[] = array(
                'title'    => $title,
                'url'      => $url,
                'children' => $items,
            );
        }

        return $result;
    }
}
