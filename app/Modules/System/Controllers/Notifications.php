<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Response;

use App\Modules\System\Controllers\BaseController;


class Notifications extends BaseController
{

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

        return Response::json(array(
            'count'  => $count,
            'lastId' => $lastId,
            'items'  => $notifications->lists('data'),
        ));
    }
}
