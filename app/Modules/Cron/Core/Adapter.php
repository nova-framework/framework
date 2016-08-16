<?php

namespace App\Modules\Cron\Core;


abstract class Adapter
{

    /**
     * Create a new Adapter instance.
     */
    public function __construct($config = array())
    {
        //
    }

    /**
     * Configure the Adapter from the given options.
     */
    public function config($config = array())
    {
        return $this;
    }

    /**
     * Execute the Adapter assigned tasks.
     */
    abstract public function execute();

}
