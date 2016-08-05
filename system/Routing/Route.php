<?php
/**
 * Route - manage a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Core\Config;
use Http\Request;

use Symfony\Component\HttpFoundation\Response;


/**
 * The Route class is responsible for routing an HTTP request to an assigned Callback function.
 */
class Route
{
    /**
     * @var array Supported HTTP methods
     */
    private $methods = array();

    /**
     * @var string URL pattern
     */
    private $pattern = null;

    /**
     * @var array The route action array.
     */
    protected $action = array();

    /**
     * @var string The current matched URI
     */
    private $uri = null;

    /**
     * @var string The matched HTTP method
     */
    private $method = null;

    /**
     * @var array The matched Route parameters
     */
    private $parameters = array();

    /**
     * @var string Matching regular expression
     */
    private $regex;

    /**
     * Constructor.
     *
     * @param string|array $method HTTP method(s)
     * @param string $pattern URL pattern
     * @param string|array|callable $action Callback function or options
     */
    public function __construct($method, $pattern, $action)
    {
        $this->methods = array_map('strtoupper', is_array($method) ? $method : array($method));

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

        //
        $this->pattern = ! empty($pattern) ? $pattern : '/';

        $this->action = $this->parseAction($action);

        if (isset($this->action['prefix'])) {
            $this->prefix($this->action['prefix']);
        }
    }

    /**
     * Parse the Route Action into a standard array.
     *
     * @param  \Closure|array  $action
     * @return array
     */
    protected function parseAction($action)
    {
        if (is_string($action) || is_callable($action)) {
            // A string or Closure is given as Action.
            return array('uses' => $action);
        } else if (! isset($action['uses'])) {
            // Find the Closure in the Action array.
            $action['uses'] = $this->findClosure($action);
        }

        return $action;
    }

    /**
     * Find the Closure in an action array.
     *
     * @param  array  $action
     * @return \Closure
     */
    protected function findClosure(array $action)
    {
        return array_first($action, function($key, $value)
        {
            return is_callable($value);
        });
    }

    /**
     * Add before filters to the route.
     *
     * @param  string  $filters
     * @return $this
     */
    public function before($filters)
    {
        return $this->addFilters('before', $filters);
    }

    /**
     * Add after filters to the route.
     *
     * @param  string  $filters
     * @return $this
     */
    public function after($filters)
    {
        return $this->addFilters('after', $filters);
    }

    /**
     * Add the given Filters to the route by type.
     *
     * @param  string  $type
     * @param  string  $filters
     * @return \Routing\Route
     */
    protected function addFilters($type, $filters)
    {
        if (isset($this->action[$type])) {
            $this->action[$type] .= '|' .$filters;
        } else {
            $this->action[$type] = $filters;
        }

        return $this;
    }

    /**
     * Get the Filters for the current Route instance.
     *
     * @return array
     */
    public function getFilters()
    {
        if (! isset($this->action['filters'])) {
            return array();
        }

        // Parse and return the Filters.
        $filters = $this->action['filters'];

        return $this->parseFilters($filters);
    }

    /**
     * Get the "before" filters for the route.
     *
     * @return array
     */
    public function beforeFilters()
    {
        if ( ! isset($this->action['before'])) return array();

        //
        $filters = $this->action['before'];

        return $this->parseFilters($filters);
    }

    /**
     * Get the "after" filters for the route.
     *
     * @return array
     */
    public function afterFilters()
    {
        if ( ! isset($this->action['after'])) return array();

        //
        $filters = $this->action['after'];

        return $this->parseFilters($filters);
    }

    /**
     * Parse the given filter string.
     *
     * @param  string  $filters
     * @return array
     */
    protected function parseFilters($filters)
    {
        return array_build(static::explodeFilters($filters), function($key, $value)
        {
            return static::parseFilter($value);
        });
    }

    /**
     * Turn the filters into an array if they aren't already.
     *
     * @param  array|string  $filters
     * @return array
     */
    protected static function explodeFilters($filters)
    {
        if (is_array($filters)) {
            return static::explodeArrayFilters($filters);
        }

        return explode('|', $filters);
    }

    /**
     * Flatten out an array of filter declarations.
     *
     * @param  array  $filters
     * @return array
     */
    protected static function explodeArrayFilters(array $filters)
    {
        $results = array();

        foreach ($filters as $filter) {
            $results = array_merge($results, explode('|', $filter));
        }

        return $results;
    }

    /**
     * Parse the given filter into name and parameters.
     *
     * @param  string  $filter
     * @return array
     */
    public static function parseFilter($filter)
    {
        if (! str_contains($filter, ':')) {
            return array($filter, array());
        }

        return static::parseParameterFilter($filter);
    }

