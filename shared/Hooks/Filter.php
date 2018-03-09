<?php

namespace Shared\Hooks;

use Shared\Hooks\ActionHookDispatcher;


class Filter extends ActionHookDispatcher
{

    /**
     * Filters a value
     *
     * @return string Always returns the value
     */
    public function apply()
    {
        $parameters = func_get_args();

        $action = array_shift($parameters);

        return $this->fire($action, $parameters);
    }

    /**
     * Filters a value
     *
     * @param  string $action Name of filter
     * @param  array $args Arguments passed to the filter
     * @return string Always returns the value
     */
    public function fire($action, array $arguments)
    {
        // Get the value, the first argument is always the value.
        $value = isset($arguments[0]) ? $arguments[0] : '';

        if (empty($listeners = $this->getListeners($action))) {
            return $value;
        }

        $count = count($arguments);

        foreach ($listeners as $listener) {
            $parameters = array($value);

            $limit = min($count, $listener['arguments']);

            for ($i = 1; $i < $limit; $i++) {
                if (isset($arguments[$i])) {
                    $parameters[] = $arguments[$i];
                }
            }

            $callback = $this->resolveCallback($listener['callback']);

            // Filter the value.
            $value = call_user_func_array($callback, $parameters);
        }

        return $value;
    }
}
