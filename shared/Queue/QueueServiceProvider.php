<?php

namespace Shared\Queue;

use Nova\Support\ServiceProvider;

use Shared\Queue\Connectors\AsyncConnector;
use Shared\Queue\Console\AsyncCommand;
use Shared\Queue\Console\BatchCommand;
use Shared\Queue\BatchRunner;


class QueueServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
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
        $manager = $this->app['queue'];

        $this->registerAsyncConnector($manager);

        $this->commands('command.queue.batch', 'command.queue.async');
    }

    /**
     * Register the AsyncQueue plugin Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBatchRunner();

        //
        $this->registerBatchCommand();
        $this->registerAsyncCommand();
    }

    /**
     * Register the queue worker.
     *
     * @return void
     */
    protected function registerBatchRunner()
    {
        $this->app->singleton('queue.batch.runner', function($app)
        {
            return new BatchRunner($app['queue'], $app['queue.failer'], $app['events']);
        });
    }

    /**
     * Register the queue async command.
     *
     * @return void
     */
    protected function registerBatchCommand()
    {
        $this->app->singleton('command.queue.batch', function()
        {
            return new BatchCommand($this->app['queue.batch.runner']);
        });
    }

    /**
     * Register the queue async command.
     *
     * @return void
     */
    protected function registerAsyncCommand()
    {
        $this->app->singleton('command.queue.async', function()
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
        return array('command.queue.batch', 'command.queue.async');
    }
}
