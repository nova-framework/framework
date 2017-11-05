<?php

namespace Shared\Widgets;

use Nova\Container\Container;


class WidgetManager
{
    /**
     * The container implementation.
     *
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * Classes registered widgets
     *
     * @var array
     */
    protected $widgets = array();

    /**
     * Prepared instances of widgets
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Positions for widgets
     *
     * @var array
     */
    protected $positions = array();


    /**
     * Create a new Widget Manager instance.
     *
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container ?: new Container();
    }

    /**
     * Register a new Widget.
     *
     * @param  string $widget
     * @param  string $name
     * @param  string|null $position
     * @param  int $order
     * @return void
     */
    public function register($widget, $name, $position = null, $order = 0)
    {
        $this->widgets[$name] = $widget;

        if (! is_null($position)) {
            $this->positions[$position][] = compact('name', 'order');
        }
    }

    /**
     * Render a registered Widget.
     *
     * @param  string $name
     * @return mixed|null
     */
    public function show($name)
    {
        $parameters = array_slice(func_get_args(), 1);

        return $this->render($name, $parameters);
    }

    protected function render($name, array $parameters = array())
    {
        if (! array_key_exists($name, $this->widgets)) {
            return;
        }

        $instance = $this->getWidget($name);

        return call_user_func_array(array($instance, 'render'), $parameters);
    }

    protected function getWidget($name)
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $widget = $this->widgets[$name];

        return $this->instances[$name] = $this->container->make($widget);
    }

    public function position($position)
    {
        $parameters = array_slice(func_get_args(), 1);

        if (! array_key_exists($position, $this->positions)) {
            return;
        }

        usort($this->positions[$position], function ($a, $b)
        {
            if ($a['order'] == $b['order']) return 0;

            return ($a['order'] > $b['order']) ? 1 : -1;
        });

        // We render each registered Widget for this position.
        $result = '';

        foreach ($this->positions[$position] as $widget) {
            $name = $widget['name'];

            $result .= $this->render($name, $parameters);
        }

        return $result;
    }

    public function exists($name)
    {
        return array_key_exists($name, $this->widgets);
    }

    public function isEmptyPosition($position)
    {
        if (! array_key_exists($position, $this->positions)) {
            return true;
        } else if (count($this->positions[$position]) === 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $method
     * @param array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        return $this->render($method, $parameters);
    }
}
