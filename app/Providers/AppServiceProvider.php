<?php

namespace App\Providers;

use Nova\Foundation\Support\Providers\AppServiceProvider as ServiceProvider;


class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $session = $this->app['session'];

        if (! $session->has('language')) {
            $cookie = $this->app['request']->cookie(PREFIX .'language', null);

            $locale = $cookie ?: $this->app['config']->get('app.locale');

            $session->set('language', $locale);
        } else {
            $locale = $session->get('language');
        }

        $this->app['language']->setLocale($locale);
    }

    /**
     * Register the Application's Service Provider.
     *
     * This service provider is a convenient place to register your modules
     * services in the IoC container. If you wish, you may make additional
     * methods or service providers to keep the code more focused and granular.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
