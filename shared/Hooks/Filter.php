<?php

namespace Shared\Hooks;

use Shared\Hooks\HookDispatcher;


class Filter extends HookDispatcher
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
     * @param  array $arguments Arguments passed to the filter
     * @return string Always returns the value
     */
    public function fire($action, array $arguments)
    {
        $value = array_shift($arguments); // The first argument is always the value.

        if (empty($listeners = $this->getListeners($action))) {
            return $value;
        }

        $this->firing[] = $hook;

        foreach ($listeners as $listener) {
            $parameters = array_slice($arguments, 0, (int) $listener['arguments']);

            // Prepend the value to parameters.
            array_unshift($parameters, $value);

            // Filter the value.
            $value = call_user_func_array(
                $this->resolveCallback($listener['callback']), $parameters
            );
        }

        array_pop($this->firing);

        return $value;
    }
}
