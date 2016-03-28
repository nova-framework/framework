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
     * @var string HTTP method or 'ANY'
     */
    private $methods = array();

    /**
     * @var string URL pattern
     */
    private $pattern;

    /**
     * @var callable Callback
     */
    private $callback = null;

    /**
     * @var array Route parameters
     */
    private $params = array();

    /**
     * @var string Matching regular expression
     */
    private $regex;

    /**
     * Constructor.
     *
     * @param string $methods HTTP methods
     * @param string $pattern URL pattern
     * @param callable $callback Callback function
     */
    public function __construct(array $methods, $pattern, $callback)
    {
        $this->methods = $methods;

        $this->pattern = ! empty($pattern) ? $pattern : '/';

        $this->callback = $callback;
    }

    /**
     * Checks if a URL and HTTP method matches the Route pattern.
     *
     * @param string $uri Requested URL
     * @param $method
     * @param bool $optionals
     * @return bool Match status
     * @internal param string $pattern URL pattern
     */
    public function match($uri, $method, $optionals = true)
    {
        if (! in_array($method, $this->methods) && ! in_array('ANY', $this->methods)) {
            return false;
        }

        // Exact match Route.
        if ($this->pattern == $uri) {
            return true;
        }

        // Build the regex for matching.
        if (strpos($this->pattern, ':') !== false) {
            $regex = str_replace(array(':any', ':num', ':all'), array('[^/]+', '-?[0-9]+', '.*'), $this->pattern);
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
     * @return string
     */
    public function method()
    {
        return $this->method;
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
