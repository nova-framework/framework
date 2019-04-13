<?php

namespace Modules\Content\Blocks;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\View;

use Modules\Content\Blocks\Block;
use Modules\Content\Models\Post;

use Carbon\Carbon;


class Archives extends Block
{

    public function render()
    {
        $items = Cache::section('content')->remember('blocks.archives', 1440, function ()
        {
            $items = Post::where('type', 'post')
                ->whereIn('status', array('publish', 'password'))
                ->select('id', 'created_at')
                ->get();

            return $items->groupBy(function ($value)
            {
                return Carbon::parse($value->created_at)->format('Y/m');

            })->mapWithKeys(function ($value, $key)
            {
                return array($key => count($value));
            });
        });

        return View::make('Modules/Content::Blocks/Archives', compact('items'))->render();
    }
}
