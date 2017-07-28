<?php

namespace Backup\Providers;

use Backup\Console\BackupCommand;
use Backup\Console\RestoreCommand;
use Backup\DatabaseBuilder;

use Nova\Support\ServiceProvider;


class PluginServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the Application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $path = realpath(__DIR__ .'/../');

        // Configure the Package.
        $this->package('Backup', 'backup', $path);

        // Configure the Services.
        if (! $this->app->runningInConsole()) {
            return;
        }

        //
        $builder = new DatabaseBuilder();

        $this->app['db.backup'] = $this->app->share(function($app) use ($builder)
        {
            return new BackupCommand($builder);
        });

        $this->app['db.restore'] = $this->app->share(function($app) use ($builder)
        {
            return new RestoreCommand($builder);
        });

        $this->commands('db.backup', 'db.restore');
    }
}
