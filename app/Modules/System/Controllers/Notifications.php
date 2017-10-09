<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
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

    public function update()
    {
        $authUser = Auth::user();

        return Redirect::back();
    }

    public function data()
    {
        $authUser = Auth::user();

        $lastId = (int) Input::get('lastId', 0);

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

        return Response::json(array(
            'count'  => $count,
            'lastId' => $lastId,
            'items'  => $items,
        ));
    }
}
