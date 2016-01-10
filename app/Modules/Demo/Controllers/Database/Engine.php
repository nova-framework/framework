<?php

namespace App\Modules\Demo\Controllers\Database;

use App\Modules\Demo\Core\BaseController;

class Engine extends BaseController
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

        //  Demo 2:
        $demo2_example1 = $engine->selectAll("SELECT * FROM " . DB_PREFIX . "car;");

        // Demo 3
        $data = array('make' => 'BMW', 'model' => 'i8', 'costs' => 138000);

        $carid = $engine->insert(DB_PREFIX . 'car', $data);

        $demo3_example1 = $carid;

        // Demo 4
        $demo4_example1 = $engine->delete(DB_PREFIX . 'car', array('carid' => $carid));

        $this->set('demo2_example1', $demo2_example1);
        $this->set('demo3_example1', $demo3_example1);
        $this->set('demo4_example1', $demo4_example1);
    }


    public function basicSqlite()
    {
        $this->title('Basic SQLite Engine Demo');

        // Make engine
        $engine = \Nova\Database\Manager::getEngine('sqlite');

        // Demo 2:
        $demo2_example1 = $engine->selectAll("SELECT * FROM " . DB_PREFIX . "car;");

        // Demo 3
        $data = array('make' => 'BMW', 'model' => 'i8', 'costs' => 138000);

        $carid = $engine->insert(DB_PREFIX . 'car', $data);

        $demo3_example1 = $carid;

        // Demo 4
        $demo4_example1 = $engine->delete(DB_PREFIX . 'car', array('carid' => $carid));

        $this->set('demo2_example1', $demo2_example1);
        $this->set('demo3_example1', $demo3_example1);
        $this->set('demo4_example1', $demo4_example1);
    }
}
