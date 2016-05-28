<?php

namespace Events;

use Events\Dispatcher;


abstract class Subscriber
{
    /**
     * Register the Event Subscriber with the Dispatcher.
     *
     * @param  string  $subscriber
     * @return void
     */
    abstract public function subscribe(Dispatcher $dispatcher);

    /**
     * Get the events listened to by the subscriber.
     *
     * @return array
     */
    public static function subscribes()
    {
        return array();
    }

    /**
     * Get the events listened to by the subscriber.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return static::subscribes();
    }

}
