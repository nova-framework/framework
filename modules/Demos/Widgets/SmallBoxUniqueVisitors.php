<?php

namespace Modules\Demos\Widgets;

use Nova\Support\Facades\View;

use Plugins\Widgets\Widget;


class SmallBoxUniqueVisitors extends Widget
{
    /**
     * Handle the Widget
     *
     * @return mixed
     */
    public function handle()
    {
        $data = array(
            'color' => 'red',
            'title' => 65,
            'text'  => __d('demos', 'Unique Visitors'),
            'icon'  => 'pie-graph',
            'url'   => '#'
        );

        return View::make('Widgets/SmallBox', $data)->render();
    }
}
