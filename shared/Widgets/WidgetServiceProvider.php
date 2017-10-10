<?php

namespace Shared\Widgets;

use Nova\Foundation\AliasLoader;
use Nova\Support\ServiceProvider;

use Shared\Widgets\WidgetManager;


class WidgetServiceProvider extends ServiceProvider
{

    /**
     * Boot the Service Provider.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('widgets', function($app)
        {
            return new WidgetManager($app);
        });
    }
}
