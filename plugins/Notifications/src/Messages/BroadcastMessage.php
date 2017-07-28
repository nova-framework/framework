<?php

namespace Notifications\Messages;

use Nova\Bus\QueueableTrait;


class BroadcastMessage
{
    use QueueableTrait;

    /**
     * The data for the notification.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Set the message data.
     *
     * @param  array  $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }
}
