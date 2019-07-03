<?php

namespace Modules\Messages\Widgets;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\View;

use Shared\Widgets\Widget;

use Modules\Messages\Models\Message;


class Messages extends Widget
{

    public function render()
    {
        $authUser = Auth::user();

        $count = Message::where('receiver_id', $authUser->id)->notReply()->count();

        $data = array(
            'color' => 'red',
            'title' => $count,
            'content' => __d('messages', 'Messages'),
            'icon'    => 'envelope',
            'url'     => site_url('messages')
        );

        return View::make('Modules/Platform::Widgets/DashboardStatBox', $data)->render();
    }
}
