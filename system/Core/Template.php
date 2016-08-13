<?php
/**
 * Template - a View specialized for handling the Template files.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Core\View;
use Template\Factory;


class Template
{
    /**
     * @var \View\Template
     */
    private static $factory;

    /**
     * Return a View Factory instance
     *
     * @return \Vview\Factory
     */
    protected static function getFactory()
    {
        if (! isset(static::$factory)) {
            $viewFactory = View::getFactory();

            static::$factory = new Factory($viewFactory);
        }

        return static::$factory;
    }

    /**
     * Magic Method for calling the methods on the Factory instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getFactory();

        // Call the non-static method from the Template Factory instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
