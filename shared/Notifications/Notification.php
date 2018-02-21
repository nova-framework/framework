<?php

namespace Shared\Notifications;

use Nova\Queue\SerializesModelsTrait;


class Notification
{
    use SerializesModelsTrait;

    /**
     * The unique identifier for the notification.
     *
     * @var string
     */
    public $id;
}
