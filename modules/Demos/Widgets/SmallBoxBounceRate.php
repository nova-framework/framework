<?php

namespace Modules\Demos\Widgets;

use Nova\Support\Facades\View;

use Plugins\Widgets\Widget;


class SmallBoxBounceRate extends Widget
{
    /**
     * Handle the Widget
     *
     * @return mixed
     */
    public function handle()
    {
        $data = array(
            'color' => 'green',
            'title' => sprintf('%d<sup style="font-size: 20px">%%</sup>', 53),
            'text'  => __d('demos', 'Bounce Rate'),
            'icon'  => 'stats-bars',
            'url'   => '#'
        );

        return View::make('Widgets/SmallBox', $data)->render();
    }
}
