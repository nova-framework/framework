<?php

namespace Routing\Matching;

use Http\Request;
use Routing\Route;


interface ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Routing\Route  $route
     * @param  \Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request);

}
