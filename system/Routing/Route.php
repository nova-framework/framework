<?php
/**
 * Route - manage a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Core\Config;

use Symfony\Component\HttpFoundation\Response;

use Language;


/**
 * The Route class is responsible for routing an HTTP request to an assigned Callback function.
 */
class Route
{
    /**
     * @var array All available Filters
     */
    private static $filters = array();

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
    private $params = array();

    /**
     * @var string Matching regular expression
     */
    private $regex;

    /**
     * @var string The current matched Language
     */
    private $language = null;


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

        $this->pattern = ! empty($pattern) ? $pattern : '/';

        $this->action = $this->parseAction($action);

        if (isset($this->action['prefix'])) {
            $this->prefix($this->action['prefix']);
        }

        $this->language = Language::code();
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
     * Add (before) Filters to the Route.
     *
     * @param  string  $filters
     * @return \Routing\Route
     */
    public function before($filters)
    {
        return $this->addFilters('filters', $filters);
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

        return static::parseFilters($filters);
    }

    /**
     * Parse the given filter string.
     *
     * @param  string  $filters
     * @return array
     */
    public static function parseFilters($filters)
    {
        return array_build(static::explodeFilters($filters), function($key, $value)
        {
            return Route::parseFilter($value);
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
     * Define a Route Filter.
     *
     * @param string $name
     * @param callback $callback
     */
    public static function filter($name, $callback)
    {
        if (array_key_exists($name, static::$filters)) {
            throw new \Exception('Filter already exists: ' .$name);
        }

        static::$filters[$name] = $callback;
    }

    /**
     * Return the available Filters.
     *
     * @return array
     */
    public static function getAvailableFilters()
    {
        return static::$filters;
    }

    public function applyFilters()
    {
        $result = null;

        foreach ($this->getFilters() as $filter => $params) {
            if(empty($filter)) {
                continue;
            } else if (! array_key_exists($filter, static::$filters)) {
                throw new \Exception('Invalid Filter specified: ' .$filter);
            }

            // Get the current Filter Callback.
            $callback = static::$filters[$filter];

            // If the Callback returns a Response instance, the Filtering will be stopped.
            if (is_callable($callback)) {
                $result = call_user_func($callback, $this, $params);
            }

            if ($result instanceof Response) {
                break;
            }
        }

        return $result;
    }

    /**
     * Checks if a URL and HTTP method matches the Route pattern.
     *
     * @param string $uri Requested URL
     * @param $method Current HTTP method
     * @param bool $optionals Use, or not, the support for the optional parameters
     * @return bool Match status
     * @internal param string $pattern URL pattern
     */
    public function match($uri, $method, $optionals = false, array $patterns = array())
    {
        if (! in_array($method, $this->methods)) {
            return false;
        }

        // Have a valid HTTP method for this Route; store it for later usage.
        $this->method = $method;

        // Exact match Route.
        if ($this->pattern == $uri) {
            // Store the current matched URI.
            $this->uri = $uri;

            return true;
        }

        //
        // Build the regex for matching.

        if (strpos($this->pattern, '{') === false) {
            $regex = $this->pattern;
        } else {
            // Convert the Named Patterns to (:any), e.g. {category}
            $regex = preg_replace('#\{([a-z]+)\}#', '([^/]+)', $regex);

            // Convert the optional Named Patterns to (/(:any)), e.g. /{category?}
            if ($optionals) {
                $count = 0;

                $regex = preg_replace('#/\{([a-z]+)\?\}#', '(/([^/]+)', $this->pattern, -1, $count);

                if($count > 0) {
                    // Pad the pattern with the required ')' characters.
                    $regex .= str_repeat (')', $count);
                }
            } else {
                // Convert the Named Patterns to (:num), e.g. {:d}
                $regex = preg_replace('#\{:([a-z]+)\}#', '([0-9]+)', $regex);
            }
        }

        if (strpos($regex, ':') !== false) {
            $searches = array_merge(array(':any', ':num', ':all'), array_keys($patterns));
            $replaces = array_merge(array('[^/]+', '[0-9]+', '.*'), array_values($patterns));

            $regex = str_replace($searches, $replaces, $regex);
        }

        if ($optionals && (strpos($regex, '(/') !== false)) {
            $regex = str_replace(array('(/', ')'), array('(?:/', ')?'), $regex);
        }

        // Attempt to match the Route and extract the parameters.
        if (preg_match('#^(?:([a-z]{2})?/?)?' .$regex .'(?:\?.*)?$#i', $uri, $matches)) {
            // Remove $matched[0] as [1] is the first parameter.
            array_shift($matches);

            // Store the current matched URI.
            $this->uri = $uri;

            // Store the extracted parameters.
            if (! empty($matches)) {
                $language = array_shift($matches);

                $active = Config::get('app.multilingual', false);

                // Check again if the first parameter is a a valid Language Code.
                if ($active && array_key_exists($language, Config::get('languages'))) {
                    $this->language = $language;
                }
            }

            $this->params = $matches;

            // Also, store the compiled regex.
            $this->regex = $regex;

            return true;
        }

        return false;
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
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->language;
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
        return $this->params;
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return array_get($this->action, 'uses');
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
    public static function getAction()
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
