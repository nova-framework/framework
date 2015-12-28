<?php
/**
 * Car Service tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 26th, 2015
 */

namespace Nova\Tests\Database\Service;
use App\Modules\Demo\Services\Database\Car;
use Nova\Database\Manager;

/**
 * Class CarServiceTest
 * @package Nova\Tests\Database\Service
 * @coversDefaultClass \Nova\Database\Service
 * @covers \Nova\Database\Manager::getService
 */
class CarServiceTest extends \PHPUnit_Framework_TestCase
{

    /** @var Car */
    private $carservice;

    private function prepareService($linkName = 'default')
    {
        $this->carservice = \Nova\Database\Manager::getService('Car', 'Demo', $linkName);
        $this->assertInstanceOf('\Nova\Database\Service', $this->carservice);
        $this->assertInstanceOf('\App\Modules\Demo\Services\Database\Car', $this->carservice);
    }

    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Service
     * @covers \App\Modules\Demo\Services\Database\Car
     */
    public function testPrepareService()
    {
        // Test MySQL Link
        $this->prepareService();

        // Test SQLite Link
        $this->prepareService('sqlite');
    }


    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Engine
     * @covers \Nova\Database\Engine\Base
     * @covers \Nova\Database\Service
     * @covers \App\Modules\Demo\Services\Database\Car
     * @param string $linkName
     */
    public function testBasicSelecting($linkName = 'default')
    {
        $this->prepareService($linkName);

        // Select all with our custom getAll function
        $all = $this->carservice->getAll();

        $this->assertGreaterThanOrEqual(2, $all);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $all[0]);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $all[1]);

        // Select with id
        $one = $this->carservice->read("SELECT * FROM " . DB_PREFIX . "car LIMIT 1;");
        $this->assertEquals(1, count($one));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $one[0]);
    }

    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Engine
     * @covers \Nova\Database\Engine\Base
     * @covers \Nova\Database\Service
     * @covers \App\Modules\Demo\Services\Database\Car
     */
    public function testBasicSelectingSQLite()
    {
        $this->testBasicSelecting('sqlite');
    }


    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Engine
     * @covers \Nova\Database\Engine\Base
     * @covers \Nova\Database\Service
     * @covers \Nova\Database\Service::create
     * @covers \App\Modules\Demo\Services\Database\Car
     * @param string $linkName
     */
    public function testBasicInserting($linkName = 'default')
    {
        $this->prepareService($linkName);

        // Make new car
        $car = new \App\Modules\Demo\Models\Entities\Car();
        $car->make = 'Nova Cars';
        $car->model = 'FrameworkCar_Service_1';
        $car->costs = 15000;

        // Insert
        $status = $this->carservice->create($car);

        $this->assertNotNull($status);
        $this->assertNotFalse($status);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $status);

        // Insert multiple cars
        $car1 = new \App\Modules\Demo\Models\Entities\Car();
        $car1->make = 'Nova Cars';
        $car1->model = 'FrameworkCar_Service_2';
        $car1->costs = 15000;

        $car2 = new \App\Modules\Demo\Models\Entities\Car();
        $car2->make = 'Nova Cars';
        $car2->model = 'FrameworkCar_Service_3';
        $car2->costs = 15000;

        $cars = array(
            $car1, $car2
        );


        $status = $this->carservice->create($cars);

        $this->assertNotNull($status);
        $this->assertNotFalse($status);
        $this->assertEquals(2, count($status));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $status[0]);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $status[1]);


        $this->cleanup($linkName);
    }


    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Engine
     * @covers \Nova\Database\Engine\Base
     * @covers \Nova\Database\Service
     * @covers \Nova\Database\Service::create
     * @covers \App\Modules\Demo\Services\Database\Car
     */
    public function testBasicInsertingSQLite()
    {
        $this->testBasicInserting('sqlite');
    }



    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Engine
     * @covers \Nova\Database\Engine\Base
     * @covers \Nova\Database\Service
     * @covers \Nova\Database\Service::update
     * @covers \App\Modules\Demo\Services\Database\Car
     * @param string $linkName
     */
    public function testBasicUpdating($linkName = 'default')
    {
        $this->prepareService($linkName);

        // Prepare by inserting a car
        // Make new car
        $car = new \App\Modules\Demo\Models\Entities\Car();
        $car->make = 'Nova Cars';
        $car->model = 'FrameworkCar_Service_1';
        $car->costs = 15000;

        // Insert
        /** @var \App\Modules\Demo\Models\Entities\Car $car */
        $car = $this->carservice->create($car);

        $this->assertNotNull($car);
        $this->assertNotFalse($car);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $car);



        // Update tests
        $car->costs = 20000;
        $car->model = "FrameworkCar_Service_Updated";

        // Update
        $status = $this->carservice->update($car);

        $this->assertNotNull($status);
        $this->assertNotFalse($status);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $status);


        // Select it again
        $one = $this->carservice->read("SELECT * FROM " . DB_PREFIX . "car WHERE model LIKE 'FrameworkCar_Service_Updated';");

        $this->assertEquals(1, count($one));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $one[0]);



        $this->cleanup($linkName);
    }



    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Engine
     * @covers \Nova\Database\Engine\Base
     * @covers \Nova\Database\Service
     * @covers \Nova\Database\Service::update
     * @covers \App\Modules\Demo\Services\Database\Car
     */
    public function testBasicUpdatingSQLite()
    {
        $this->testBasicUpdating('sqlite');
    }




    private function cleanup($linkName)
    {
        $engine = Manager::getEngine($linkName);
        $engine->rawQuery("DELETE FROM " .DB_PREFIX ."car WHERE make LIKE 'Nova Cars';");
    }
}