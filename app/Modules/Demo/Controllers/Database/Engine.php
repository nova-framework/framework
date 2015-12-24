<?php

namespace App\Modules\Demo\Controllers\Database;

use App\Core\ThemedController;

class Engine extends ThemedController
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function basicMysql()
    {
        $this->title('Basic MySQL Engine Demo');

        // Make engine
        $engine = \Nova\Database\Manager::getEngine();

        // Example 1, Demo 2:
        $demo2_example1 = $engine->selectAll("SELECT * FROM " . DB_PREFIX . "car;");


        $this->set('demo2_example1', $demo2_example1);
    }
}