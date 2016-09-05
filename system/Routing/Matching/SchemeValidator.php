<?php

namespace Routing\Matching;

use Http\Request;
use Routing\Route;


class SchemeValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        if ($route->httpOnly()) {
            return ! $request->secure();
        } else if ($route->secure()) {
            return $request->secure();
        }

        return true;
    }

}
