<?php

namespace System\Traits;

use System\Models\Notification;


trait HasNotificationsTrait
{
    /**
     * Relationship between Notification and User.
     */

    public function notifications()
    {
        return $this->hasMany('System\Models\Notification', 'user_id');
    }

    public function newNotification()
    {
        $notification = new Notification();

        $notification->user()->associate($this);

        return $notification;
    }

}
