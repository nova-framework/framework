<?php

namespace Modules\Users\Widgets;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use Modules\Users\Models\User;


class RegisteredUsers extends Widget
{

    public function render()
    {
        $count = User::hasMeta('activated', 1)->count();

        $data = array(
            'color' => 'green',
            'title' => $count,
            'content' => __d('users', 'Registered Users'),
            'icon'    => 'users',
            'url'     => site_url('admin/users')
        );

        return View::make('Modules/Platform::Widgets/DashboardStatBox', $data)->render();
    }
}
