<?php

namespace Shared\Notifications;

use Nova\Support\ServiceProvider;

use Shared\Notifications\Console\NotificationMakeCommand;
use Shared\Notifications\Console\NotificationTableCommand;
use Shared\Notifications\Console\ModuleNotificationMakeCommand;
use Shared\Notifications\DispatcherInterface;
use Shared\Notifications\ChannelManager;


class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the Notifications plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerChannelManager();

        $this->registerCommands();
    }

    protected function registerChannelManager()
    {
        $this->app->bindShared('notifications', function ($app)
        {
            return new ChannelManager($app, $app['events']);
        });
    }

    protected function registerCommands()
    {
        $this->app->singleton('command.notification.table', function ($app) {
            return new NotificationTableCommand($app['files']);
        });

        $this->app->singleton('command.notification.make', function ($app)
        {
            return new NotificationMakeCommand($app['files']);
        });

        $this->app->singleton('command.make.module.notification', function ($app)
        {
            return new ModuleNotificationMakeCommand($app['files'], $app['modules']);
        });

        $this->commands(
            'command.notification.make', 'command.notification.table', 'command.make.module.notification'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'notifications'
        );
    }
}
