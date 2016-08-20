<?php

namespace View;

use View\Engines\EngineResolver;
use View\Engines\PhpEngine;
use View\Factory;
use View\FileViewFinder;
use Support\MessageBag;
use Support\ServiceProvider;


class ViewServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEngineResolver();

        $this->registerViewFinder();

        $this->registerFactory();

        $this->registerSessionBinder();
    }

    /**
     * Register the engine resolver instance.
     *
     * @return void
     */
    public function registerEngineResolver()
    {
        $me = $this;

        $this->app->bindShared('view.engine.resolver', function($app) use ($me)
        {
            $resolver = new EngineResolver();

            $me->registerPhpEngine($resolver);

            return $resolver;
        });
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  \View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerPhpEngine($resolver)
    {
        $resolver->register('php', function()
        {
            return new PhpEngine();
        });
    }

    /**
     * Register the View Factory.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->bindShared('view', function($app)
        {
            $resolver = $app['view.engine.resolver'];

            $factory = new Factory($resolver, $app['view.finder']);

            $factory->share('app', $app);

            return $factory;
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->bindShared('view.finder', function($app)
        {
            return new FileViewFinder($app['files']);
        });
    }

    /**
     * Register the session binder for the view environment.
     *
     * @return void
     */
    protected function registerSessionBinder()
    {
        list($app, $me) = array($this->app, $this);

        $app->booted(function() use ($app, $me)
        {
            // If the current session has an "errors" variable bound to it, we will share
            // its value with all view instances so the views can easily access errors
            // without having to bind. An empty bag is set when there aren't errors.
            if ($me->sessionHasErrors($app)) {
                $errors = $app['session.store']->get('errors');

                $app['view']->share('errors', $errors);
            } else {
                $app['view']->share('errors', new MessageBag());
            }
        });
    }

    /**
     * Determine if the application session has errors.
     *
     * @param  \Foundation\Application  $app
     * @return bool
     */
    public function sessionHasErrors($app)
    {
        $config = $app['config']['session'];

        if (isset($app['session.store']) && ! is_null($config['driver'])) {
            return $app['session.store']->has('errors');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('view', 'view.engine.resolver');
    }
}
