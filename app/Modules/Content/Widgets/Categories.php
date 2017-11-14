<?php

namespace App\Modules\Content\Widgets;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Taxonomy;


class Categories extends Widget
{

    public function render()
    {
        $categories = Cache::remember('content.categories', 1440, function ()
        {
            return Taxonomy::where('taxonomy', 'category')->where('count', '>', 0)->get();
        });

        return View::make('Widgets/Categories', compact('categories'), 'Content')->render();
    }
}
