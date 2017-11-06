<?php

namespace App\Modules\Roles\Widgets;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Roles\Models\Role;


class RegisteredRoles extends Widget
{

    public function render()
    {
        $count = Role::count();

        $data = array(
            'color' => 'yellow',
            'title' => $count,
            'content' => __d('Roles', 'Registered Roles'),
            'icon'    => 'cubes',
            'url'     => site_url('admin/roles')
        );

        return View::make('Widgets/DashboardStatBox', $data, 'Platform')->render();
    }
}
