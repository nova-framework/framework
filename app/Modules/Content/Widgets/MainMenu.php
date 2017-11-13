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

        $items = $menu->items()->where('parent_id', 0)->get();

        static::sortItems($items);

        //
        $data = compact('menu', 'items', 'siteUrl');

        return View::make('Widgets/MainMenuItems', $data, 'Content')->render();
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
