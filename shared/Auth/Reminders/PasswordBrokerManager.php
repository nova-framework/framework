<?php

namespace Shared\Auth\Reminders;

use Nova\Foundation\Application;
use Nova\Support\Str;

use Shared\Auth\Reminders\DatabaseReminderRepository;
use Shared\Auth\Reminders\PasswordBroker;

use InvalidArgumentException;


class PasswordBrokerManager
{
    /**
     * The application instance.
     *
     * @var \Nova\Foundation\Application
     */
    protected $app;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $brokers = array();


    /**
     * Create a new PasswordBroker manager instance.
     *
     * @param  \Nova\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Attempt to get the broker from the local cache.
     *
     * @param  string  $name
     * @return \Shared\Auth\Reminders\PasswordBroker
     */
    public function broker($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();


        if (! isset($this->brokers[$name])) {
            $this->brokers[$name] = $this->resolve($name);
        }

        return $this->brokers[$name];
    }

    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     * @return \Shared\Auth\Reminders\PasswordBroker
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        // The password broker uses a token repository to validate tokens and send user
        // password e-mails, as well as validating that password reset process as an
        // aggregate service of sorts providing a convenient interface for resets.
        return new PasswordBroker(
            $this->createReminderRepository($config),
            $this->app['auth']->createUserProvider($config['provider']),
            $this->app['config']->get('app.key')
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return \Shared\Auth\Reminders\ReminderRepositoryInterface
     */
    protected function createReminderRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        $connection = isset($config['connection']) ? $config['connection'] : null;

        return new DatabaseReminderRepository(
            $this->app['db']->connection($connection),
            $config['table'],
            $key,
            $config['expire']
        );
    }

    /**
     * Get the password broker configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["auth.reminders.{$name}"];
    }

    /**
     * Get the default password broker name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['auth.defaults.reminder'];
    }

    /**
     * Set the default password broker name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['auth.defaults.reminder'] = $name;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->broker(), $method], $parameters);
    }
}
