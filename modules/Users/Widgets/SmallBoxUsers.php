<?php

namespace Modules\Users\Widgets;

use Nova\Support\Facades\View;
use Nova\Widget\Widget;

use Modules\Users\Models\User;


class SmallBoxUsers extends Widget
{
    /**
     * Handle the Widget
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('active', 1)->count();

        $data = array(
            'color' => 'yellow',
            'users' => $users
        );

        return View::make('Widgets/SmallBox', $data)->render();
    }
}
