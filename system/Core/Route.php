<?php
/**
 * Route - manage a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

/**
 * The Route class is responsible for routing an HTTP request to an assigned callback function.
 */
class Route
{
    /**
     * @var array All available Filters
     */
    private static $availFilters = array();

    /**
     * @var array Supported HTTP methods
     */
    private $methods = array();

    /**
     * @var string URL pattern
     */
    private $pattern = null;

    /**
     * @var array Filters to be applied on match
     */
    private $filters = array();

    /**
     * @var callable Callback
     */
    private $callback = null;

    /**
     * @var string The current matched URI
     */
    private $currentUri = null;

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
     * Constructor.
     *
     * @param string|array $method HTTP method(s)
     * @param string $pattern URL pattern
     * @param string|array|callable $callback Callback function or options
     */
    public function __construct($method, $pattern, $callback)
    {
        $this->methods = array_map('strtoupper', is_array($method) ? $method : array($method));

        $this->pattern = ! empty($pattern) ? $pattern : '/';

        if (is_array($callback)) {
            $this->callback = isset($callback['uses']) ? $callback['uses'] : null;

            if (isset($callback['filters']) && ! empty($callback['filters'])) {
                // Explode the filters string using the '|' delimiter.
                $filters = array_filter(explode('|', $callback['filters']), 'strlen');

                $this->filters = array_unique($filters);
            }
        } else {
            $this->callback = $callback;
        }
    }

    /**
     * Define a Route Filter
     *
     * @param string $name
     * @param callback $callback
     */
    public static function filter($name, $callback)
    {
        self::$availFilters[$name] = $callback;
    }

    /**
     * Return the available Filters.
     *
     * @return array
     */
    public static function availFilters()
    {
        return self::$availFilters;
    }

    public function applyFilters()
    {
        $result = true;

        foreach ($this->filters as $filter) {
            if (array_key_exists($filter, self::$availFilters)) {
                // Get the current Filter Callback.
                $callback = self::$availFilters[$filter];

                // Execute the current Filter's callback with the current matched Route as argument.
                //
                // When the Filter returns false, the filtering is considered being globally failed.
                if ($callback !== null) {
                    $result = $this->invokeCallback($callback);
                }
            } else {
                // No Filter with this name found; mark that as failure.
                $result = false;
            }

            if ($result === false) {
                // Failure of the current Filter; stop the loop.
                break;
            }
        }

        return $result;
    }

    private function invokeCallback($callback)
    {
        if (is_object($callback)) {
            // We have a Closure; execute it with the Route instance as parameter.
            return call_user_func($callback, $this);
        }

        // Extract the Class name and the Method from the callback's string.
        $segments = explode('@', $callback);

        $className = $segments[0];
        $method    = $segments[1];

        if (! class_exists($className)) {
            return false;
        }

        // The Filter Class receive on Constructor the Route instance as parameter.
        $object = new $className();

        if (method_exists($object, $method)) {
            // Execute the object's method with this Route instance as argument.
            return call_user_func(array($object, $method), $this);
        }

        return false;
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
    public function match($uri, $method, $optionals = true)
    {
        if (! in_array($method, $this->methods)) {
            return false;
        }

        // Have a valid HTTP method for this Route; store it for later usage.
        $this->method = $method;

        // Exact match Route.
        if ($this->pattern == $uri) {
            // Store the current matched URI.
            $this->currentUri = $uri;

            return true;
        }

        // Build the regex for matching.
        if (strpos($this->pattern, ':') !== false) {
            $regex = str_replace(array(':any', ':num', ':all'), array('[^/]+', '[0-9]+', '.*'), $this->pattern);
        } else {
            $regex = $this->pattern;
        }

        if ($optionals) {
            $regex = str_replace(array('(/', ')'), array('(?:/', ')?'), $regex);
        }

        // Attempt to match the Route and extract the parameters.
        if (preg_match('#^' .$regex .'(?:\?.*)?$#i', $uri, $matches)) {
            // Remove $matched[0] as [1] is the first parameter.
            array_shift($matches);

            // Store the current matched URI.
            $this->currentUri = $uri;

            // Store the extracted parameters.
            $this->params = $matches;

            // Also, store the compiled regex.
            $this->regex = $regex;

            return true;
        }

        return false;
    }

    //
    // Some Getters

    /**
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * @return string
     */
    public function pattern()
    {
        return $this->pattern;
    }

    /**
     * @return callable
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * @return string|null
     */
    public function currentUri()
    {
        return $this->currentUri;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function regex()
    {
        return $this->regex;
    }
}
