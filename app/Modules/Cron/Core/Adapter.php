<?php

namespace App\Modules\Cron\Core;


abstract class Adapter
{
    protected $name = 'CRON Adapter';

    /**
     * Create a new Adapter instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the Adapter assigned tasks.
     */
    abstract public function execute();

    /**
     * Return the Adapter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
