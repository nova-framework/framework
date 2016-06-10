<?php
/**
 * Application - Implements the Application.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Illuminate\Container\Container;
use Core\EnvironmentDetector;
use Core\Providers as ProviderRepository;
use Config\FileLoader;
use Http\Request;
use Http\Response;

use Events\EventServiceProvider;
use Exception\ExceptionServiceProvider;
use Routing\RoutingServiceProvider;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;


class Application extends Container
{
    /**
     * The Nova Framework version.
     *
     * @var string
     */
    const VERSION = '3.53.1';

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
    public function __construct()
    {
        $this->registerBaseBindings($request ?: $this->createNewRequest());

        $this->registerBaseServiceProviders();
    }

    /**
     * Create a new Request instance from the Request class.
     *
     * @return \Http\Request
     */
    protected function createNewRequest()
    {
        return forward_static_call(array(static::$requestClass, 'createFromGlobals'));
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

        $this->instance('Container\Container', $this);
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
        $this['exception']->register($this->environment());

        $this['exception']->setDebug($this['config']['app.debug']);
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
     * Determine if we are running unit tests.
     *
     * @return bool
     */
    public function runningUnitTests()
    {
        return $this['env'] == 'testing';
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

        $this->dispatch($request);
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

        //return $router->dispatch($this->prepareRequest($request));
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
            $session = $this['session.store'];

            $request->setSession($session);
        }

        return $request;
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
     * Get the configuration loader instance.
     *
     * @return \Config\LoaderInterface
     */
    public function getConfigLoader()
    {
        return new FileLoader();
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
     * Get the Application URL.
     *
     * @param  string  $path
     * @return string
     */
    public function url($path = '')
    {
        return site_url($path);
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        $aliases = array(
            'app'            => 'Core\Application',
            'auth'           => 'Auth\AuthManager',
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
