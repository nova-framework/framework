<?php

namespace Modules\Demos\Widgets;

use Nova\Support\Facades\View;
use Nova\Widget\Widget;


class SmallBoxOrders extends Widget
{
    /**
     * Handle the Widget
     *
     * @return mixed
     */
    public function handle()
    {
        $data = array(
            'color' => 'aqua',
            'title' => 150,
            'text'  => __d('demos', 'New Orders'),
            'icon'  => 'bag',
            'url'   => '#'
        );

        return View::make('Widgets/SmallBox', $data)->render();
    }
}
