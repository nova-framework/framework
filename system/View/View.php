<?php
/**
 * View
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

namespace View;

use Support\Contracts\ArrayableInterface as Arrayable;
use Support\Contracts\RenderableInterface as Renderable;
use Support\MessageBag;
use View\Engines\EngineInterface;
use View\Factory;

use ArrayAccess;
use Exception;


/**
 * View class to load template and views files.
 */
class View implements ArrayAccess, Renderable
{
    /**
     * The View Factory instance.
     *
     * @var \View\Factory
     */
    protected $factory;

    /**
     * The View Engine instance.
     *
     * @var \View\Engines\EngineInterface
     */
    protected $engine;

    /**
     * @var string The given View name.
     */
    protected $view = null;

    /**
     * @var string The path to the View file on disk.
     */
    protected $path = null;

    /**
     * @var array Array of local data.
     */
    protected $data = array();

    /**
     * @var bool Falg marking the View as Template.
     */
    protected $layout = false;


    /**
     * Constructor
     * @param mixed $path
     * @param array $data
     */
    public function __construct(Factory $factory, EngineInterface $engine, $view, $path, $data = array(), $layout = false)
    {
        $this->factory = $factory;
        $this->engine  = $engine;

        //
        $this->view = $view;
        $this->path = $path;

        $this->data = ($data instanceof Arrayable) ? $data->toArray() : (array) $data;

        $this->layout = $layout;
    }

    /**
     * Get the string contents of the View.
     *
     * @param  \Closure  $callback
     * @return string
     */
    public function render(Closure $callback = null)
    {
        $contents = $this->renderContents();

        $response = isset($callback) ? $callback($this, $contents) : null;

        return $response ?: $contents;
    }

    /**
     * Render the View and return the result.
     *
     * @return string
     */
    public function renderContents()
    {
        return $this->engine->get($this->path, $this->gatherData());
    }

    /**
     * Return all variables stored on local and shared data.
     *
     * @return array
     */
    public function gatherData()
    {
        $data = array_merge($this->factory->getShared(), $this->data);

        // All nested Views are evaluated before the main View.
        foreach ($data as $key => $value) {
            if ($value instanceof Renderable) {
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

    /**
     * Add a view instance to the view data.
     *
     * <code>
     *     // Add a View instance to a View's data
     *     $view = View::make('foo')->nest('footer', 'Partials/Footer');
     *
     *     // Equivalent functionality using the "with" method
     *     $view = View::make('foo')->with('footer', View::make('Partials/Footer'));
     * </code>
     *
     * @param  string  $key
     * @param  string  $view
     * @param  array   $data
     * @param  string|null  $module
     * @return View
     */
    public function nest($key, $view, array $data = array(), $module = null)
    {
        if(empty($data)) {
            // The nested View instance inherit parent Data if none is given.
            $data = $this->data;
        }

        return $this->with($key, $this->factory->make($view, $data, $module));
    }

    /**
     * Add a key / value pair to the view data.
     *
     * Bound data will be available to the view as variables.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return View
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Add validation errors to the view.
     *
     * @param  \Nova\Support\Contracts\MessageProviderInterface|array  $provider
     * @return \Nova\View\View
     */
    public function withErrors($provider)
    {
        if ($provider instanceof MessageProviderInterface) {
            $this->with('errors', $provider->getMessageBag());
        } else {
            $this->with('errors', new MessageBag((array) $provider));
        }

        return $this;
    }

    /**
     * Add a key / value pair to the shared view data.
     *
     * Shared view data is accessible to every view created by the application.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return View
     */
    public function shares($key, $value)
    {
        $this->factory->share($key, $value);

        return $this;
    }

    /**
     * Get the View Factory instance.
     *
     * @return \Nova\View\Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Return true if the current View instance is a Layout.
     *
     * @return bool
     */
    public function isLayout()
    {
        return $this->layout;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function getName()
    {
        return $this->view;
    }

    /**
     * Get the array of view data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the path to the view file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path to the view.
     *
     * @param  string  $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Implementation of the ArrayAccess offsetExists method.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Implementation of the ArrayAccess offsetGet method.
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
      * Implementation of the ArrayAccess offsetSet method.
      */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * Implementation of the ArrayAccess offsetUnset method.
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Magic Method for handling dynamic data access.
     */
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Magic Method for handling the dynamic setting of data.
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Magic Method for checking dynamically set data.
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Magic Method for handling dynamic functions.
     *
     * @param  string  $method
     * @param  array   $params
     * @return \Nova\View\View|static|void
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $params)
    {
        // Add the support for the dynamic withX Methods.
        if (str_starts_with($method, 'with')) {
            $name = lcfirst(substr($method, 4));

            return $this->with($name, array_shift($params));
        }

        throw new \BadMethodCallException("Method [$method] does not exist on " .get_class($this));
    }


    /**
     * Get the evaluated string content of the View.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return '';
        }
    }

}
