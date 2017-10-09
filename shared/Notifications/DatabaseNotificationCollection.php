<?php

namespace Shared\Notifications;

use Nova\Database\ORM\Collection;


class DatabaseNotificationCollection extends Collection
{
    /**
     * Mark all notification as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->each(function ($notification)
        {
            $notification->markAsRead();
        });
    }
}
