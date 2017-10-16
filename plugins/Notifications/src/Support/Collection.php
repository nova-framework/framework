<?php

namespace AcmeCorp\Notifications\Support;

use Nova\Database\ORM\Collection as BaseCollection;


class Collection extends BaseCollection
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