    /**
     * Parse a filter with parameters.
     *
     * @param  string  $filter
     * @return array
     */
    protected static function parseParameterFilter($filter)
    {
        list($name, $parameters) = explode(':', $filter, 2);

        return array($name, explode(',', $parameters));
    }

    /**
     * Run the Route action and return the response.
     *
     * @return mixed
     */
    public function run()
    {
        $parameters = array_filter($this->getParams(), function($p) { return isset($p); });

        return call_user_func_array($this->action['uses'], $parameters);
    }

    /**
     * Checks if a Request matches the Route pattern.
     *
     * @param \Http\Request $request The dispatched Request instance
     * @param bool $includingMethod Wheter or not is matched the HTTP Method
     * @return bool Match status
     * @internal param string $pattern URL pattern
     */
    public function matches(Request $request, $includingMethod = true)
    {
        $method = $request->method();

        if ($includingMethod && ! in_array($method, $this->methods)) {
            return false;
        }

        $uri = $request->path();

        //
        // Build the regex for matching.
        $namedParams = false;

        if (preg_match('#\{([^\}]+)\}#', $this->pattern) === 1) {
            // We have Named Parameters.
            $regex = static::compileRoute($this->pattern);

            $namedParams = true;
        } else {
            $searches = array(':any', ':num', ':all');
            $replaces = array('[^/]+', '[0-9]+', '.*');

            // Retrieve the additional Routing Patterns from configuration.
            $patterns = Config::get('routing.patterns', array());

            if(! empty($patterns)) {
                $searches = array_merge($searches, array_keys($patterns));
                $replaces = array_merge($replaces, array_values($patterns));
            }

            // Prepare the pattern with replacements.
            $regex = str_replace($searches, $replaces, $this->pattern);

            if (strpos($regex, '(/') !== false) {
                $regex = str_replace(array('(/', ')'), array('(?:/', ')?'), $regex);
            }
        }

        // Attempt to match the Route and extract the parameters.
        if (preg_match('#^' .$pattern .'(?:\?.*)?$#i', $uri, $matches)) {
            $params = array_filter($matches, function($key) use ($namedParams)
            {
                return $namedParams ? is_string($key) : ($key > 0);
            }, ARRAY_FILTER_USE_KEY);

            // Store the current information.
            $this->uri        = $uri;
            $this->method     = $method;
            $this->parameters = $params;
            $this->regex      = $regex;

            return true;
        }

        return false;
    }

    protected static function compileRoute($pattern)
    {
        // Convert the Named Patterns, e.g. {controller}
        $regex = preg_replace('#\{(\w+)\}#', '(?P<\1>[^/]+)', $pattern);

        // Convert the optional Named Patterns, e.g. /{category?}
        $regex = preg_replace('#/\{(\w+)\?\}#', '(?:/(?P<\1>[^/]+)', $regex, -1, $count);

        // Convert the optional Named Patterns with custom regular expression e.g. {id:\d+:?}
        $regex = preg_replace('#/\{(\w+):([^\}]+):\?\}#', '(?:/(?P<\1>\2)', $regex, -1, $count2);

        $count += $count2;

        // Convert the Named Patterns with custom regular expression e.g. {id:\d+}
        $regex = preg_replace('#\{(\w+):([^\}]+)\}#', '(?P<\1>\2)', $regex);

        // Pad the pattern with ')?' if is need.
        if($count > 0) {
            $regex .= str_repeat (')?', $count);
        }

        return $regex;
    }

    /**
     * Add a prefix to the route URI.
     *
     * @param  string  $prefix
     * @return \Routing\Route
     */
    public function prefix($prefix)
    {
        $this->pattern = trim($prefix, '/') .'/' .trim($this->pattern, '/');

        return $this;
    }

    /**
     * Get the key / value list of parameters for the route.
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function parameters()
    {
        return array_map(function($value)
        {
            return is_string($value) ? rawurldecode($value) : $value;
        }, $this->parameters);
    }

    /**
     * Get the key / value list of parameters without null values.
     *
     * @return array
     */
    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), function($value)
        {
            return ! is_null($value);
        });
    }

    // Some Getters

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function pattern()
    {
        return $this->pattern;
    }

    /**
     * @return string|null
     */
    public function getUri()
    {
        return $this->uri();
    }

    /**
     * @return string|null
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->parameters();
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Get the prefix of the route instance.
     *
     * @return string
     */
    public function getPrefix()
    {
        return array_get($this->action, 'prefix');
    }

    /**
     * Get the name of the route instance.
     *
     * @return string
     */
    public function getName()
    {
        return array_get($this->action, 'as');
    }

    /**
     * Return the Action array.
     *
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the Action array for the Route.
     *
     * @param  array  $action
     * @return \Routing\Route
     */
    public function setAction(array $action)
    {
        $this->action = $action;

        return $this;
    }

}
