<?php

namespace Routing\Matching;

use Http\Request;
use Routing\Route;


class MethodValidator implements ValidatorInterface
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
        return in_array($request->getMethod(), $route->methods());
    }

}
