<?php

namespace Shared\Database\Backup;

use Shared\Database\Backup\Commands\BackupCommand;
use Shared\Database\Backup\Commands\RestoreCommand;
use Shared\Database\Backup\DatabaseBuilder;

use Nova\Support\ServiceProvider;


class BackupServiceProvider extends ServiceProvider
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
