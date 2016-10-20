<?php

namespace Nova\Filesystem;

use Nova\Filesystem\Filesystem;
use Nova\Support\ServiceProvider;


class FilesystemServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('files', function()
        {
            return new Filesystem();
        });
    }

}
