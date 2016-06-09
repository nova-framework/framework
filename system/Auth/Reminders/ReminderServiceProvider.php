<?php

namespace Auth\Reminders;

use Support\ServiceProvider;
use Auth\PasswordBroker;
use Auth\Reminders\ReminderRepository;


class ReminderServiceProvider extends ServiceProvider
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
        $this->registerPasswordBroker();

        $this->registerReminderRepository();
    }

    /**
     * Register the Password Broker instance.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->bindShared('auth.reminder', function($app)
        {
            $reminders = $app['auth.reminder.repository'];

            $users = $app['auth']->driver()->getProvider();

            $view = $app['config']['auth.reminder.email'];

            return new PasswordBroker($reminders, $users, $app['mailer'], $view);
        });
    }

    /**
     * Register the reminder repository implementation.
     *
     * @return void
     */
    protected function registerReminderRepository()
    {
        $this->app->bindShared('auth.reminder.repository', function($app)
        {
            $connection = $app['db']->connection();

            $table = $app['config']['auth.reminder.table'];

            $key = $app['config']['app.key'];

            $expire = $app['config']->get('auth.reminder.expire', 60);

            return new DatabaseRepository($connection, $table, $key, $expire);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('auth.reminder', 'auth.reminder.repository');
    }

}
