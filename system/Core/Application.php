<?php
/**
 * Application - Implements the Application.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Illuminate\Container\Container;

use Core\Environment as EnvironmentDetector;
use Core\Providers as ProviderRepository;
use Config\FileLoader;
use Encryption\DecryptException;
use Helpers\Profiler;
use Http\Request;
use Http\Response;
use Forensics\Profiler as QuickProfiler;
use Session\SessionInterface;
use Support\Facades\Facade;

use Events\EventServiceProvider;
use Exception\ExceptionServiceProvider;
use Routing\RoutingServiceProvider;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Closure;


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

        //$this->processRequestCookies($request);

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
        $response = $this->handle($request);

        $this->finish($request, $response);
    }

    /**
     * Handle the given Request and get the Response.
     *
     * Provides compatibility with BrowserKit functional testing.
     *
     * @implements HttpKernelInterface::handle
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  bool  $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(SymfonyRequest $request, $catch = true)
    {
        try {
            $this->refreshRequest($request = Request::createFromBase($request));

            $this->boot();

            return $this->dispatch($request);
        } catch (\Exception $e) {
            if ($this->runningUnitTests()) throw $e;

            return $this['exception']->handleException($e);
        }
    }

    /**
     * Refresh the bound request instance in the container.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function refreshRequest(Request $request)
    {
        $this->processRequestCookies($request);

        $this->instance('request', $request);

        Facade::clearResolvedInstance('request');
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

    protected function processRequestCookies(SymfonyRequest $request)
    {
        // Retrieve the Session configuration.
        $config = $this['config']['session'];

        if($config['encrypt'] == false) {
            // The Cookies encryption is disabled.
            return;
        }

        // Get the Encrypter instance.
        $encrypter = $this['encrypter'];

        foreach ($request->cookies as $name => $cookie) {
            if($name == 'PHPSESSID') {
                // Leave alone the PHPSESSID.
                continue;
            }

            try {
                if(is_array($cookie)) {
                    $decrypted = array();

                    foreach ($cookie as $key => $value) {
                        $decrypted[$key] = $encrypter->decrypt($value);
                    }
                } else {
                    $decrypted = $encrypter->decrypt($cookie);
                }

                $request->cookies->set($name, $decrypted);
            } catch (DecryptException $e) {
                $request->cookies->set($name, null);
            }
        }
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

    protected function finish(SymfonyRequest $request, $response)
    {
        $cookieJar = $this['cookie'];

        $session = $this['session.store'];

        // Get the Session Store configuration.
        $config = $this['config']['session'];

        // Store the Session ID in a Cookie.
        $cookie = $cookieJar->make(
            $config['cookie'],
            $session->getId(),
            $config['lifetime'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            false
        );

        $cookieJar->queue($cookie);

        // Save the Session Store data.
        $session->save();

        // Collect the garbage for the Session Store instance.
        $this->collectSessionGarbage($session, $config);

        if(is_null($response)) {
            // No further processing required.
            return;
        }

        // Add all Request and queued Cookies.
        $this->processResponseCookies($response, $config);

        // Finally, minify the Response's Content.
        $this->processResponseContent($response);

        // Prepare the Response instance for sending.
        $response->prepare($request);

        // Send the Response.
        $response->send();
    }

    /**
     * Minify the Response instance Content.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    protected function processResponseContent(SymfonyResponse $response)
    {
        if (! $response instanceof Response) {
            return;
        }

        $content = $response->getContent();

        if(ENVIRONMENT == 'development') {
            // Insert the QuickProfiler Widget in the Response's Content.

            $content = str_replace(
                array(
                    '<!-- DO NOT DELETE! - Forensics Profiler -->',
                    '<!-- DO NOT DELETE! - Profiler -->',
                ),
                array(
                    QuickProfiler::process(true),
                    Profiler::getReport(),
                ),
                $content
            );
        } else if(ENVIRONMENT == 'production') {
            // Minify the Response's Content.

            $search = array(
                '/\>[^\S ]+/s', // Strip whitespaces after tags, except space.
                '/[^\S ]+\</s', // Strip whitespaces before tags, except space.
                '/(\s)+/s'      // Shorten multiple whitespace sequences.
            );

            $replace = array('>', '<', '\\1');

            $content = preg_replace($search, $replace, $content);
        }

        $response->setContent($content);
    }

    /**
     * Remove the garbage from the session if necessary.
     *
     * @param  \Illuminate\Session\SessionInterface  $session
     * @return void
     */
    protected function collectSessionGarbage(SessionInterface $session, array $config)
    {
        $lifeTime = $config['lifetime'] * 60; // The option is in minutes.

        // Here we will see if this request hits the garbage collection lottery by hitting
        // the odds needed to perform garbage collection on any given request. If we do
        // hit it, we'll call this handler to let it delete all the expired sessions.
        if ($this->configHitsLottery($config))  {
            $session->getHandler()->gc($lifeTime);
        }
    }

    /**
     * Add all the queued Cookies to Response instance and encrypt all Cookies.
     *
     * @return void
     */
    protected function processResponseCookies(SymfonyResponse $response, array $config)
    {
        $cookieJar = $this['cookie'];

        // Insert all queued Cookies on the Response instance.
        foreach ($cookieJar->getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        if($config['encrypt'] == false) {
            // The Cookies encryption is disabled.
            return;
        }

        // Get the Encrypter instance.
        $encrypter = $this['encrypter'];

        // Encrypt all Cookies present on the Response instance.
        foreach ($response->headers->getCookies() as $key => $cookie)  {
            if($key == 'PHPSESSID') {
                // Leave alone the PHPSESSID.
                continue;
            }

            // Create a new Cookie with the content encrypted.
            $cookie = new SymfonyCookie(
                $cookie->getName(),
                $encrypter->encrypt($cookie->getValue()),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );

            $response->headers->setCookie($cookie);
        }
    }

    /**
     * Determine if the configuration odds hit the lottery.
     *
     * @param  array  $config
     * @return bool
     */
    protected function configHitsLottery(array $config)
    {
        return (mt_rand(1, $config['lottery'][1]) <= $config['lottery'][0]);
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
            //throw new NotFoundHttpException($message);
        } else {
            //throw new HttpException($code, $message, null, $headers);
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
