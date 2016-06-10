<?php
/**
 * Environment - Implements a Environment Detector.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Core;

use Closure;


class Environment
{
    /**
     * Detect the application's current environment.
     *
     * @param  array|string  $environments
     * @param  array|null  $consoleArgs
     * @return string
     */
    public function detect($environments, $consoleArgs = null)
    {
        if ($consoleArgs)
        {
            return $this->detectConsoleEnvironment($environments, $consoleArgs);
        }
        else
        {
            return $this->detectWebEnvironment($environments);
        }
    }

    /**
     * Set the application environment for a web request.
     *
     * @param  array|string  $environments
     * @return string
     */
    protected function detectWebEnvironment($environments)
    {
        if ($environments instanceof Closure) {
            return call_user_func($environments);
        }

        foreach ($environments as $environment => $hosts) {
            foreach ((array) $hosts as $host) {
                if ($this->isMachine($host)) return $environment;
            }
        }

        return 'production';
    }

    /**
     * Set the application environment from command-line arguments.
     *
     * @param  mixed   $environments
     * @param  array  $args
     * @return string
     */
    protected function detectConsoleEnvironment($environments, array $args)
    {
        if (! is_null($value = $this->getEnvironmentArgument($args))) {
            return head(array_slice(explode('=', $value), 1));
        } else {
            return $this->detectWebEnvironment($environments);
        }
    }

    /**
     * Get the environment argument from the console.
     *
     * @param  array  $args
     * @return string|null
     */
    protected function getEnvironmentArgument(array $args)
    {
        return array_first($args, function($k, $v)
        {
            return starts_with($v, '--env');
        });
    }

    /**
     * Determine if the name matches the machine name.
     *
     * @param  string  $name
     * @return bool
     */
    public function isMachine($name)
    {
        return str_is($name, gethostname());
    }

}
