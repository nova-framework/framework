<?php

namespace Nova\Routing\Matching;

use Nova\Http\Request;
use Nova\Routing\Route;


class SchemeValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Nova\Routing\Route  $route
     * @param  \Nova\Http\Request  $request
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
