<?php

namespace App\Modules\System\Traits;

use App\Modules\System\Models\Notification;


trait HasNotificationsTrait
{
    /**
     * Relationship between Notification and User.
     */

    public function notifications()
    {
        return $this->hasMany('App\Modules\System\Models\Notification', 'user_id');
    }

    public function newNotification()
    {
        $notification = new Notification();

        $notification->user()->associate($this);

        return $notification;
    }

}
