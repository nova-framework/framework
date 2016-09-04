<?php

namespace Routing\Matching;

use Http\Request;
use Routing\Route;


class UriValidator implements ValidatorInterface
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
        $path = ($request->path() == '/') ? '/' : '/' .$request->path();

        return preg_match($route->getRegex(), rawurldecode($path));
    }

}
