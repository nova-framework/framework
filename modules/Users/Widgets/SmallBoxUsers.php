<?php

namespace Users\Widgets;

use Nova\Support\Facades\View;
use Nova\Widget\Widget;

use Users\Models\User;


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
            'title' => $users,
            'text'  => __d('users', 'Registered Users'),
            'icon'  => 'person-add',
            'url'   => site_url('admin/users')
        );

        return View::make('Widgets/SmallBox', $data)->render();
    }
}
