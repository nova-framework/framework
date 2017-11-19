<?php

namespace App\Modules\Content\Blocks;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Post;

use Carbon\Carbon;


class Archives
{

    public function render()
    {
        $items = Cache::remember('content.archives', 1440, function ()
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

        return View::make('Blocks/Archives', compact('items'), 'Content')->render();
    }
}
