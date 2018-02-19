<?php

namespace Modules\Users\Providers;

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
        'Modules\Users\Events\MetaFields\UserEditing' => array(
            'Modules\Users\Listeners\MetaFields@edit',
        ),
        'Modules\Users\Events\MetaFields\UserValidation' => array(
            'Modules\Users\Listeners\MetaFields@validate',
        ),
        'Modules\Users\Events\MetaFields\UserSaving' => array(
            'Modules\Users\Listeners\MetaFields@save',
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
        $path = realpath(__DIR__ .'/../');

        // Load the Events.
        $path = $path .DS .'Events.php';

        $this->loadEventsFrom($path);
    }
}
