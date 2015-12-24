<?php
namespace App\Controllers;

use App\Services\Database\Car;
use App\Services\Database\CarLite;
use Nova\Core\View;
use Nova\Core\Controller;
use Nova\Database\Manager;

/*
*
* Demo controller
*/
class Demo extends Controller
{

    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define Index method
     */
    public function index()
    {
        echo 'hello';
    }

    public function test($param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        $params = array(
            'param1' => $param1,
            'param2' => $param2,
            'param3' => $param3,
            'param4' => $param4
        );

        echo '<pre>'.var_export($params, true).'</pre>';
    }

    public function catchAll($str)
    {
        echo htmlspecialchars($str, ENT_COMPAT, 'ISO-8859-1', true);
    }

    /**
     * Basic database demonstration.
     *
     * @throws \Exception
     */
    public function database()
    {
        echo "<pre>Plain:<br>";

        // Use it without the Services:
        $engine = Manager::getEngine();
        $result_plain = $engine->select('SELECT * FROM ' . DB_PREFIX . 'car', true);
        var_dump($result_plain);

        // Using the select and prefix the SELECT in the sql is optional for the MySQL Engine!
        // WARNING: this is using an old funciton syntax.
        $result_plain = $engine->select('* FROM '.DB_PREFIX.'car', true);
        var_dump($result_plain);

        echo "<br><br>Service:<br>";

        // Use with the Car service:
        $service = new Car();
        $result_service = $service->getAll();
        var_dump($result_service);

        echo "</pre>";
    }

    /**
     * Demonstrate how the inserts will work. With service and without.
     *
     * @throws \Exception
     */
    public function databaseInsert()
    {
        echo "Demo of inserting with engine's<br><pre>";

        // Make new car with mysql engine
        $engine = Manager::getEngine();

        $car1 = array('makeid' => 1, 'model' => 'Model X', 'type' => '90D', 'costs' => 75000);
        $car2 = array('makeid' => 1, 'model' => 'Model X', 'type' => 'P90D', 'costs' => 95000);

        $cars = array($car1, $car2);

        $result = $engine->insert(DB_PREFIX . 'car', $cars, true, true); // True means to use transactions, second true is for using multiple inserts.
        var_dump($result);



        // Insert with service
        // Make cars
        $care1 = new \App\Models\Entities\Car();
        $care1->makeid = 1;
        $care1->model = 'Model X';
        $care1->type = '90D';
        $care1->costs = 75000;

        $care2 = new \App\Models\Entities\Car();
        $care2->makeid = 1;
        $care2->model = 'Model X';
        $care2->type = 'P90D';
        $care2->costs = 96000;

        $cares = array($care1, $care2);

        // Service and insert
        $service = new Car();
        $result = $service->create($cares); // You could also give just one of the new Car entities.
        var_dump($result);


        // Sample update
        $care1->model .= "!!";
        var_dump($service->update($care1));

        // Sample delete
        var_dump($service->delete($care2));

        echo "</pre>";
    }

    /**
     * Demonstrate how the SQLite engine works.
     * The same as the MySQL Engine!
     *
     * @throws \Exception
     */
    public function databaseSqlite()
    {
        echo "<pre>";
        $engine = Manager::getEngine('sqlite');

        // You can use simple query when not require to bind any values.
        $result = $engine->raw("SELECT * FROM " . DB_PREFIX . "car;", true);
        var_dump($result);

        // Don't use it when you have a where, or inject dynamic parameters
        $result = $engine->selectAll("SELECT * FROM " . DB_PREFIX . "car WHERE model LIKE :model", array(':model' => 'Model S'));
        var_dump($result);
        echo "<br><br><br>";

        // Insert
        $car1 = array(
            'make' => 'tesla',
            'model' => 'Model X P90',
            'costs' => 75000
        );
        $car2 = array(
            'make' => 'tesla',
            'model' => 'Model X P90D',
            'costs' => 95000
        );

        // Insert it!
        $result = $engine->superInsert(DB_PREFIX . 'car', array($car1, $car2));
        var_dump($result);

        // Update something!
        $result = $engine->update(DB_PREFIX . 'car', array('model' => 'Model X ?'), array('model' => 'Model X P90D'));
        var_dump($result);

        // Delete the P90, not the P90D!
        $result = $engine->delete(DB_PREFIX . 'car', array('model' => 'Model X P90'));
        var_dump($result);

        var_dump($engine->getTotalQueries());


        // Service
        $carservice = Manager::getService('Car', 'sqlite');
        var_dump($carservice);
        echo "SERVICES: ===============<BR>";
        var_dump($carservice->getAll());

        $carnew = new \App\Models\Entities\Car();
        $carnew->make = 'BMW';
        $carnew->model = 'test';
        $carnew->costs = 10;
        $result = $carservice->create($carnew);
        var_dump($result);

        echo "SERVICES: ===============<BR>";
        var_dump($carservice->getAll());

        echo "</pre>";
    }
}
