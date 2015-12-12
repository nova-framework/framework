<?php
/**
 * Route - manage a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@@giulianaeassociati.com
 * @version 3.0
 * @date December 11th, 2015
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
    private $method;

    /**
     * @var string URL pattern
     */
    private $pattern;

    /**
     * @var mixed Callback
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
     * @param string $method HTTP method
     * @param string $pattern URL pattern
     * @param mixed $callback Callback function
     */
    public function __construct($method, $pattern, $callback)
    {
        $this->method   = $method;
        $this->pattern  = $pattern;
        $this->callback = $callback;
    }

    /**
     * Checks if a URL and HTTP method matches the Route pattern.
     *
     * @param string $uri Requested URL
     * @param string $pattern URL pattern
     * @return boolean Match status
     */
    public function match($uri, $method)
    {
        if (($this->method != $method) && ($this->method != 'ANY')) {
            return false;
        }

        // Wildcard or exact match.
        if (($this->pattern === '*') || ($this->pattern === $uri)) {
            return true;
        }

        $last_char = substr($this->pattern, -1);

        // Build the regex for matching.
        if (strpos($this->pattern, ':') !== false) {
            $regex = str_replace(
                array(':any', ':num', ':all', ':hex', ':uuidV4'),
                array('[^/]+', '-?[0-9]+', '.*', '[[:xdigit:]]+', '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}'),
                $this->pattern
            );
        }
        else {
            $regex = $this->pattern;
        }

        $regex = str_replace(array('(/', ')', '/*'), array('(?:/', ')?', '(/?|/.*?)'), $regex);

        // Fix trailing slash.
        if ($last_char === '/') {
            $regex .= '?';
        }
        // Allow trailing slash.
        else {
            $regex .= '/?';
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

    public function method()
    {
        return $this->method;
    }

    public function pattern()
    {
        return $this->pattern;
    }

    public function callback()
    {
        return $this->callback;
    }

    public function params()
    {
        return $this->params;
    }

    public function regex()
    {
        return $this->regex;
    }

}
