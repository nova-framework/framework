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
     * Handle the Adapter assigned tasks.
     */
    abstract protected function handle();

    /**
     * Execute the Adapter.
     */
    public function execute()
    {
        $result = $this->handle();

        return array($this->name, $result);
    }

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
