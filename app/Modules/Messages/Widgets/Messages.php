<?php

namespace App\Modules\Messages\Widgets;

use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Messages\Models\Message;


class Messages extends Widget
{

    public function render(array $parameters = array())
    {
        $count = Message::count();

        $data = array(
            'color' => 'red',
            'title' => $count,
            'content' => __d('users', 'Messages'),
            'icon'    => 'envelope',
            'url'     => site_url('admin/messages')
        );

        return View::make('Partials/DashboardStatBox', $data, 'Platform')->render();
    }
}
