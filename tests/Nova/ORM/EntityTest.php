<?php
/**
 * Entity Tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 4th, 2016
 */

namespace Nova\ORM;

use App\Modules\Demo\Models\Entities\Car;
use Nova\DBAL\Manager;

class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Nova\ORM\Entity
     */
    public function testBasic()
    {
        $car = new Car();

        $cols = Structure::getTableColumns($car);

        $this->assertEquals(4, count($cols));
    }


    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::find
     * @throws \Exception
     */
    public function testGetEntity()
    {
        $car = Car::find(1);

        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $car);
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     * @covers \Nova\ORM\Entity::find
     * @throws \Exception
     */
    public function testBasicFinding()
    {
        $car = new Car();

        // Insert 1
        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_Search_1';
        $car->costs = 50000;

        $insert = $car->save();
        $this->assertEquals(1, $insert);

        $car2 = new Car();

        // Insert 2
        $car2->make = 'Nova Cars';
        $car2->model = 'Framework_ORM_Test_Search_2';
        $car2->costs = 50000;

        $insert = $car2->save();
        $this->assertEquals(1, $insert);



        // Search single
        $searched = Car::find($car->carid);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);

        $searched = Car::find(array('model' => 'Framework_ORM_Test_Search_1'));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);

        $searched = Car::find(array('model' => 'Framework_ORM_Test_Search_1', 'costs' => 50000));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);
    }


    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     */
    public function testSave()
    {
        $car = new Car();

        // Insert
        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_1';
        $car->costs = 50000;

        $insert = $car->save();

        // Check if primary key is filled in
        $carid = $car->carid;
        $this->assertGreaterThan(2, $carid);


        // Update
        $car->costs = 55000;
        $update = $car->save();

        // Check if the pk is still the same
        $this->assertEquals($carid, $car->carid);
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::delete
     */
    public function testDelete()
    {
        $car = new Car();

        // Insert
        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_1';
        $car->costs = 50000;

        $car->save();

        $this->assertGreaterThan(2, $car->carid);
        $carid = $car->carid;

        // Delete
        $car->delete();

        $car = Car::find($carid);

        $this->assertFalse($car);
    }


    public function tearDown()
    {
        parent::tearDown();

        // CleanUp
        $connection = Manager::getConnection();
        $connection->delete(DB_PREFIX . 'car', array('make' => 'Nova Cars'));
    }
}
