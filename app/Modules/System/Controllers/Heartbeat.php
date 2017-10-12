<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Response;

use App\Modules\System\Controllers\BaseController;
use App\Modules\System\Models\Message;

class Heartbeat extends BaseController
{

    public function update()
    {
        $authUser = Auth::user();

        $lastId = (int) Input::get('last_id', 0);

        //Retrieve the unread notifications for the current User.
        $query = $authUser->unreadNotifications();

        // Get the total number of unread notifications.
        $count = $query->count();

        // Handle the last notification ID if requested.
        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $lastId = 0;

        // Get the first 5 unread notifications.
        $notifications = $query->limit(10)->get();

        if (! $notifications->isEmpty()) {
            $notification = $notifications->first();

            $lastId = $notification->id;
        }

        $items = array();

        foreach ($notifications as $notification) {
            $items[] = array_merge(
                array('id' => $notification->uuid), $notification->data
            );
        }

        // Get the number of unread messages.
        $messages = Message::where('receiver_id', $authUser->id)->unread()->count();

        return Response::json(array(
            'messages' => array(
                'count' => $messages,
            ),
            'notifications' => array(
                'count'  => $count,
                'lastId' => $lastId,
                'items'  => $items,
            ),
        ));
    }
}
