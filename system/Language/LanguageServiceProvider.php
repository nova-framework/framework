<?php

namespace Nova\Language;

use Nova\Language\LanguageManager;
use Nova\Support\ServiceProvider;


class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $config = $this->app['config'];

        $session = $this->app['session'];

        $request = $this->app['request'];

        //
        $cookie = $request->cookie(PREFIX .'language', null);

        if ($session->has('language')) {
            // The Language was already setup on Session.
        } else if (! is_null($cookie)) {
            $session->set('language', $cookie);
        } else {
            $session->set('language', $config->get('app.locale'));
        }

        // Always retrieve the current locale from Session.
        $lang = $session->get('language');

        $locale = $config->get('languages.' .$lang .'.locale', 'en_US');

        setlocale(LC_TIME, $locale .'.utf8', $lang);

        $this->app['language']->setLocale($lang);
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('language', function($app)
        {
            return new LanguageManager($app, $app['config']['app.locale']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('language');
    }
}
