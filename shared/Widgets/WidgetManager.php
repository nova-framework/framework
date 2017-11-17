<?php

namespace Shared\Widgets;

use Nova\Http\Request;
use Nova\Container\Container;
use Nova\Support\Arr;


class WidgetManager
{
    /**
     * The Container instance.
     *
     * @var \Nova\Container\Container
     */
    protected $container;

    /**
     * The Request instance.
     *
     * @var \Nova\Http\Request
     */
    protected $request;

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
    public function __construct(Request $request, Container $container = null)
    {
        $this->container = $container ?: new Container();

        $this->request = $request;
    }

    /**
     * Register a new Widget.
     *
     * @param  string $widget
     * @param  string $name
     * @param  string|null $position
     * @param  int $order
     * @param  array|string $parameters
     * @return void
     */
    public function register($widget, $name, $position = null, $order = 0, $parameters = array())
    {
        if (is_string($parameters)) {
            $parameters = array_map('trim', explode(',', $parameters));
        }

        $this->widgets[$name] = compact('widget', 'parameters');

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

        extract($this->widgets[$name]);

        return $this->instances[$name] = $this->container->make($widget, $parameters);
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

            $config = $this->container['config']->get('widgets.widgets.' .$name, array());

            // Calculate the widget visibility, then skip its rendering if is not visible.
            $mode = Arr::get($config, 'mode', 'show');

            $patterns = Arr::get($config, 'paths', array('*'));

            $pathMatches = call_user_func_array(array($this->request, 'is'), $patterns);

            if (($pathMatches && ($mode == 'hide')) || (! $pathMatches && ($mode == 'show'))) {
                continue;
            }

            // The User is authorized to see this Widget?
            else if (! $this->widgetFiltersAllows($config)) {
                continue;
            }

            $result .= $this->render($name, $parameters);
        }

        return $result;
    }

    protected function widgetFiltersAllows(array $config)
    {
        $filters = Arr::get($config, 'filters', array());

        foreach ($filters as $filter) {
            list ($type, $parameter) = array_pad(explode(':', $filter, 2), 2, null);

            $guards = array_filter(array_map('trim', explode(',', $parameter)), function ($value)
            {
                return ! empty($value);
            });

            if (empty($guards)) {
                $guards[] = $this->container['config']->get('auth.defaults.guard');
            }

            foreach ($guards as $guard) {
                $auth = $this->container['auth']->guard($guard);

                // Authenticated users only?
                if (($type === 'auth') && $auth->guest()) {
                    return false;
                }

                // Guests only?
                else if (($type === 'guest') && $auth->check()) {
                    return false;
                }
            }
        }

        return true;
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
