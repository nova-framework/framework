<?php

namespace Widgets\Support;

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
		if (! array_key_exists($name, $this->widgets)) {
			return;
		}

		if (! array_key_exists($name, $this->instances)) {
			$widget = $this->widgets[$name];

			$instance = $this->container->make($widget);

			$this->addInstance($instance, $name);
		} else {
			$instance = $this->instances[$name];
		}

		$parameters = array_slice(func_get_args(), 1);

		return call_user_func_array(array($instance, 'render'), $parameters);
	}

	public function position($position)
	{
		if (! array_key_exists($position, $this->positions)) {
			return;
		}

		usort($this->positions[$position], function ($a, $b)
		{
			if ($a['order'] == $b['order']) return 0;

			return ($a['order'] > $b['order']) ? -1 : 1;
		});

		$arguments = array_slice(func_get_args(), 1);

		// We render each registered Widget for this position.
		$result = '';

		foreach ($this->positions[$position] as $widget) {
			$parameters = $arguments;

			array_unshift($parameters, $widget['name']);

			$result .= call_user_func_array(array($this, 'show'), $parameters);
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
		} else if (! count($this->positions[$position])) {
			return true;
		}

		return false;
	}

	/**
	 * @param		$widget
	 * @param string $name
	 * @return void
	 */
	protected function addInstance($widget, $name)
	{
		$this->instances[$name] = $widget;
	}

	/**
	 * @param string $method
	 * @param array  $arguments
	 * @return mixed
	 */
	public function __call($method, array $arguments)
	{
		array_unshift($arguments, $method);

		return call_user_func_array(array($this, 'show'), $arguments);
	}
}

