<?php

namespace Modules\Content\Blocks;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\View;

use Modules\Content\Blocks\Block;
use Modules\Content\Models\Taxonomy;


class Categories extends Block
{

    public function render()
    {
        $categories = Cache::remember('content.categories', 1440, function ()
        {
            return Taxonomy::where('taxonomy', 'category')->where('count', '>', 0)->get();
        });

        return View::make('Modules/Content::Blocks/Categories', compact('categories'))->render();
    }
}
