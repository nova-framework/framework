<?php

namespace Plugins\Widgets;

use Nova\Foundation\Application;
use Nova\Support\Str;

use Plugins\Widgets\Exception\InvalidWidgetException;


class Factory
{
    /**
     * @var \Nova\Foundation\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $namespaces = array();


    /**
     * Create a new factory instance.
     *
     * @param  Nova\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register a new namespace location where widgets may be found.
     *
     * @param  string  $namespace
     */
    public function register($namespace)
    {
        if (array_search($namespace, $this->namespaces) === false) {
            array_push($this->namespaces, $namespace);
        }
    }

    /**
     * Create a new Widget instance.
     *
     * @param  string  $signature
     * @param array $parameters
     *
     * @return \Nova\Widget\Widget
     */
    public function make($signature, array $parameters = array())
    {
        $className = Str::studly($signature);

        $namespace = $this->determineNamespace($className);

        $widgetClass = $namespace .'\\' .$className;

        $widget = $this->app->make($widgetClass);

        $widget->registerParameters($parameters);

        return $widget;
    }

    /**
     * Handle a Widget instance.
     *
     * @param  \Nova\Widget\Widget $widget
     * @param array $parameters
     *
     * @return mixed
     * @throws \Nova\Widget\InvalidWidgetException
     */
    public function handle($widget, array $parameters = array())
    {
        $parameters = $this->flattenParameters($parameters);

        if (! $widget instanceof Widget) {
            throw new InvalidWidgetException();
        }

        $widget->registerParameters($parameters);

        return $widget->handle();
    }

    /**
     * Determine the full namespace for the given class.
     *
     * @param  string  $className
     * @return string
     */
    protected function determineNamespace($className)
    {
        foreach ($this->namespaces as $namespace) {
            if (class_exists($namespace .'\\' .$className)) {
                return $namespace;
            }
        }

        return 'App\\Widgets';
    }

    /**
     * Flattens the given array.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function flattenParameters(array $parameters)
    {
        $flattened = array();

        foreach($parameters as $parameter) {
            array_walk($parameter, function($value, $key) use (&$flattened)
            {
                $flattened[$key] = $value;
            });
        }

        return $flattened;
    }

    /**
     * Returns the defined namespaces.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Magic method to call widget instances.
     *
     * @param  string  $signature
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($signature, $parameters)
    {
        $widget = $this->make($signature);

        return $this->handle($widget, $parameters);
    }
}
