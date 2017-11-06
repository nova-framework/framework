<?php

namespace App\Modules\Messages\Widgets;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use App\Modules\Messages\Models\Message;


class Messages extends Widget
{

    public function render()
    {
        $authUser = Auth::user();

        $count = Message::where('receiver_id', $authUser->id)->notReply()->count();

        $data = array(
            'color' => 'red',
            'title' => $count,
            'content' => __d('users', 'Messages'),
            'icon'    => 'envelope',
            'url'     => site_url('admin/messages')
        );

        return View::make('Widgets/DashboardStatBox', $data, 'Platform')->render();
    }
}
