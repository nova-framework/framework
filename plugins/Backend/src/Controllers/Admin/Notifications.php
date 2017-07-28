<?php

namespace Backend\Controllers\Admin;

use Nova\Support\Facades\Auth;

use Backend\Controllers\BaseController;

use Backend\Models\User;


class Notifications extends BaseController
{

    public function index()
    {
        $perPage = 15;

        //
        $authUser = Auth::user();

        //Retrieve the unread notifications for the current User.
        $unreadNotifications = $authUser->unreadNotifications();

        // Recalculate the notifications count.
        $count = $unreadNotifications->count();

        $notificationCount = ($count > $perPage) ? ($count - $perPage) : 0;

        // Paginate the notifications.
        $notifications = $unreadNotifications->paginate($perPage);

        // Mark all notifications as read.
        $notifications->each(function ($notification)
        {
            $notification->markAsRead();
        });

        return $this->createView()
            ->shares('title', __d('backend', 'Notifications'))
            ->shares('notificationCount', $notificationCount)
            ->with(compact('authUser', 'notifications'));
    }

}
