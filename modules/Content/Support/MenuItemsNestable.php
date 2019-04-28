<?php

namespace Modules\Content\Support;

use Nova\Database\ORM\Collection;
use Nova\Support\Facades\View;


class MenuItemsNestable
{

    public static function render(Collection $items)
    {
        $items->load('children');

        // We will sort the items collection with the same algorithm as in the real widget.
        $items->sort(function ($a, $b)
        {
            if ($a->menu_order === $b->menu_order) {
                return strcmp($a->title, $b->title);
            }

            return ($a->menu_order < $b->menu_order) ? -1 : 1;
        });

        return View::make('Modules/Content::Partials/MenuItemsNestable', compact('items'))->render();
    }
}
