<?php

namespace Modules\System\Traits;

use Modules\System\Models\Notification;


trait HasNotificationsTrait
{
    /**
     * Relationship between Notification and User.
     */

    public function notifications()
    {
        return $this->hasMany('Modules\System\Models\Notification', 'user_id');
    }

    public function newNotification()
    {
        $notification = new Notification();

        $notification->user()->associate($this);

        return $notification;
    }

}
