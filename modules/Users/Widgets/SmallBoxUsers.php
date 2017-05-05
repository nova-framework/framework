<?php

namespace Modules\Users\Widgets;

use Nova\Support\Facades\View;

use Plugins\Widgets\Widget;

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
        $users = User::where('activated', 1)->count();

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
