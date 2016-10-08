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

        $language = $this->app['language'];
        
        $session = $this->app['session'];
       
        $cookie = $this->app['request']->cookie(PREFIX .'language');
       
        if ($session->has('language')) {
            $locale = $session->get('language', $locale);
        } else if(! is_null($cookie)) {
            $session->set('language', $cookie);
            
            $language->setLocale($cookie);  
            
            return;
        }
        
        $language->setLocale($locale); 
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
