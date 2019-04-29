<?php

namespace Modules\Content\Support;

use Nova\Database\ORM\Collection;
use Nova\Support\Facades\View;


class MenuItemsNestable
{
    /**
     * The view used for rendering.
     *
     * @var string
     */
    protected static $view = 'Modules/Content::Partials/MenuItemsNestable';


    public static function render(Collection $items)
    {
        if (! $items->isEmpty()) {
            $items->load('children');

            // We will sort the items collection with the same algorithm as in the real widget.
            static::sortItems($items);

            return View::make(static::$view, compact('items'))->render();
        }
    }

    protected static function sortItems(Collection $items)
    {
        $items->sort(function ($a, $b)
        {
            if ($a->menu_order === $b->menu_order) {
                return strcmp($a->title, $b->title);
            }

            return ($a->menu_order < $b->menu_order) ? -1 : 1;
        });

        return $items;
    }
}
