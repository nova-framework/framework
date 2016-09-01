<?php

namespace Routing;

use Http\Response;
use View\View;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use BadMethodCallException;
use Closure;


abstract class Controller
{
    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout;

    /**
     * The "before" filters registered on the controller.
     *
     * @var array
     */
    protected $beforeFilters = array();

    /**
     * The "after" filters registered on the controller.
     *
     * @var array
     */
    protected $afterFilters = array();

    /**
     * The route filterer implementation.
     *
     * @var \Routing\RouteFiltererInterface
     */
    protected static $filterer;


    /**
     * Register a "before" filter on the controller.
     *
     * @param  \Closure|string  $filter
     * @param  array  $options
     * @return void
     */
    public function beforeFilter($filter, array $options = array())
    {
        $this->beforeFilters[] = $this->parseFilter($filter, $options);
    }

    /**
     * Register an "after" filter on the controller.
     *
     * @param  \Closure|string  $filter
     * @param  array  $options
     * @return void
     */
    public function afterFilter($filter, array $options = array())
    {
        $this->afterFilters[] = $this->parseFilter($filter, $options);
    }

    /**
     * Parse the given filter and options.
     *
     * @param  \Closure|string  $filter
     * @param  array  $options
     * @return array
     */
    protected function parseFilter($filter, array $options)
    {
        $parameters = array();

        $original = $filter;

        if ($filter instanceof Closure) {
            $filter = $this->registerClosureFilter($filter);
        } else if ($this->isInstanceFilter($filter)) {
            $filter = $this->registerInstanceFilter($filter);
        } else {
            list($filter, $parameters) = Route::parseFilter($filter);
        }

        return compact('original', 'filter', 'parameters', 'options');
    }

    /**
     * Register an anonymous controller filter Closure.
     *
     * @param  \Closure  $filter
     * @return string
     */
    protected function registerClosureFilter(Closure $filter)
    {
        $this->getFilterer()->filter($name = spl_object_hash($filter), $filter);

        return $name;
    }

    /**
     * Register a controller instance method as a filter.
     *
     * @param  string  $filter
     * @return string
     */
    protected function registerInstanceFilter($filter)
    {
        $method = substr($filter, 1);

        $this->getFilterer()->filter($filter, array($this, $method));

        return $filter;
    }

    /**
     * Determine if a filter is a local method on the controller.
     *
     * @param  mixed  $filter
     * @return boolean
     *
     * @throws \InvalidArgumentException
     */
    protected function isInstanceFilter($filter)
    {
        if (is_string($filter) && str_starts_with($filter, '@')) {
            $method = substr($filter, 1);

            if (method_exists($this, $method)) return true;

            throw new \InvalidArgumentException("Filter method [$filter] does not exist.");
        }

        return false;
    }

    /**
     * Remove the given before filter.
     *
     * @param  string  $filter
     * @return void
     */
    public function forgetBeforeFilter($filter)
    {
        $this->beforeFilters = $this->removeFilter($filter, $this->getBeforeFilters());
    }

    /**
     * Remove the given after filter.
     *
     * @param  string  $filter
     * @return void
     */
    public function forgetAfterFilter($filter)
    {
        $this->afterFilters = $this->removeFilter($filter, $this->getAfterFilters());
    }

    /**
     * Remove the given controller filter from the provided filter array.
     *
     * @param  string  $removing
     * @param  array  $current
     * @return array
     */
    protected function removeFilter($removing, $current)
    {
        return array_filter($current, function($filter) use ($removing)
        {
            return $filter['original'] != $removing;
        });
    }

    /**
     * Get the registered "before" filters.
     *
     * @return array
     */
    public function getBeforeFilters()
    {
        return $this->beforeFilters;
    }

    /**
     * Get the registered "after" filters.
     *
     * @return array
     */
    public function getAfterFilters()
    {
        return $this->afterFilters;
    }

    /**
     * Get the route filterer implementation.
     *
     * @return \Routing\RouteFiltererInterface
     */
    public static function getFilterer()
    {
        return static::$filterer;
    }

    /**
     * Set the route filterer implementation.
     *
     * @param  \Routing\RouteFiltererInterface  $filterer
     * @return void
     */
    public static function setFilterer(RouteFiltererInterface $filterer)
    {
        static::$filterer = $filterer;
    }

    /**
     * Create the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {}

    /**
     * Execute an action on the controller.
     *
     * @param string  $method
     * @param array   $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        // Execute the requested Method with the given arguments.
        $response = call_user_func_array(array($this, $method), $parameters);

        // If no response is returned from the controller action and a layout is being
        // used we will assume we want to just return the Layout view as any nested
        // Views were probably bound on this view during this Controller actions.
        if (is_null($response) && ($this->layout instanceof View)) {
            $response = $this->layout;
        }

        // Process the Response and return it.
        return $this->processResponse($response);
    }

    /**
     * Process the response given by the controller action.
     *
     * @param mixed $response
     *
     * @return mixed
     */
    protected function processResponse($response)
    {
        if (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response;
    }

    /**
     * Handle calls to missing methods on the Controller.
     *
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function missingMethod($parameters = array())
    {
        throw new NotFoundHttpException("Controller method not found.");
    }

    /**
     * Handle calls to missing methods on the Controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

}
