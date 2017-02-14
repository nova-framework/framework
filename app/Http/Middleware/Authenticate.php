<?php

namespace App\Http\Middleware;

use Nova\Auth\Middleware\Authenticate as BaseAuthenticator;
use Nova\Foundation\Application;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Redirect;

use Closure;


class Authenticate extends BaseAuthenticator
{
    /**
     * The URI where are redirected the Guests.
     *
     * @var string
     */
    protected $guestUri = 'login';

}
