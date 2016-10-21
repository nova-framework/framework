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

        if (! $session->has('language')) {
            $cookie = $this->app['request']->cookie(PREFIX .'language', null);

            if (! is_null($cookie)) {
                $session->set('language', $cookie);
            } else {
                $session->set('language', $config->get('app.locale'));
            }
        }

        $language = $session->get('language');

        $this->app['language']->setLocale($language);
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
