<?php

namespace AcmeCorp\Backend\Widgets;

use Nova\Support\Facades\View;

use AcmeCorp\Backend\Models\User;


class DashboardUsersPanel
{

    public function render()
    {
        $users = User::count();

        $data = array(
            'type'  => 'primary',
            'icon'  => 'users',
            'count' => $users,
            'title' => __d('backend', 'Registered Users'),
            'link'  => site_url('admin/users'),
        );

        return View::fetch('AcmeCorp/Backend::Widgets/DashboardPanel', $data);
    }
}
