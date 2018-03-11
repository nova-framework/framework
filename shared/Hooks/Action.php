<?php

namespace Shared\Hooks;

use Shared\Hooks\ActionHookDispatcher;


class Action extends ActionHookDispatcher
{

    /**
     * Run an action
     *
     * @return void
     */
    public function do()
    {
        $parameters = func_get_args();

        $action = array_shift($parameters);

        return $this->fire($action, $parameters);
    }

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

        $this->firing[] = $hook;

        foreach ($listeners as $listener) {
            $parameters = array_slice($arguments, 0, (int) $listener['arguments']);

            call_user_func_array(
                $this->resolveCallback($listener['callback']), $parameters
            );
        }

        array_pop($this->firing);
    }
}
