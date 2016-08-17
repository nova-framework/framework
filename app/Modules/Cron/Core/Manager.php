<?php

namespace App\Modules\Cron\Core;

use Events\Dispatcher;

use App\Modules\Cron\Core\Adapter;


class Manager
{
    /**
     * The Event Dispatcher instance.
     *
     * @var \Events\Dispatcher
     */
    protected $events;


    /**
     * Create a new CRON Manager instance.
     *
     * @return void
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->events = $dispatcher;
    }

    /**
     * Register a new Filter with the Router.
     *
     * @param  string  $name
     * @param  string|callable  $callback
     * @param  int     $priority
     * @return void
     */
    public function register($name, $callback, $priority = 0)
    {
        $this->events->listen('cron.execute', $this->parseAdapter($callback), $priority);
    }

    /**
     * Parse the registered Filter.
     *
     * @param  callable|string  $callback
     * @return mixed
     */
    protected function parseAdapter($callback)
    {
        if (is_string($callback) && ! str_contains($callback, '@')) {
            return $callback .'@execute';
        }

        return $callback;
    }

    /**
     * Remove a Cron Adapter.
     *
     * @param string  $name The Adapter name
     */
    public function forget($name)
    {
        $this->events->forget($name);
    }

    /**
     * Execute the CRON.
     *
     * @param  string  $filter
     * @param  \Nova\Http\Request   $request
     * @param  \Nova\Http\Response  $response
     * @return mixed
     */
    public function execute()
    {
        return $this->events->fire('cron.execute');
    }

}
