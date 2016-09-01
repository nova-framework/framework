<?php

namespace Language;

use Foundation\Application;
use Language\Language;


class LanguageManager
{
    /**
     * The Application instance.
     *
     * @var \Foundation\Application
     */
    protected $app;

    /**
     * The default locale being used by the translator.
     *
     * @var string
     */
    protected $locale;

    /**
     * The know Languages.
     *
     * @var array
     */
    protected $languages = array();

    /**
     * The active Language instances.
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Create new Language Manager instance.
     *
     * @param  \core\Application  $app
     * @return void
     */
    function __construct(Application $app, $locale)
    {
        $this->app = $app;

        $this->locale = $locale;

        // Setup the know Languages.
        $this->languages = $app['config']['languages'];
    }

    /**
     * Get instance of Language with domain and code (optional).
     * @param string $domain Optional custom domain
     * @param string $code Optional custom language code.
     * @return Language
     */
    public function instance($domain = 'app', $locale = null)
    {
        $locale = $locale ?: $this->locale;

        // The ID code is something like: 'en/system', 'en/app' or 'en/file_manager'
        $id = $locale .'/' .$domain;

        // Initialize the domain instance, if not already exists.
        if (! isset($this->instances[$id])) {
            $this->instances[$id] = new Language($this, $domain, $locale);
        }

        return $this->instances[$id];
    }

    /**
     * Get the know Languages.
     *
     * @return string
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the default locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Dynamically pass methods to the default instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->instance(), $method), $parameters);
    }

}
