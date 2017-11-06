<?php

namespace App\Modules\Permissions\Widgets;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Permissions\Models\Permission;


class RegisteredPermissions extends Widget
{

    public function render()
    {
        $count = Permission::count();

        $data = array(
            'color' => 'aqua',
            'title' => $count,
            'content' => __d('permissions', 'Registered Permissions'),
            'icon'    => 'cube',
            'url'     => site_url('admin/permissions')
        );

        return View::make('Widgets/DashboardStatBox', $data, 'Platform')->render();
    }
}
