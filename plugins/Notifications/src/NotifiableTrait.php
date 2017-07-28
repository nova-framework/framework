<?php

namespace Notifications;

use Nova\Support\Facades\App;
use Nova\Support\Facades\Config;
use Nova\Support\Str;


trait NotifiableTrait
{
    /**
     * Get the entity's notifications.
     */
    public function notifications()
    {
        return $this->morphMany('Notifications\Models\Notification', 'notifiable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the entity's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->morphMany('Notifications\Models\Notification', 'notifiable')
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance)
    {
        $dispatcher = App::make('Notifications\Contracts\DispatcherInterface');

        return $dispatcher->send(array($this), $instance);
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function routeNotificationFor($driver)
    {
        if (method_exists($this, $method = 'routeNotificationFor'. Str::studly($driver))) {
            return call_user_func(array($this, $method));
        }

        switch ($driver) {
            case 'database':
                return $this->notifications();

            case 'mail':
                if (preg_match('/^\w+@\w+\.dev$/s', $this->email)) {
                    return Config::get('mail.from.address');
                }

                return $this->email;
        }
    }

}
