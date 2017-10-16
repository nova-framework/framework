<?php

namespace AcmeCorp\Reminders;

use Nova\Foundation\Application;
use Nova\Support\Str;

use AcmeCorp\Reminders\DatabaseReminderRepository;
use AcmeCorp\Reminders\PasswordBroker;

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
     * @return \Nova\Auth\Reminders\PasswordBroker
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
     * @return \Nova\Auth\Reminders\PasswordBroker
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
            $this->app['mailer'],
            $config['email']
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return \AcmeCorp\Reminders\Contracts\ReminderRepositoryInterface
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
        return $this->app['config']["reminders::reminders.{$name}"];
    }

    /**
     * Get the default password broker name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['reminders::default'];
    }

    /**
     * Set the default password broker name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['reminders::default'] = $name;
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
