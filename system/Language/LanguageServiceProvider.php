<?php

namespace Language;

use Language\LanguageManager;
use Support\ServiceProvider;


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
        $locale = $this->app['config']['app.locale'];

        $session = $this->app['session'];
       
        $cookie = $this->app['request']->cookie(PREFIX .'language', null);
       
        if ($session->has('language')) {
            // The Language was already setup on Session Store.
        } else if (! is_null($cookie)) {
            $session->set('language', $cookie);
        } else {
            $session->set('language', $locale);
        }

        $locale = $session->get('language', $locale);
        
        $this->app['language']->setLocale($locale); 
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
