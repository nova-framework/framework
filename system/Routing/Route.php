<?php
/**
 * Route - manage a route to an HTTP request and an assigned callback function.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Routing;

use Http\Request;
use Routing\Matching\UriValidator;
use Routing\Matching\HostValidator;
use Routing\Matching\MethodValidator;
use Routing\Matching\SchemeValidator;
use Routing\LegacyRouteCompiler;

use Symfony\Component\Routing\Route as SymfonyRoute;


/**
 * The Route class is responsible for routing an HTTP request to an assigned Callback function.
 */
class Route
{
    /**
     * The URI pattern the Route responds to.
     *
     * @var string
     */
    private $uri = null;

    /**
     * The compiled pattern the Route responds to.
     *
     * @var string
     */
    private $pattern = null;

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
     * The default values for the Route.
     *
     * @var array
     */
    protected $defaults = array();

    /**
     * The regular expression requirements.
     *
     * @var array
     */
    protected $wheres = array();

    /**
     * The route compiler instance.
     *
     * @var \Routing\RouteCompiler
     */
    protected $compiler;

    /**
     * The matched Route parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * The parameter names for the route.
     *
     * @var array|null
     */
    protected $parameterNames;

    /**
     * The validators used by the routes.
     *
     * @var array
     */
    protected static $validators;

    /**
     * The compiled version of the Route.
     *
     * @var \Symfony\Component\Routing\CompiledRoute
     */
    protected $compiled = null;

    /**
     * Boolean indicating the use of Named Parameters on not.
     *
     * @var bool $namedParams
     */
    private $namedParams = true;


    /**
     * Constructor.
     *
     * @param string|array $methods HTTP methods
     * @param string $uri URL pattern
     * @param string|array|callable $action Callback function or options
     * @param bool $namedParams Wheter or not are used the Named Parameters
     */
    public function __construct($methods, $uri, $action, $namedParams = true)
    {
        $uri = trim($uri, '/');

        //
        $this->uri = ! empty($uri) ? $uri : '/';

        $this->methods = (array) $methods;

        $this->action = $this->parseAction($action);

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }

        if (isset($this->action['prefix'])) {
            $this->prefix($this->action['prefix']);
        }

