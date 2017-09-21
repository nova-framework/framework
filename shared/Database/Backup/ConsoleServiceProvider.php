<?php

namespace Shared\Database\Backup;

use Shared\Database\Backup\Console\BackupCommand;
use Shared\Database\Backup\Console\RestoreCommand;
use Shared\Database\Backup\DatabaseBuilder;

use Nova\Support\ServiceProvider;


class ConsoleServiceProvider extends ServiceProvider
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
        if (! $this->app->runningInConsole()) {
            return;
        }

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
