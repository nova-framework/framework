<?php

namespace App\Modules\Demo\Controllers\Database;

use App\Models\Entities\Car;
use App\Modules\Demo\Core\BaseController;


class Service extends BaseController
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
        $this->title('Basic MySQL DBAL Service Demo');

        // Get Car service
        $carservice = \Nova\Database\Manager::getService('car');

        // Demo 2 Make new Car entity
        $demo2 = $carservice->getAll();


        // Demo 3 Insert new Car
        $car = new Car(); // Create our entity
        $car->make = 'BMW';
        $car->model = '1-serie';
        $car->costs = 40000;

        $carservice->create($car); // CREATE operation

        // Output our car model again, you will see that the carid is now filled in!
        $demo3 = $car;


        // Demo 4 delete last car instance
        $demo4 = $carservice->delete($car);


        $this->set('demo2', $demo2);
        $this->set('demo3', $demo3);
        $this->set('demo4', $demo4);
    }


    public function basicSqlite()
    {
        $this->title('Basic SQLite DBAL Service Demo');

        // Get Car service
        $carservice = \Nova\Database\Manager::getService('car', 'sqlite');

        // Demo 2 Make new Car entity
        $demo2 = $carservice->getAll();


        // Demo 3 Insert new Car
        $car = new Car(); // Create our entity
        $car->make = 'BMW';
        $car->model = '1-serie';
        $car->costs = 40000;

        $carservice->create($car); // CREATE operation

        // Output our car model again, you will see that the carid is now filled in!
        $demo3 = $car;


        // Demo 4 delete last car instance
        $demo4 = $carservice->delete($car);


        $this->set('demo2', $demo2);
        $this->set('demo3', $demo3);
        $this->set('demo4', $demo4);
    }
}
