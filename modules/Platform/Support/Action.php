<?php

namespace Modules\Platform\Support;

use Modules\Platform\Support\ActionHookDispatcher;


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

        $count = count($arguments);

        foreach ($listeners as $listener) {
            $parameters = array();

            $limit = min($count, $listener['arguments']);

            for ($i = 0; $i < $limit; $i++) {
                if (isset($arguments[$i])) {
                    $parameters[] = $arguments[$i];
                }
            }

            $callback = $this->resolveCallback($listener['callback']);

            call_user_func_array($callback, $parameters);
        }
    }
}
