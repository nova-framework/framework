<?php

namespace Shared\Database\Backup;

use Shared\Database\Backup\Console\BackupCommand;
use Shared\Database\Backup\Console\RestoreCommand;
use Shared\Database\Backup\DatabaseBuilder;

use Nova\Support\ServiceProvider;


class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
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
        if (! $this->app->runningInConsole()) {
            return;
        }

        $builder = new DatabaseBuilder();

        $this->app['command.db.backup'] = $this->app->share(function($app) use ($builder)
        {
            return new BackupCommand($builder);
        });

        $this->app['command.db.restore'] = $this->app->share(function($app) use ($builder)
        {
            return new RestoreCommand($builder);
        });

        $this->commands('command.db.backup', 'command.db.restore');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        if (! $this->app->runningInConsole()) {
            return array();
        }

        return array(
            'command.db.backup', 'command.db.restore'
        );
    }
}
