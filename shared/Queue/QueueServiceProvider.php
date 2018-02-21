<?php

namespace Shared\Queue;

use Nova\Support\ServiceProvider;

use Shared\Queue\Connectors\AsyncConnector;
use Shared\Queue\Console\AsyncCommand;


class QueueServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the Application Events.
     *
     * @return void
     */
    public function boot()
    {
        $manager = $this->app['queue'];

        $this->registerAsyncConnector($manager);

        $this->commands('command.queue.async');
    }

    /**
     * Register the AsyncQueue plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAsyncCommand();
    }

    /**
     * Register the queue async command.
     *
     * @return void
     */
    protected function registerAsyncCommand()
    {
        $this->app->bindShared('command.queue.async', function()
        {
            return new AsyncCommand($this->app['queue.worker']);
        });
    }

    /**
     * Register the database queue connector.
     *
     * @param  \Nova\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerAsyncConnector($manager)
    {
        $manager->addConnector('async', function ()
        {
            return new AsyncConnector($this->app['db']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('command.queue.async');
    }
}
