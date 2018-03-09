<?php

use Shared\Support\Facades\Action;
use Shared\Support\Facades\Filter;

//----------------------------------------------------------------------
// Custom Helpers
//----------------------------------------------------------------------

if (! function_exists('add_filter'))
{
    /**
     * @param $hook
     * @param $callback
     * @param int $priority
     * @param int $arguments
     */
    function add_filter($hook, $callback, $priority = 20, $arguments = 1)
    {
        Filter::listen($hook, $callback, $priority, $arguments);
    }
}

if (! function_exists('apply_filters'))
{
    /**
     * @return mixed
     */
    function apply_filters()
    {
        $parameters = func_get_args();

        $action = array_shift($parameters);

        return Filter::fire($action, $parameters);
    }
}

if (! function_exists('add_action'))
{
    /**
     * @param $hook
     * @param $callback
     * @param int $priority
     * @param int $arguments
     */
    function add_action($hook, $callback, $priority = 20, $arguments = 1)
    {
        Action::listen($hook, $callback, $priority, $arguments);
    }
}

if (! function_exists('do_action'))
{
    /**
     * @return void
     */
    function do_action()
    {
        $parameters = func_get_args();

        $action = array_shift($parameters);

        Action::fire($action, $parameters);
    }
}
