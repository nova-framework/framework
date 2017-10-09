<?php

namespace Shared\Notifications\Channels;

use Shared\Notifications\Notification;

use RuntimeException;


class DatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Shared\Notifications\Notification  $notification
     * @return \Nova\Database\ORM\Model
     */
    public function send($notifiable, Notification $notification)
    {
        return $notifiable->routeNotificationFor('database')->create(array(
            'uuid'        => $notification->id,
            'type'        => get_class($notification),
            'data'        => $this->getData($notifiable, $notification),
            'read_at'    => null,
        ));
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  \Shared\Notifications\Notification  $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toDatabase')) {
            $data = $notification->toDatabase($notifiable);

            return is_array($data) ? $data : $data->data;
        } else if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException('Notification is missing toDatabase / toArray method.');
    }
}
