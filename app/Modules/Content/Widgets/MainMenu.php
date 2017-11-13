<?php

namespace App\Modules\Content\Widgets;

use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;
use Nova\Support\Collection;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Menu;
use App\Modules\Content\Models\MenuItem;


class MainMenu extends Widget
{

    public function render()
    {
        $siteUrl = Request::url();

        //
        $menu = Menu::findOrFail(2); // This is the Main Menu.

        $items = $menu->items->where('parent_id', 0);

        static::sortItems($items);

        //
        $data = compact('menu', 'items', 'siteUrl');

        return View::make('Widgets/MainMenuItems', $data, 'Content')->render();
    }

    public static function handleItem(MenuItem $item)
    {
        $type = $item->menu_item_type;

        if ($type == 'custom') {
            $title = $item->title;

            $url = $item->menu_item_url;
        }

        // The item is not a Custom Link.
        else {
            $instance = $item->instance();

            if (($type == 'post') || ($type == 'page')) {
                $title = $instance->title;

                $url = site_url('content/' .$instance->name);
            }

            // Taxonomy.
            else if ($type == 'taxonomy') {
                $title = $instance->name;

                $url = site_url('content/category/' .$instance->slug);
            }
        }

        $item->load('children');

        $children = $item->children;

        static::sortItems($children);

        return array($title, $url, $children);
    }

    public static function sortItems(Collection $items)
    {
        return $items->sort(function ($a, $b)
        {
            if ($a->menu_order < $b->menu_order) {
                return strcmp($a->title, $b->title);
            }

            return ($a->menu_order < $b->menu_order) ? -1 : 1;
        });
    }
}
