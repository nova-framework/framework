<?php

namespace Routing;

use Http\Request;
use Support\Str;


class UrlGenerator
{
    /**
     * The Request instance.
     *
     * @var \Http\Request
     */
    protected $request;

    /**
     * Create a new UrlGenerator instance.
     *
     * @param  \Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the full URL for the current request.
     *
     * @return string
     */
    public function full()
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public function current()
    {
        return $this->to($this->request->getPathInfo());
    }

    /**
     * Get the URL for the previous request.
     *
     * @return string
     */
    public function previous()
    {
        return $this->to($this->request->headers->get('referer'));
    }

    /**
     * Generate a absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool  $secure
     * @return string
     */
    public function to($path, $extra = array(), $secure = null)
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
     * Generate a secure, absolute URL to the given path.
     *
     * @param  string  $path
     * @param  array   $parameters
     * @return string
     */
    public function secure($path, $parameters = array())
    {
        return $this->to($path, $parameters, true);
    }

    /**
     * Generate a URL to an application asset.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) return $path;

        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = $this->getRootUrl($this->getScheme($secure));

        return $this->removeIndex($root).'/'.trim($path, '/');
    }

    /**
     * Remove the index.php file from a path.
     *
     * @param  string  $root
     * @return string
     */
    protected function removeIndex($root)
    {
        $i = 'index.php';

        return str_contains($root, $i) ? str_replace('/' .$i, '', $root) : $root;
    }

    /**
     * Generate a URL to a secure asset.
     *
     * @param  string  $path
     * @return string
     */
    public function secureAsset($path)
    {
        return $this->asset($path, true);
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

        return preg_replace('~' .$start .'~', $scheme, $root, 1);
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
        return trim($root .'/' .trim($path.'/'.$tail, '/'), '/');
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

    /**
     * Get the Request instance.
     *
     * @return  \Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Nova\Http\Request  $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
