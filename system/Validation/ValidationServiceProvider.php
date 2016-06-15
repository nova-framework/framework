<?php
/**
 * ValidationServiceProvider - Implements a Service Provider for Validation.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Validation;

use Validation\DatabasePresenceVerifier;
use Validation\Factory;
use Validation\Translator;
use Support\ServiceProvider;


class ValidationServiceProvider extends ServiceProvider
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
        $this->registerTranslator();

        $this->registerPresenceVerifier();

        $this->app->bindShared('validator', function($app)
        {
            // Get a Validation Factory instance.
            $validator = new Factory($app['validation.translator']);

            if (isset($app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }

            return $validator;
        });
    }

    /**
     * Register the Database Presence Verifier.
     *
     * @return void
     */
    protected function registerPresenceVerifier()
    {
        $this->app->bindShared('validation.presence', function($app)
        {
            $connection = $app['db']->connection();

            return new DatabasePresenceVerifier($connection);
        });
    }

    /**
     * Register the Database Presence Verifier.
     *
     * @return void
     */
    protected function registerTranslator()
    {
        $this->app->bindShared('validation.translator', function($app)
        {
            return new Translator();
        });
    }

    /**
     * Get the services provided by the Provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('validator', 'validation.presence', 'validation.translator');
    }
}
