<?php

namespace AcmeCorp\Notifications\Channels;

use Nova\Events\Dispatcher;

use AcmeCorp\Notifications\Messages\BroadcastMessage;
use AcmeCorp\Notifications\Events\BroadcastNotificationCreated;
use AcmeCorp\Notifications\Notification;

use RuntimeException;


class BroadcastChannel
{
    /**
     * The event dispatcher.
     *
     * @var \Nova\Events\Dispatcher
     */
    protected $events;

    /**
     * Create a new database channel.
     *
     * @param  \Nova\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \AcmeCorp\Notifications\Notification  $notification
     * @return array|null
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $this->getData($notifiable, $notification);

        $event = new BroadcastNotificationCreated(
            $notifiable, $notification, is_array($message) ? $message : $message->data
        );

        if ($message instanceof BroadcastMessage) {
            $event->onConnection($message->connection)
                  ->onQueue($message->queue);
        }

        return $this->events->fire($event);
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  \AcmeCorp\Notifications\Notification  $notification
     * @return mixed
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toBroadcast')) {
            return $notification->toBroadcast($notifiable);
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException(
            'Notification is missing toArray method.'
        );
    }
}
