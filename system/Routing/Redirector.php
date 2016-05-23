<?php

namespace Routing;

use Http\Request;
use Http\RedirectResponse;
use Session\Store as SessionStore;
use Support\Str;

class Redirector
{
    /**
     * The Request instance.
     *
     * @var \Http\Request
     */
    protected $request;

    /**
     * The Session Store instance.
     *
     * @var \Session\Store
     */
    protected $session;

    /**
     * Create a new Redirector instance.
     *
     * @param  \Http\Request  $request
     * @return void
     */
    public function __construct(Request $request, SessionStore $session)
    {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Create a new redirect response to the "home" route.
     *
     * @param  int  $status
     * @return \Http\RedirectResponse
     */
    public function home($status = 302)
    {
        return $this->to('/', $status);
    }

    /**
     * Create a new redirect response to the previous location.
     *
     * @param  int    $status
     * @param  array  $headers
     * @return \Http\RedirectResponse
     */
    public function back($status = 302, $headers = array())
    {
        $back = $this->request->headers->get('referer');

        return $this->createRedirect($back, $status, $headers);
    }

    /**
     * Create a new redirect response to the current URI.
     *
     * @param  int    $status
     * @param  array  $headers
     * @return \Http\RedirectResponse
     */
    public function refresh($status = 302, $headers = array())
    {
        return $this->to($this->request->path(), $status, $headers);
    }

    /**
     * Create a new redirect response, while putting the current URL in the session.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Http\RedirectResponse
     */
    public function guest($path, $status = 302, $headers = array(), $secure = null)
    {
        $this->session->put('url.intended', $this->request->fullUrl());

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the previously intended location.
     *
     * @param  string  $default
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Http\RedirectResponse
     */
    public function intended($default = '/', $status = 302, $headers = array(), $secure = null)
    {
        $path = $this->session->get('url.intended', $default);

        $this->session->forget('url.intended');

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the given path.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Http\RedirectResponse
     */
    public function to($path, $status = 302, $headers = array(), $secure = null)
    {
        $path = $this->createUrl($path, array(), $secure);

        return $this->createRedirect($path, $status, $headers);
    }

    /**
     * Create a new redirect response to an external URL (no validation).
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Http\RedirectResponse
     */
    public function away($path, $status = 302, $headers = array())
    {
        return $this->createRedirect($path, $status, $headers);
    }

    /**
     * Create a new redirect response to the given HTTPS path.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Http\RedirectResponse
     */
    public function secure($path, $status = 302, $headers = array())
    {
        return $this->to($path, $status, $headers, true);
    }

    /**
     * Generate a absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool  $secure
     * @return string
     */
    protected function createUrl($path, $extra = array(), $secure = null)
    {
        if ($this->isValidUrl($path)) return $path;

        $scheme = $this->getScheme($secure);

        $tail = implode('/', array_map(
            'rawurlencode', (array) $extra)
        );

        $root = $this->getRootUrl($scheme);

        return $this->trimUrl($root, $path, $tail);
    }

    /**
     * Create a new redirect response.
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Http\RedirectResponse
     */
    protected function createRedirect($path, $status, $headers)
    {
        $redirect = new RedirectResponse($path, $status, $headers);

        if (isset($this->session)) {
            $redirect->setSession($this->session);
        }

        $redirect->setRequest($this->request);

        return $redirect;
    }

    /**
     * Get the URL generator instance.
     *
     * @return  \Http\Request
     */
    public function getUrlRequest()
    {
        return $this->request;
    }

    /**
     * Set the active session store.
     *
     * @param  \Illuminate\Session\Store  $session
     * @return void
     */
    public function setSession(SessionStore $session)
    {
        $this->session = $session;
    }

    /**
     * Get the base URL for the request.
     *
     * @param  string  $scheme
     * @param  string  $root
     * @return string
     */
    protected function getRootUrl($scheme, $root = null)
    {
        $root = $root ?: $this->request->root();

        $start = Str::startsWith($root, 'http://') ? 'http://' : 'https://';

        return preg_replace('~'.$start.'~', $scheme, $root, 1);
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    protected function isValidUrl($path)
    {
        if (Str::startsWith($path, array('#', '//', 'mailto:', 'tel:'))) return true;

        return (filter_var($path, FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Format the given URL segments into a single URL.
     *
     * @param  string  $root
     * @param  string  $path
     * @param  string  $tail
     * @return string
     */
    protected function trimUrl($root, $path, $tail = '')
    {
        return trim($root.'/'.trim($path.'/'.$tail, '/'), '/');
    }

    /**
     * Get the scheme for a raw URL.
     *
     * @param  bool    $secure
     * @return string
     */
    protected function getScheme($secure)
    {
        if (is_null($secure)) {
            return $this->request->getScheme() .'://';
        } else {
            return $secure ? 'https://' : 'http://';
        }
    }
}
