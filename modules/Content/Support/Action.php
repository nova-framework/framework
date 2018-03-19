<?php

namespace Modules\Content\Support;

use Modules\Content\Support\ActionHookDispatcher;


class Action extends ActionHookDispatcher
{

    /**
     * Run an action
     *
     * @return void
     */
    public function run()
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

        foreach ($listeners as $listener) {
            $parameters = array();

            for ($i = 0; $i < $listener['arguments']; $i++) {
                if (isset($arguments[$i])) {
                    $parameters[] = $arguments[$i];
                }
            }

            $callback = $this->resolveCallback($listener['callback']);

            call_user_func_array($callback, $parameters);
        }
    }
}
