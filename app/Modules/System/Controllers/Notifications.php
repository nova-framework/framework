<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Response;

use App\Modules\System\Controllers\BaseController;


class Notifications extends BaseController
{

    public function index()
    {
        $authUser = Auth::user();

        $notifications = $authUser->notifications()->paginate(25);

        return $this->createView()
            ->shares('title', __d('system', 'Notifications'))
            ->with('notifications', $notifications);
    }

    public function data()
    {
        $authUser = Auth::user();

        $lastId = (int) Input::get('lastId', 0);

        //Retrieve the unread notifications for the current User.
        $query = $authUser->unreadNotifications()->where('id', '>', $lastId);

        // Get the total number of unread notifications.
        $count = $query->count();

        // Get the first 5 unread notifications.
        $notifications = $query->limit(10)->get();

        if (! $notifications->isEmpty()) {
            $lastId = $notifications->first()->id;
        } else {
            $lastId = 0;
        }

        $items = array();

        foreach ($notifications as $notification) {
            $items[] = array_merge(array('id' => $notification->uuid), $notification->data);
        }

        return Response::json(array(
            'count'  => $count,
            'lastId' => $lastId,
            'items'  => $items,
        ));
    }
}
