<?php

namespace Messages\Providers;

use Nova\Events\Dispatcher;
use Nova\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = array(
        'Messages\Events\SomeEvent' => array(
            'Messages\Listeners\EventListener',
        ),
    );


    /**
     * Register any other events for your application.
     *
     * @param  \Nova\Events\Dispatcher  $events
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        //
        $path = realpath(__DIR__ .'/../') .DS .'Events.php';

        $this->loadEventsFrom($path);
    }
}
