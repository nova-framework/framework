<?php

namespace Cron;

use Cron\Adapter;
use Events\Dispatcher;


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
    public function register($callback, $priority = 0)
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
     * Execute the CRON.
     *
     * @param  string  $filter
     * @param  \Nova\Http\Request   $request
     * @param  \Nova\Http\Response  $response
     * @return mixed
     */
    public function execute()
    {
        // Fire the CRON Event and retrieve the results.
        $responses = $this->events->fire('cron.execute');

        // Extract the not null items from the responses.
        $result = array_filter($responses, function($value)
        {
            return ! is_null($value);
        });

        return $result;
    }

}
