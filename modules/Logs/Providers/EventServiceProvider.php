<?php

namespace Logs\Providers;

use Nova\Events\Dispatcher;
use Nova\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Logs\Observers\UserActionsObserver;

use System\Models\Role;
use Users\Models\User;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = array(
        'Logs\Events\SomeEvent' => array(
            'Logs\Listeners\EventListener',
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

        //
        $observer = new UserActionsObserver();

        User::observe($observer);
        Role::observe($observer);
    }
}