        //
        $this->namedParams = $namedParams;
    }

    /**
     * Run the Route action and return the response.
     *
     * @return mixed
     */
    public function run()
    {
        $parameters = array_filter($this->getParams(), function($param)
        {
            return isset($param);
        });

        return call_user_func_array($this->action['uses'], $parameters);
    }

    /**
     * Checks if a Request matches the Route pattern.
     *
     * @param \Http\Request $request The dispatched Request instance
     * @param bool $includingMethod Wheter or not is matched the HTTP Method
     * @return bool Match status
     */
    public function matches(Request $request, $includingMethod = true)
    {
        $this->compileRoute();

        foreach ($this->getValidators() as $validator) {
            if (! $includingMethod && ($validator instanceof MethodValidator)) continue;

            if (! $validator->matches($this, $request)) return false;
        }

        return true;
    }

    /**
     * Compile the Route pattern for matching and return it.
     *
     * @return string
     */
    public function compileRoute()
    {
        if ($this->namedParams) {
            // We are using the Named Parameters on Route compilation.
            $optionals = $this->extractOptionalParameters();

            $this->pattern = preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri);
        } else {
            // We are using the Unnamed Parameters on Route compilation.
            $compiler = $this->getLegacyCompiler();

            list($this->pattern, $optionals, $wheres) = $compiler->compile($this->uri);

            // Setup the Route wheres.
            foreach ($wheres as $key => $value) {
                $this->where($key, $value);
            }
        }

        $this->compiled = with(
            new SymfonyRoute($this->pattern, $optionals, $this->wheres, array(), $this->domain() ?: '')
        )->compile();
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

    /**
     * Parse the Route Action into a standard array.
     *
     * @param  \Closure|array  $action
     * @return array
     */
    protected function parseAction($action)
    {
        if (is_string($action) || is_callable($action)) {
            // A null, string or Closure is given as Action.
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
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        if (isset(static::$validators)) return static::$validators;

        return static::$validators = array(
            new MethodValidator(),
            new SchemeValidator(),
            new HostValidator(),
            new UriValidator(),
        );
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
     * Get the "before" filters for the route.
     *
     * @return array
     */
    public function beforeFilters()
    {
        if (! isset($this->action['before'])) return array();

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
        if (! isset($this->action['after'])) return array();

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
     * Get a given parameter from the route.
     *
     * @param  string  $name
     * @param  mixed   $default
     * @return string
     */
    public function getParameter($name, $default = null)
    {
        return $this->parameter($name, $default);
    }

    /**
     * Get a given parameter from the route.
     *
     * @param  string  $name
     * @param  mixed   $default
     * @return string
     */
    public function parameter($name, $default = null)
    {
        return array_get($this->parameters(), $name, $default);
    }

    /**
     * Set a parameter to the given value.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters();

        $this->parameters[$name] = $value;
    }

    /**
     * Unset a parameter on the route if it is set.
     *
     * @param  string  $name
     * @return void
     */
    public function forgetParameter($name)
    {
        $this->parameters();

        unset($this->parameters[$name]);
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
        if (! isset($this->parameters)) {
            throw new \LogicException("Route is not bound.");
        }

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
     * Get all of the parameter names for the route.
     *
     * @return array
     */
    public function parameterNames()
    {
        if (isset($this->parameterNames)) return $this->parameterNames;

        return $this->parameterNames = $this->compileParameterNames();
    }

    /**
     * Get the parameter names for the route.
     *
     * @return array
     */
    protected function compileParameterNames()
    {
        if (! isset($this->pattern)) {
            throw new \LogicException("Route is not compiled.");
        }

        preg_match_all('/\{(.*?)\}/', $this->pattern, $matches);

        return array_map(function($value)
        {
            return trim($value, '?');

        }, $matches[1]);
    }

    /**
     * Bind the Route to a given Request for execution.
     *
     * @param  \Http\Request  $request
     * @return $this
     */
    public function bind(Request $request)
    {
        $this->compileRoute();

        $this->bindParameters($request);

        return $this;
    }

    /**
     * Extract the parameter list from the request.
     *
     * @param  \Http\Request  $request
     * @return array
     */
    public function bindParameters(Request $request)
    {
        $parameters = $this->matchToKeys(
            array_slice($this->bindPathParameters($request), 1)
        );

        if (! is_null($this->compiled->getHostRegex())) {
            $params = $this->bindHostParameters($request, $params);
        }

        return $this->parameters = $this->replaceDefaults($parameters);
    }

    /**
     * Get the parameter matches for the path portion of the URI.
     *
     * @param  \Http\Request  $request
     * @return array
     */
    protected function bindPathParameters(Request $request)
    {
        preg_match($this->compiled->getRegex(), '/' .$request->decodedPath(), $matches);

        return $matches;
    }

    /**
     * Extract the parameter list from the host part of the request.
     *
     * @param  \Http\Request  $request
     * @param  array  $parameters
     * @return array
     */
    protected function bindHostParameters(Request $request, $parameters)
    {
        preg_match($this->compiled->getHostRegex(), $request->getHost(), $matches);

        return array_merge($this->matchToKeys(array_slice($matches, 1)), $parameters);
    }

    /**
     * Combine a set of parameter matches with the route's keys.
     *
     * @param  array  $matches
     * @return array
     */
    protected function matchToKeys(array $matches)
    {
        if (count($this->parameterNames()) == 0) return array();

        $parameters = array_intersect_key($matches, array_flip($this->parameterNames()));

        return array_filter($parameters, function($value)
        {
            return is_string($value) && (strlen($value) > 0);
        });
    }

    /**
     * Replace null parameters with their defaults.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function replaceDefaults(array $parameters)
    {
        foreach ($parameters as $key => &$value) {
            $value = isset($value) ? $value : array_get($this->defaults, $key);
        }

        return $parameters;
    }

    /**
     * Set a default value for the route.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function defaults($key, $value)
    {
        $this->defaults[$key] = $value;

        return $this;
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
        $uri = trim(trim($prefix, '/') .'/' .trim($this->uri, '/'), '/');

        $this->uri = ! empty($uri) ? $uri : '/';

        return $this;
    }

    /**
     * Get a Route Compiler instance.
     *
     * @return \Routing\RouteCompiler
     */
    protected function getLegacyCompiler()
    {
        return $this->compiler ?: $this->compiler = new LegacyRouteCompiler($this->wheres);
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
     * Determine if the route only responds to HTTP requests.
     *
     * @return bool
     */
    public function httpOnly()
    {
        return in_array('http', $this->action, true);
    }

    /**
     * Determine if the route only responds to HTTPS requests.
     *
     * @return bool
     */
    public function httpsOnly()
    {
        return $this->secure();
    }

    /**
     * Determine if the route only responds to HTTPS requests.
     *
     * @return bool
     */
    public function secure()
    {
        return in_array('https', $this->action, true);
    }

    /**
     * Get the domain defined for the Route.
     *
     * @return string|null
     */
    public function domain()
    {
        return isset($this->action['domain']) ? $this->action['domain'] : null;
    }

    /**
     * @return string|null
     */
    public function getUri()
    {
        return $this->uri();
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
        return isset($this->action['prefix']) ? $this->action['prefix'] : null;
    }

    /**
     * Get the name of the route instance.
     *
     * @return string
     */
    public function getName()
    {
        return isset($this->action['as']) ? $this->action['as'] : null;
    }

    /**
     * Get the action name for the route.
     *
     * @return string
     */
    public function getActionName()
    {
        return isset($this->action['controller']) ? $this->action['controller'] : 'Closure';
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

    /**
     * Get the compiled version of the Route.
     *
     * @return \Routing\CompiledRoute
     */
    public function getCompiled()
    {
        return $this->compiled;
    }

}
