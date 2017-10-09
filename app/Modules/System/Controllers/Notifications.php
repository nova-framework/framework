<?php

namespace App\Modules\System\Controllers;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Response;

use App\Modules\System\Controllers\BaseController;


class Notifications extends BaseController
{

    public function data()
    {
        $authUser = Auth::user();

        //Retrieve the unread notifications for the current User.
        $query = $authUser->unreadNotifications();

        // Get the total number of unread notifications.
        $count = $query->count();

        // Get the first 5 unread notifications.
        $notifications = $query->limit(5)->get();

        return Response::json(array(
            'total' => $count,
            'items' => $notifications->toArray()
        ));
    }
}
