<?php
/**
 * Application - Implements the Application.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Foundation;

use Config\LoaderManager;
use Foundation\EnvironmentDetector;
use Foundation\ProviderRepository;
use Http\Request;
use Http\Response;
use Support\Contracts\ResponsePreparerInterface;
use Support\Facades\Facade;

use Events\EventServiceProvider;
use Exception\ExceptionServiceProvider;
use Routing\RoutingServiceProvider;

use Illuminate\Container\Container;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Debug\Exception\FatalErrorException;

use Stack\Builder as StackBuilder;

use Closure;


class Application extends Container implements HttpKernelInterface, TerminableInterface, ResponsePreparerInterface
{
    /**
     * The Nova Framework version.
     *
     * @var string
     */
    const VERSION = '3.61.4';

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * The array of booting callbacks.
     *
     * @var array
     */
    protected $bootingCallbacks = array();

    /**
     * The array of booted callbacks.
     *
     * @var array
     */
    protected $bootedCallbacks = array();

    /**
     * The array of finish callbacks.
     *
     * @var array
     */
    protected $finishCallbacks = array();

    /**
     * The array of shutdown callbacks.
     *
     * @var array
     */
    protected $shutdownCallbacks = array();

    /**
     * All of the developer defined middlewares.
     *
     * @var array
     */
    protected $middlewares = array();

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = array();

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = array();

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = array();

    /**
     * The Request class used by the application.
     *
     * @var string
     */
    protected static $requestClass = 'Http\Request';


    /**
     * Create a new application instance.
     *
     * @return void
     */
    public function __construct(Request $request = null)
    {
        $request = $request ?: $this->createNewRequest();

        $this->registerBaseBindings($request);

        $this->registerBaseServiceProviders();
    }

    /**
     * Create a new Request instance from the Request class.
     *
     * @return \Http\Request
     */
    protected function createNewRequest()
    {
        $request = forward_static_call(array(static::$requestClass, 'createFromGlobals'));

        return $request;
    }

    /**
     * Register the basic bindings into the Container.
     *
     * @param  \Http\Request  $request
     * @return void
     */
    protected function registerBaseBindings(Request $request)
    {
        $this->instance('request', $request);

        $this->instance('Illuminate\Container\Container', $this);
    }

    /**
     * Register all of the base Service Providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        foreach (array('Event', 'Exception', 'Routing') as $name) {
            $this->{"register{$name}Provider"}();
        }
    }

    /**
     * Register the Exception Service Provider.
     *
     * @return void
     */
    protected function registerExceptionProvider()
    {
        $this->register(new ExceptionServiceProvider($this));
    }

    /**
     * Register the Routing Service Provider.
     *
     * @return void
     */
    protected function registerRoutingProvider()
    {
        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * Register the Event Service Provider.
     *
     * @return void
     */
    protected function registerEventProvider()
    {
        $this->register(new EventServiceProvider($this));
    }

    /**
     * Bind the installation paths to the application.
     *
     * @param  string  $paths
     * @return string
     */
    public function bindInstallPaths(array $paths)
    {
        $this->instance('path', realpath($paths['app']));

        foreach ($paths as $key => $value) {
            $this->instance("path.{$key}", realpath($value));
        }
    }

    /**
     * Start the exception handling for the request.
     *
     * @return void
     */
    public function startExceptionHandling()
    {
        //$debug = $this['config']['app.debug'];

        // This way is possible to start early the Exception Handler.
        $debug = (ENVIRONMENT == 'development');

        // Optionally setup the Default Timezone to UTC.
        $timezone = ini_get('date.timezone');

        if (empty($timezone)) {
            date_default_timezone_set('UTC');
        }

        // Start the Exception Handling.
        $this['exception']->register($this->environment());

        $this['exception']->setDebug($debug);
    }

    /**
     * Get or check the current application environment.
     *
     * @param  dynamic
     * @return string
     */
    public function environment()
    {
        if (count(func_get_args()) > 0) {
            return in_array($this['env'], func_get_args());
        } else {
            return $this['env'];
        }
    }

    /**
     * Determine if application is in local environment.
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this['env'] == 'local';
    }

    /**
     * Detect the application's current environment.
     *
     * @param  array|string  $envs
     * @return string
     */
    public function detectEnvironment($envs)
    {
        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        return $this['env'] = with(new EnvironmentDetector())->detect($envs, $args);
    }

    /**
     * Determine if we are running in the console.
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool  $force
     * @return \Support\ServiceProvider
     */
    public function register($provider, $options = array(), $force = false)
    {
        if ($registered = $this->getRegistered($provider) && ! $force) {
            return $registered;
        }

        if (is_string($provider)) {
            $provider = $this->resolveProviderClass($provider);
        }

        $provider->register();

        foreach ($options as $key => $value) {
            $this[$key] = $value;
        }

        $this->markAsRegistered($provider);

        if ($this->booted) $provider->boot();

        return $provider;
    }

    /**
     * Get the registered Service Provider instance if it exists.
     *
     * @param  \Support\ServiceProvider|string  $provider
     * @return \Support\ServiceProvider|null
     */
    public function getRegistered($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        if (array_key_exists($name, $this->loadedProviders)) {
            return array_first($this->serviceProviders, function($key, $value) use ($name)
            {
                return (get_class($value) == $name);
            });
        }
    }

    /**
     * Resolve a Service Provider instance from the class name.
     *
     * @param  string  $provider
     * @return \Support\ServiceProvider
     */
    public function resolveProviderClass($provider)
    {
        return new $provider($this);
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  \Support\ServiceProvider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $class = get_class($provider);

        $this->serviceProviders[] = $provider;

        $this->loadedProviders[$class] = true;
    }

    /**
     * Load and boot all of the remaining deferred providers.
     *
     * @return void
     */
    public function loadDeferredProviders()
    {
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = array();
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param  string  $service
     * @return void
     */
    protected function loadDeferredProvider($service)
    {
        $provider = $this->deferredServices[$service];

        if (!isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * Register a deffered provider and service.
     *
     * @param  string  $provider
     * @param  string  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        if ($service) unset($this->deferredServices[$service]);

        $this->register($instance = new $provider($this));

        if (! $this->booted) {
            $this->booting(function() use ($instance)
            {
                $instance->boot();
            });
        }
    }

    /**
     * Resolve the given type from the Container.
     *
     * (Overriding Container::make)
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = array())
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::make($abstract, $parameters);
    }

    /**
     * Register a "finish" application filter.
     *
     * @param  Closure|string  $callback
     * @return void
     */
    public function finish($callback)
    {
        $this->finishCallbacks[] = $callback;
    }

    /**
     * Register a "shutdown" callback.
     *
     * @param  callable  $callback
     * @return void
     */
    public function shutdown($callback = null)
    {
        if (is_null($callback)) {
            $this->fireAppCallbacks($this->shutdownCallbacks);
        } else {
            $this->shutdownCallbacks[] = $callback;
        }
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->booted) return;

        array_walk($this->serviceProviders, function($p) { $p->boot(); });

        $this->bootApplication();
    }

    /**
     * Boot the application and fire app callbacks.
     *
     * @return void
     */
    protected function bootApplication()
    {
        $this->fireAppCallbacks($this->bootingCallbacks);

        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    /**
     * Register a new boot listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booting($callback)
    {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) $this->fireAppCallbacks(array($callback));
    }

    /**
     * Run the application and send the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return void
     */
    public function run(SymfonyRequest $request = null)
    {
        $request = $request ?: $this['request'];

        // Handle the Request.
        $response = with($stack = $this->getStackedClient())->handle($request);

        // Send the Response.
        $this->prepareResponse($response)->send();

        // Execute the Termination Stage.
        $this->terminate($request, $response);
    }

    /**
     * Get the stacked HTTP kernel for the application.
     *
     * @return  \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected function getStackedClient()
    {
        $debug = $this['config']['app.debug'];

        $sessionReject = $this->bound('session.reject') ? $this['session.reject'] : null;

        $client = with(new StackBuilder)
            ->push('Cookie\Guard', $this['encrypter'])
            ->push('Cookie\Queue', $this['cookie'])
            ->push('Session\Middleware', $this['session'], $sessionReject);

        $this->mergeCustomMiddlewares($client);

        return $client->resolve($this);
    }

    /**
     * Merge the developer defined middlewares onto the stack.
     *
     * @param  \Stack\Builder
     * @return void
     */
    protected function mergeCustomMiddlewares(StackBuilder $stack)
    {
        foreach ($this->middlewares as $middleware) {
            list($class, $parameters) = array_values($middleware);

            array_unshift($parameters, $class);

            call_user_func_array(array($stack, 'push'), $parameters);
        }
    }

    /**
     * Register the default, but optional middlewares.
     *
     * @return void
     */
    protected function registerBaseMiddlewares()
    {
        $this->middleware('Http\FrameGuard');
    }

    /**
     * Add a HttpKernel middleware onto the stack.
     *
     * @param  string  $class
     * @param  array  $parameters
     * @return \Illuminate\Foundation\Application
     */
    public function middleware($class, array $parameters = array())
    {
        $this->middlewares[] = compact('class', 'parameters');

        return $this;
    }

    /**
     * Remove a custom middleware from the application.
     *
     * @param  string  $class
     * @return void
     */
    public function forgetMiddleware($class)
    {
        $this->middlewares = array_filter($this->middlewares, function($m) use ($class)
        {
            return $m['class'] != $class;
        });
    }


    /**
     * Handle the given Request and get the Response.
     *
     * Provides compatibility with BrowserKit functional testing.
     *
     * @implements HttpKernelInterface::handle
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  int   $type
     * @param  bool  $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        try {
            $this->refreshRequest($request = Request::createFromBase($request));

            $this->boot();

            return $this->dispatch($request);
        } catch (\Exception $e) {
            return $this['exception']->handleException($e);
        }
    }

    /**
     * Handle the given request and get the response.
     *
     * @param  Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatch(Request $request)
    {
        $router = $this['router'];

        return $router->dispatch($this->prepareRequest($request));
    }

    /**
     * Terminate the request and send the response to the browser.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(SymfonyRequest $request, SymfonyResponse $response)
    {
        $this->callFinishCallbacks($request, $response);

        $this->shutdown();
    }

    /**
     * Refresh the bound request instance in the container.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function refreshRequest(Request $request)
    {
        $this->instance('request', $request);

        Facade::clearResolvedInstance('request');
    }

    /**
     * Call the "finish" callbacks assigned to the application.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function callFinishCallbacks(SymfonyRequest $request, SymfonyResponse $response)
    {
        foreach ($this->finishCallbacks as $callback) {
            call_user_func($callback, $request, $response);
        }
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @return void
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * Prepare the request by injecting any services.
     *
     * @param  Http\Request  $request
     * @return \Http\Request
     */
    public function prepareRequest(Request $request)
    {
        $config = $this['config'];

        if (! is_null($config['session.driver']) && ! $request->hasSession()) {
            $request->setSession($this['session']->driver());
        }

        return $request;
    }

    /**
     * Prepare the given value as a Response object.
     *
     * @param  mixed  $value
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function prepareResponse($value)
    {
        if (! $value instanceof SymfonyResponse) $value = new Response($value);

        return $value->prepare($this['request']);
    }

    /**
     * Set the application's deferred services.
     *
     * @param  array  $services
     * @return void
     */
    public function setDeferredServices(array $services)
    {
        $this->deferredServices = $services;
    }

    /**
     * Determine if the application is ready for responses.
     *
     * @return bool
     */
    public function readyForResponses()
    {
        return $this->booted;
    }

    /**
     * Throw an HttpException with the given data.
     *
     * @param  int     $code
     * @param  string  $message
     * @param  array   $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function abort($code, $message = '', array $headers = array())
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        } else {
            throw new HttpException($code, $message, null, $headers);
        }
    }

    /**
     * Register a 404 error handler.
     *
     * @param  Closure  $callback
     * @return void
     */
    public function missing(Closure $callback)
    {
        $this->error(function(NotFoundHttpException $e) use ($callback)
        {
            return call_user_func($callback, $e);
        });
    }

    /**
     * Register an application error handler.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function error(Closure $callback)
    {
        $this['exception']->error($callback);
    }

    /**
     * Register an error handler at the bottom of the stack.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function pushError(Closure $callback)
    {
        $this['exception']->pushError($callback);
    }

    /**
     * Register an error handler for fatal errors.
     *
     * @param  Closure  $callback
     * @return void
     */
    public function fatal(Closure $callback)
    {
        $this->error(function(FatalErrorException $e) use ($callback)
        {
            return call_user_func($callback, $e);
        });
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        return file_exists($this['path.storage'].'/SITEDOWN');
    }

    /**
     * Register a maintenance mode event listener.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function down(Closure $callback)
    {
        $this['events']->listen('illuminate.app.down', $callback);
    }

    /**
     * Get the configuration loader instance.
     *
     * @return \Config\LoaderInterface
     */
    public function getConfigLoader()
    {
        return new LoaderManager();
    }

    /**
     * Get the Service Provider Repository instance.
     *
     * @return \Core\Providers
     */
    public function getProviderRepository()
    {
        $path = APPDIR .'Storage';

        return new ProviderRepository($path);
    }

    /**
     * Set the application request for the console environment.
     *
     * @return void
     */
    public function setRequestForConsoleEnvironment()
    {
        $url = $this['config']->get('app.url', 'http://localhost');

        $parameters = array($url, 'GET', array(), array(), array(), $_SERVER);

        $this->refreshRequest(static::onRequest('create', $parameters));
    }

    /**
     * Call a method on the default request class.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function onRequest($method, $parameters = array())
    {
        return forward_static_call_array(array(static::requestClass(), $method), $parameters);
    }

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this['config']->get('app.locale');
    }

    /**
     * Set the current application locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this['config']->set('app.locale', $locale);

        $this['translator']->setLocale($locale);

        $this['events']->fire('locale.changed', array($locale));
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        $aliases = array(
            'app'            => 'Foundation\Application',
            'auth'           => 'Auth\AuthManager',
            'cache'          => 'Cache\CacheManager',
            'cache.store'    => 'Cache\Repository',
            'auth.reminder.repository' => 'Auth\Reminders\ReminderRepositoryInterface',
            'config'         => 'Config\Repository',
            'cookie'         => 'Cookie\CookieJar',
            'encrypter'      => 'Encryption\Encrypter',
            'db'             => 'Database\DatabaseManager',
            'events'         => 'Events\Dispatcher',
            'hash'           => 'Hashing\HasherInterface',
            'log'            => 'Log\Writer',
            'mailer'         => 'Mail\Mailer',
            'paginator'      => 'Pagination\Environment',
            'auth.reminder'  => 'Auth\Reminders\PasswordBroker',
            'redirect'       => 'Routing\Redirector',
            'request'        => 'Http\Request',
            'router'         => 'Routing\Router',
            'session'        => 'Session\SessionManager',
            'session.store'  => 'Session\Store',
            'validator'      => 'Validation\Factory',
        );

        foreach ($aliases as $key => $alias) {
            $this->alias($key, $alias);
        }
    }

    /**
     * Dynamically access application services.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set application services.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}
