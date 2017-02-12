<?php

namespace Plugins\Backup\Providers;

use Plugins\Backup\Console\BackupCommand;
use Plugins\Backup\Console\RestoreCommand;
use Plugins\Backup\DatabaseBuilder;

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
        $this->package('Plugins/Backup', 'backup', $path);

        // Configure the Services.
        if (! $this->app->runningInConsole()) return;

        //
        $databaseBuilder = new DatabaseBuilder();

        $this->app['db.backup'] = $this->app->share(function($app) use ($databaseBuilder)
        {
            return new BackupCommand($databaseBuilder);
        });

        $this->app['db.restore'] = $this->app->share(function($app) use ($databaseBuilder)
        {
            return new RestoreCommand($databaseBuilder);
        });

        $this->commands('db.backup', 'db.restore');
    }
}
