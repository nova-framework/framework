<?php

namespace App\Modules\Users\Widgets;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Users\Models\User;


class RegisteredUsers extends Widget
{

    public function render()
    {
        $count = User::whereMeta('activated', 1)->count();

        $data = array(
            'color' => 'green',
            'title' => $count,
            'content' => __d('users', 'Registered Users'),
            'icon'    => 'users',
            'url'     => site_url('admin/users')
        );

        return View::make('Widgets/DashboardStatBox', $data, 'Platform')->render();
    }
}
