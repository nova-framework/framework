<?php

namespace Shared\Hooks;

use Shared\Hooks\HookDispatcher;


class Action extends HookDispatcher
{

    /**
     * Fire an action
     *
     * @param  string  $action Name of action
     * @param  array  $arguments Arguments passed to the filter
     * @return void
     */
    public function fire($action, array $arguments)
    {
        if (empty($listeners = $this->getListeners($action))) {
            return;
        }

        $this->firing[] = $action;

        foreach ($listeners as $listener) {
            $callback = $listener['callback'];

            // Extract an exact number of parameters.
            $parameters = array_slice($arguments, 0, (int) $listener['arguments']);

            call_user_func_array($callback, $parameters);
        }

        array_pop($this->firing);
    }
}
