<?php

namespace Shared\Notifications;


interface DispatcherInterface
{
    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Nova\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification);
}
