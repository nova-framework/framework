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
        'Modules\Users\Events\MetaFields\UpdateUserValidation' => array(
            'Modules\Users\Listeners\MetaFields@updateValidator',
        ),
        'Modules\Users\Events\MetaFields\UserEditing' => array(
            'Modules\Users\Listeners\MetaFields@edit',
        ),
        'Modules\Users\Events\MetaFields\UserSaving' => array(
            'Modules\Users\Listeners\MetaFields@save',
        ),
        'Modules\Users\Events\MetaFields\UserShowing' => array(
            'Modules\Users\Listeners\MetaFields@show',
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

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        //
        $this->app->singleton('Modules\Users\Listeners\MetaFields');
    }
}
