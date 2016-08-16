<?php

namespace App\Modules\Cron\Adapters;

use App\Modules\Cron\Core\Adapter;


class Test extends Adapter
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
        //
    }

    /**
     * Execute the Adapter assigned tasks.
     */
    public function execute()
    {
        return 'Hello from the CRON Adapter';
    }

}
