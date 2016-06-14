<?php

namespace Session;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Closure;


class Middleware implements HttpKernelInterface
{
    /**
     * The wrapped kernel implementation.
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected $app;

    /**
     * The session manager.
     *
     * @var \Session\SessionManager
     */
    protected $manager;

    /**
     * The callback to determine to use session arrays.
     *
     * @var \Closure|null
     */
    protected $reject;

    /**
     * Create a new session middleware.
     *
     * @param  \Symfony\Component\HttpKernel\HttpKernelInterface  $app
     * @param  \Session\SessionManager  $manager
     * @param  \Closure|null  $reject
     * @return void
     */
    public function __construct(HttpKernelInterface $app, SessionManager $manager, Closure $reject = null)
    {
        $this->app = $app;
        $this->reject = $reject;
        $this->manager = $manager;
    }

    /**
     * Handle the given request and get the response.
     *
     * @implements HttpKernelInterface::handle
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  int   $type
     * @param  bool  $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->checkRequestForArraySessions($request);

        if ($this->sessionConfigured()) {
            $session = $this->startSession($request);

            $request->setSession($session);
        }

        $response = $this->app->handle($request, $type, $catch);

        if ($this->sessionConfigured()) {
            $this->closeSession($session);

            $this->addCookieToResponse($response, $session);
        }

        return $response;
    }

    /**
     * Check the request and reject callback for array sessions.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return void
     */
    public function checkRequestForArraySessions(Request $request)
    {
        if (is_null($this->reject)) return;

        if (call_user_func($this->reject, $request)) {
            $this->manager->setDefaultDriver('array');
        }
    }

    /**
     * Start the session for the given request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Session\SessionInterface
     */
    protected function startSession(Request $request)
    {
        with($session = $this->getSession($request))->setRequestOnHandler($request);

        $session->start();

        return $session;
    }

    /**
     * Close the session handling for the request.
     *
     * @param  \Session\SessionInterface  $session
     * @return void
     */
    protected function closeSession(SessionInterface $session)
    {
        $session->save();

        $this->collectGarbage($session);
    }

    /**
     * Get the full URL for the request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return string
     */
    protected function getUrl(Request $request)
    {
        $url = rtrim(preg_replace('/\?.*/', '', $request->getUri()), '/');

        return $request->getQueryString() ? $url.'?'.$request->getQueryString() : $url;
    }

    /**
     * Remove the garbage from the session if necessary.
     *
     * @param  \Session\SessionInterface  $session
     * @return void
     */
    protected function collectGarbage(SessionInterface $session)
    {
        $config = $this->manager->getSessionConfig();

        if ($this->configHitsLottery($config)) {
            $session->getHandler()->gc($this->getLifetimeSeconds());
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
        return mt_rand(1, $config['lottery'][1]) <= $config['lottery'][0];
    }

    /**
     * Add the session cookie to the application response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface  $session
     * @return void
     */
    protected function addCookieToResponse(Response $response, SessionInterface $session)
    {
        if ($this->sessionIsPersistent($config = $this->manager->getSessionConfig())) {
            $secure = array_get($config, 'secure', false);

            $response->headers->setCookie(new Cookie(
                $session->getName(),
                $session->getId(),
                $this->getCookieLifetime(),
                $config['path'],
                $config['domain'],
                $secure
            ));
        }
    }

    /**
     * Get the session lifetime in seconds.
     *
     *
     */
    protected function getLifetimeSeconds()
    {
        return array_get($this->manager->getSessionConfig(), 'lifetime') * 60;
    }

    /**
     * Get the cookie lifetime in seconds.
     *
     * @return int
     */
    protected function getCookieLifetime()
    {
        $config = $this->manager->getSessionConfig();

        return $config['expireOnClose'] ? 0 : Carbon::now()->addMinutes($config['lifetime']);
    }

    /**
     * Determine if a session driver has been configured.
     *
     * @return bool
     */
    protected function sessionConfigured()
    {
        return ! is_null(array_get($this->manager->getSessionConfig(), 'driver'));
    }

    /**
     * Determine if the configured session driver is persistent.
     *
     * @param  array|null  $config
     * @return bool
     */
    protected function sessionIsPersistent(array $config = null)
    {
        // Some session drivers are not persistent, such as the test array driver or even
        // when the developer don't have a session driver configured at all, which the
        // session cookies will not need to get set on any responses in those cases.
        $config = $config ?: $this->manager->getSessionConfig();

        return ! in_array($config['driver'], array(null, 'array'));
    }

    /**
     * Get the session implementation from the manager.
     *
     * @return \Session\SessionInterface
     */
    public function getSession(Request $request)
    {
        $session = $this->manager->driver();

        $session->setId($request->cookies->get($session->getName()));

        return $session;
    }

}
