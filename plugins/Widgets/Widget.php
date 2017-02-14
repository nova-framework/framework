<?php

namespace Plugins\Widgets;


abstract class Widget
{
    /**
     * Handle the widget instance.
     *
     * @return mixed
     */
    abstract public function handle();

    /**
     * Register parameters for use within the class.
     *
     * @param  array  $parameters
     */
    public function registerParameters($parameters)
    {
        foreach ($parameters as $parameter => $value) {
            if (property_exists($this, $parameter)) {
                $this->${$parameter} = $value;
            }
        }
    }
}
