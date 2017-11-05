<?php

namespace App\Modules\Platform\Controllers;

use Nova\Http\Request;
use Nova\Routing\Controller;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Response;

use App\Modules\Messages\Models\Message;
use App\Modules\Platform\Models\Activity;


class Heartbeat extends BaseController
{

    public function update(Request $request)
    {
        // Update the User Activity.
        Activity::updateCurrent($request);

        //
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
                array('id' => $notification->id, 'uuid' => $notification->uuid), $notification->data
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
