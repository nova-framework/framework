<?php

namespace Routing\Matching;

use Http\Request;
use Routing\Route;


class HostValidator implements ValidatorInterface
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
        $hostRegex = $route->getCompiled()->getHostRegex();

        if (is_null($hostRegex)) return true;

        return preg_match($hostRegex, $request->getHost());
    }

}
