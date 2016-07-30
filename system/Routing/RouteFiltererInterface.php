<?php

namespace Routing;


interface RouteFiltererInterface
{
    /**
     * Register a new filter with the router.
     *
     * @param  string  $name
     * @param  mixed  $callback
     * @return void
     */
    public function filter($name, $callback);

}
