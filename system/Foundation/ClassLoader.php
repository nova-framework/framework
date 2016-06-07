<?php
/**
 * ClassLoader - Implements a Class Loader.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Foundation;


class ClassLoader
{
    /**
     * The array of dirs.
     *
     * @var array
     */
    protected $dirs;

    /**
     * Indicates if a loader has been registered.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * The singleton instance of the loader.
     *
     * @var \Foundation\ClassLoader
     */
    protected static $instance;

    /**
     * Create a new class alias loader instance.
     *
     * @param  array  $dirs
     * @return void
     */
    public function __construct(array $dirs = array())
    {
        $this->dirs = $dirs;
    }

    /**
     * Get or create the singleton alias loader instance.
     *
     * @param  array  $dirs
     * @return \Foundation\ClassLoader
     */
    public static function getInstance(array $dirs = array())
    {
        if (is_null(static::$instance)) static::$instance = new static($dirs);

        $dirs = array_merge(static::$instance->getDirs(), $dirs);

        static::$instance->setDirs($dirs);

        return static::$instance;
    }

    /**
     * Load class if exists.
     *
     * @param  string  $class
     * @return void
     */
    public function load($class)
    {
        $class = str_replace('\\', '/', $class);

        foreach ($this->dirs as $dir) {
            if (file_exists($dir.'/'.$class.'.php')) {
                require_once $dir.'/'.$class.'.php';

                break;
            }
        }
    }

    /**
     * Add an dir to the loader.
     *
     * @param  string  $dir
     * @return void
     */
    public function dir($dir)
    {
        $this->dirs[] = $dir;
    }

    /**
     * Register the loader on the auto-loader stack.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->registered) {
            $this->prependToLoaderStack();

            $this->registered = true;
        }
    }

    /**
     * Prepend the load method to the auto-loader stack.
     *
     * @return void
     */
    protected function prependToLoaderStack()
    {
        spl_autoload_register(array($this, 'load'), true, true);
    }

    /**
     * Get the registered dirs.
     *
     * @return array
     */
    public function getDirs()
    {
        return $this->dirs;
    }

    /**
     * Set the registered dirs.
     *
     * @param  array  $dirs
     * @return void
     */
    public function setDirs(array $dirs)
    {
        $this->dirs = $dirs;
    }

    /**
     * Set the value of the singleton class loader.
     *
     * @param  \Foundation\ClassLoader  $loader
     * @return void
     */
    public static function setInstance($loader)
    {
        static::$instance = $loader;
    }
}
