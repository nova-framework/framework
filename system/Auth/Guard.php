<?php

namespace Auth;

use Cookie\CookieJar;
use Events\Dispatcher;
use Session\Store as SessionStore;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Guard
{
    /**
     * The currently authenticated user.
     *
     * @var \Auth\UserInterface
     */
    protected $user;

    /**
     * The user we last attempted to retrieve.
     *
     * @var \Auth\UserInterface
     */
    protected $lastAttempted;

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;

    /**
     * The user provider implementation.
     *
     * @var \Auth\UserProviderInterface
     */
    protected $provider;

    /**
     * The session store used by the guard.
     *
     * @var \Session\Store
     */
    protected $session;

    /**
     * The Illuminate cookie creator service.
     *
     * @var \Cookie\CookieJar
     */
    protected $cookie;

    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The event dispatcher instance.
     *
     * @var \Events\Dispatcher
     */
    protected $events;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $tokenRetrievalAttempted = false;

    /**
     * Create a new authentication guard.
     *
     * @param  \Auth\UserProviderInterface  $provider
     * @param  \Session\Store  $session
     * @return void
     */
    public function __construct(UserProviderInterface $provider, SessionStore $session, Request $request = null)
    {
        $this->session = $session;
        $this->request = $request;
        $this->provider = $provider;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return ! is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return ! $this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Auth\UserInterface|null
     */
    public function user()
    {
        if ($this->loggedOut) return;

        if ( ! is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());

        $user = null;

        if ( ! is_null($id)) {
            $user = $this->provider->retrieveByID($id);
        }

        $recaller = $this->getRecaller();

        if (is_null($user) && ! is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);
        }

        return $this->user = $user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->loggedOut) return;

        return $this->session->get($this->getName()) ?: $this->getRecallerId();
    }

    /**
     * Pull a user from the repository by its recaller ID.
     *
     * @param  string  $recaller
     * @return mixed
     */
    protected function getUserByRecaller($recaller)
    {
        if ($this->validRecaller($recaller) && ! $this->tokenRetrievalAttempted) {
            $this->tokenRetrievalAttempted = true;

            list($id, $token) = explode('|', $recaller, 2);

            $this->viaRemember = ! is_null($user = $this->provider->retrieveByToken($id, $token));

            return $user;
        }
    }

    /**
     * Get the decrypted recaller cookie for the request.
     *
     * @return string|null
     */
    protected function getRecaller()
    {
        return $this->request->cookies->get($this->getRecallerName());
    }

    /**
     * Get the user ID from the recaller cookie.
     *
     * @return string
     */
    protected function getRecallerId()
    {
        if ($this->validRecaller($recaller = $this->getRecaller()))
        {
            return head(explode('|', $recaller));
        }
    }

    /**
     * Determine if the recaller cookie is in a valid format.
     *
     * @param  string  $recaller
     * @return bool
     */
    protected function validRecaller($recaller)
    {
        if ( ! is_string($recaller) || ! str_contains($recaller, '|')) return false;

        $segments = explode('|', $recaller);

        return ((count($segments) == 2) && (trim($segments[0]) !== '') && (trim($segments[1]) !== ''));
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function once(array $credentials = array())
    {
        if ($this->validate($credentials))
        {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = array())
    {
        return $this->attempt($credentials, false, false);
    }

    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param  string  $field
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function basic($field = 'email', Request $request = null)
    {
        if ($this->check()) return;

        $request = $request ?: $this->getRequest();

        // If a username is set on the HTTP basic request, we will return out without
        // interrupting the request lifecycle. Otherwise, we'll need to generate a
        // request indicating that the given credentials were invalid for login.
        if ($this->attemptBasic($request, $field)) return;

        return $this->getBasicResponse();
    }

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param  string  $field
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onceBasic($field = 'email', Request $request = null)
    {
        $request = $request ?: $this->getRequest();

        if (! $this->once($this->getBasicCredentials($request, $field))) {
            return $this->getBasicResponse();
        }
    }

    /**
     * Attempt to authenticate using basic authentication.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string  $field
     * @return bool
     */
    protected function attemptBasic(Request $request, $field)
    {
        if (! $request->getUser()) return false;

        return $this->attempt($this->getBasicCredentials($request, $field));
    }

    /**
     * Get the credential array for a HTTP Basic request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string  $field
     * @return array
     */
    protected function getBasicCredentials(Request $request, $field)
    {
        return array($field => $request->getUser(), 'password' => $request->getPassword());
    }

    /**
     * Get the response for basic authentication.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getBasicResponse()
    {
        $headers = array('WWW-Authenticate' => 'Basic');

        return new Response('Invalid credentials.', 401, $headers);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @param  bool   $login
     * @return bool
     */
    public function attempt(array $credentials = array(), $remember = false, $login = true)
    {
        $this->fireAttemptEvent($credentials, $remember, $login);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($this->hasValidCredentials($user, $credentials)) {
            if ($login) $this->login($user, $remember);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  mixed  $user
     * @param  array  $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return ! is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Fire the attempt event with the arguments.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @param  bool   $login
     * @return void
     */
    protected function fireAttemptEvent(array $credentials, $remember, $login)
    {
        if ($this->events) {
            $payload = array($credentials, $remember, $login);

            $this->events->fire('auth.attempt', $payload);
        }
    }

    /**
     * Register an authentication attempt event listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function attempting($callback)
    {
        if ($this->events) {
            $this->events->listen('auth.attempt', $callback);
        }
    }

    /**
     * Log a user into the application.
     *
     * @param  \Auth\UserInterface  $user
     * @param  bool  $remember
     * @return void
     */
    public function login(UserInterface $user, $remember = false)
    {
        $this->updateSession($user->getAuthIdentifier());

        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);

            $this->queueRecallerCookie($user);
        }

        if (isset($this->events)) {
            $this->events->fire('auth.login', array($user, $remember));
        }

        $this->setUser($user);
    }

    /**
     * Update the session with the given ID.
     *
     * @param  string  $id
     * @return void
     */
    protected function updateSession($id)
    {
        $this->session->put($this->getName(), $id);

        $this->session->migrate(true);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed  $id
     * @param  bool   $remember
     * @return \Auth\UserInterface
     */
    public function loginUsingId($id, $remember = false)
    {
        $this->session->put($this->getName(), $id);

        $this->login($user = $this->provider->retrieveById($id), $remember);

        return $user;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function onceUsingId($id)
    {
        $this->setUser($this->provider->retrieveById($id));

        return ($this->user instanceof UserInterface);
    }

    /**
     * Queue the recaller cookie into the cookie jar.
     *
     * @param  \Auth\UserInterface  $user
     * @return void
     */
    protected function queueRecallerCookie(UserInterface $user)
    {
        $value = $user->getAuthIdentifier().'|'.$user->getRememberToken();

        $this->getCookieJar()->queue($this->createRecaller($value));
    }

    /**
     * Create a remember me cookie for a given ID.
     *
     * @param  string  $value
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function createRecaller($value)
    {
        return $this->getCookieJar()->forever($this->getRecallerName(), $value);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        $this->clearUserDataFromStorage();

        if ( ! is_null($this->user)) {
            $this->refreshRememberToken($user);
        }

        if (isset($this->events)) {
            $this->events->fire('auth.logout', array($user));
        }

        $this->user = null;

        $this->loggedOut = true;
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->forget($this->getName());

        $recaller = $this->getRecallerName();

        $this->getCookieJar()->queue($this->getCookieJar()->forget($recaller));
    }

    /**
     * Refresh the remember token for the user.
     *
     * @param  \Auth\UserInterface  $user
     * @return void
     */
    protected function refreshRememberToken(UserInterface $user)
    {
        $user->setRememberToken($token = str_random(60));

        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * Create a new remember token for the user if one doesn't already exist.
     *
     * @param  \Auth\UserInterface  $user
     * @return void
     */
    protected function createRememberTokenIfDoesntExist(UserInterface $user)
    {
        if (is_null($user->getRememberToken())) {
            $this->refreshRememberToken($user);
        }
    }

    /**
     * Get the cookie creator instance used by the guard.
     *
     * @return \Cookie\CookieJar
     *
     * @throws \RuntimeException
     */
    public function getCookieJar()
    {
        if (! isset($this->cookie)) {
            throw new \RuntimeException("Cookie jar has not been set.");
        }

        return $this->cookie;
    }

    /**
     * Set the cookie creator instance used by the guard.
     *
     * @param  \Cookie\CookieJar  $cookie
     * @return void
     */
    public function setCookieJar(CookieJar $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Events\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Events\Dispatcher
     */
    public function setDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Get the session store used by the guard.
     *
     * @return \Session\Store
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return \Auth\UserProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param  \Auth\UserProviderInterface  $provider
     * @return void
     */
    public function setProvider(UserProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Return the currently cached user of the application.
     *
     * @return \Auth\UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the current user of the application.
     *
     * @param  \Auth\UserInterface  $user
     * @return void
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        $this->loggedOut = false;
    }

    /**
     * Get the current request instance.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request
     * @return \Auth\Guard
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return \Auth\UserInterface
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_'.md5(get_class($this));
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_'.md5(get_class($this));
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }

}
