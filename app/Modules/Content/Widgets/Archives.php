<?php

namespace App\Modules\Content\Widgets;

use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Language;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use Shared\Widgets\Widget;

use App\Modules\Content\Models\Post;

use Carbon\Carbon;


class Archives extends Widget
{

    public function render()
    {
        $items = Cache::remember('content.archives', 1440, function ()
        {
            $posts = Post::where('type', 'post')
                ->whereIn('status', array('publish', 'password'))
                ->select('id', 'created_at')
                ->get();

            return $posts->groupBy(function ($value)
            {
                return Carbon::parse($value->created_at)->format('Y/m');

            })->mapWithKeys(function ($value, $key)
            {
                return array($key => count($value));
            });
        });

        return View::make('Widgets/Archives', compact('items'), 'Content')->render();
    }
}
