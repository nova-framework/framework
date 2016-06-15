<?php

namespace Filesystem;

use Support\ServiceProvider;

use Filesystem\Filesystem;


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
