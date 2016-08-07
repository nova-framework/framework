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
     * This string defines the characters that are automatically considered separators in front of
     * optional placeholders (with default and no static text following). Such a single separator
     * can be left out together with the optional placeholder from matching and generating URLs.
     */
    const SEPARATORS = '/,;.:-_~+*=@|';

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    private $uri = null;

    /**
     * Supported HTTP methods.
     *
     * @var array
     */
    private $methods = array();

    /**
     * The route action array.
     *
     * @var array
     */
    protected $action = array();

    /**
     * The regular expression requirements.
     *
     * @var array
     */
    protected $wheres = array();

    /**
     * The matched Route parameters.
     *
     * @var array
     */
    private $parameters = array();


    /**
     * Constructor.
     *
     * @param string|array $methods HTTP methods
     * @param string $uri URL pattern
     * @param string|array|callable $action Callback function or options
     */
    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;

        $this->methods = (array) $methods;

        $this->action = $this->parseAction($action);

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

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
        // Attempt to match the Route Method if it is requested.
        if ($includingMethod && ! in_array($request->method(), $this->methods)) {
            return false;
        }

        // Detect the Named Parameters and build the pattern for matching.
        $pattern = $this->compileRoute();

        // Attempt to match the Route pattern.
        if (preg_match('#^' .$pattern .'(?:\?.*)?$#i', $request->path(), $matches) !== 1) {
            return false;
        }

        // Extract the captured parameters.
        $params = array_filter($matches, function($key)
        {
            return is_string($key);
        }, ARRAY_FILTER_USE_KEY);

        // Store the matched parameters.
        $this->parameters = $params;

        return true;
    }

    protected function compileRoute()
    {
        // Process for the Routes which contains Named Parameters.
        if (preg_match('#\{[^\}]+\}#', $this->uri) === 1) {
            $optionals = $this->extractOptionalParameters();

            $uri = preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri);

            return $this->compilePattern($uri, $optionals);
        }

        // Process for the Routes which contains Unnamed Parameters.
        if (preg_match('#\(:\w+\)#', $this->uri) === 1) {
            return static::compileLegacyPattern($this->uri);
        }

        // Process for the bare Routes with optional paths.
        $pattern = $this->uri;

        if (strpos($pattern, '(/') !== false) {
            $pattern = str_replace(array('(/', ')'), array('(?:/', ')?'), $pattern);
        }

        return $pattern;
    }

    /**
     * Get the optional parameters for the route.
     *
     * @return array
     */
    protected function extractOptionalParameters()
    {
        preg_match_all('/\{(\w+?)\?\}/', $this->uri, $matches);

        return isset($matches[1]) ? $matches[1] : array();
    }

    protected function compilePattern($pattern, $optionals)
    {
        preg_match_all('#\{[^\}]+\}#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $variables = array();

        $tokens = array();

        $pos = 0;

        foreach ($matches as $match) {
            $varName = substr($match[0][0], 1, -1);

            if (in_array($varName, $variables)) {
                throw new \LogicException(sprintf('Route pattern "%s" cannot reference variable name "%s" more than once.', $pattern, $varName));
            }

            array_push($variables, $varName);

            // Get all static text preceding the current variable.
            $precedingText = substr($pattern, $pos, $match[0][1] - $pos);

            $pos = $match[0][1] + strlen($match[0][0]);

            $precedingChar = (strlen($precedingText) > 0) ? substr($precedingText, -1) : '';

            $isSeparator = ('' !== $precedingChar) && (false !== strpos(static::SEPARATORS, $precedingChar));

            //
            if ($isSeparator && (strlen($precedingText) > 1)) {
                $tokens[] = array('text', substr($precedingText, 0, -1));
            } elseif (! $isSeparator && (strlen($precedingText) > 0)) {
                $tokens[] = array('text', $precedingText);
            }

            if (isset($this->wheres[$varName])) {
                $regexp = $this->wheres[$varName];
            } else {
                $regexp = '[^/]+';
            }

            $tokens[] = array('variable', $isSeparator ? $precedingChar : '', $varName, $regexp);
        }

        if ($pos < strlen($pattern)) {
            $tokens[] = array('text', substr($pattern, $pos));
        }

        return static::createPattern($tokens, $optionals);
    }

    public static function compileLegacyPattern($pattern)
    {
        $optionals = array();

        //
        $patterns = Config::get('routing.patterns', array());

        $patterns = array_merge($patterns, array(
            ':any' => '[^/]+',
            ':num' => '[0-9]+',
            ':all' => '.*'
        ));

        //
        preg_match_all('#\(:\w+\)#', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        //
        $tokens = array();

        $pos = 0;
        $cnt = 1;

        foreach ($matches as $match) {
            $varName = substr($match[0][0], 1, -1);

            // Get all static text preceding the current variable
            $precedingText = substr($pattern, $pos, $match[0][1] - $pos);

            $pos = $match[0][1] + strlen($match[0][0]);

            $precedingChar = (strlen($precedingText) > 0) ? substr($precedingText, -1) : '';

            $isSeparator = ('' !== $precedingChar) && (false !== strpos(static::SEPARATORS, $precedingChar));

            //
            $isOptional = false;

            if ($isSeparator && (strlen($precedingText) > 1)) {
                if(substr($precedingText, -2) == '(/') {
                    $isOptional = true;

                    if ((strlen($precedingText) > 2)) {
                        $tokens[] = array('text', substr($precedingText, 0, -2));
                    }
                } else {
                    $tokens[] = array('text', substr($precedingText, 0, -1));
                }
            } elseif (! $isSeparator && (strlen($precedingText) > 0)) {
                $tokens[] = array('text', $precedingText);
            }

            //
            $regexp = isset($patterns[$varName]) ? $patterns[$varName] : '[^/]+';

            $varName = 'param' .$cnt++;

            if ($isOptional) array_push($optionals, $varName);

            $tokens[] = array('variable', $isSeparator ? $precedingChar : '', $varName, $regexp);
        }

        if (empty($optionals) && ($pos < strlen($pattern))) {
            $tokens[] = array('text', substr($pattern, $pos));
        }

        return static::createPattern($tokens, $optionals);
    }

    protected static function createPattern(array $tokens, $optionals)
    {
        $pattern = '';

        foreach ($tokens as $token) {
            if ($token[0] == 'variable') {
                list($type, $separator, $varName, $regexp) = $token;

                //
                if (in_array($varName, $optionals)) $pattern .= '(?:';

                $pattern .= $separator .'(?P<' .$varName .'>' .$regexp .')';
            } else if ($token[0] == 'text') {
                $pattern .= $token[1];
            }
        }

        // Pad the pattern with ')?' if it is need.
        if(! empty($optionals)) {
            $pattern .= str_repeat (')?', count($optionals));
        }

        return $pattern;
    }

    /**
     * Set a regular expression requirement on the route.
     *
     * @param  array|string  $name
     * @param  string  $expression
     * @return $this
     */
    public function where($name, $expression = null)
    {
        foreach ($this->parseWhere($name, $expression) as $name => $expression) {
            $this->wheres[$name] = $expression;
        }

        return $this;
    }

    /**
     * Parse arguments to the where method into an array.
     *
     * @param  array|string  $name
     * @param  string  $expression
     * @return array
     */
    protected function parseWhere($name, $expression)
    {
        return is_array($name) ? $name : array($name => $expression);
    }

    /**
     * Set a list of regular expression requirements on the route.
     *
     * @param  array  $wheres
     * @return $this
     */
    protected function whereArray(array $wheres)
    {
        foreach ($wheres as $name => $expression) {
            $this->where($name, $expression);
        }

        return $this;
    }

    /**
     * Add a prefix to the route URI.
     *
     * @param  string  $prefix
     * @return \Routing\Route
     */
    public function prefix($prefix)
    {
        $this->uri = trim($prefix, '/') .'/' .trim($this->uri, '/');

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

    /**
     * Get the URI associated with the route.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->uri();
    }

    /**
     * Get the URI associated with the route.
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

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
     * Set the URI that the route responds to.
     *
     * @param  string  $uri
     * @return \Routing\Route
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUri()
    {
        return $this->uri();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->parameters();
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
