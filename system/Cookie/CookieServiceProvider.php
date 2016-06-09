<?php
/**
 * CookieServiceProvider - Implements a Service Provider for CookieJar.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Cookie;

use Cookie\CookieJar;
use Support\ServiceProvider;


class CookieServiceProvider extends ServiceProvider
{
    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('cookie', function($app)
        {
            $config = $app['config']['session'];

            return with(new CookieJar())->setDefaultPathAndDomain($config['path'], $config['domain']);
        });
    }
}
