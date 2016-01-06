<?php
/**
 * Entity Tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 4th, 2016
 */

namespace Nova\Tests\ORM;

use App\Modules\Demo\Models\Entities\Car;
use Nova\DBAL\Manager;
use Nova\ORM\Structure;
use Nova\Tests\Utils;

class EntityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Nova\ORM\Entity
     */
    public function executeTestBasic()
    {
        $car = new Car();

        $cols = Structure::getTableColumns($car);

        $this->assertEquals(4, count($cols));
    }

    /**
     * @covers \Nova\ORM\Entity
     */
    public function testBasic()
    {
        Utils::switchDatabase('sqlite');
        $this->executeTestBasic();

        Utils::switchDatabase('mysql');
        $this->executeTestBasic();
    }




    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::find
     * @covers \Nova\ORM\Entity::findMany
     * @throws \Exception
     */
    public function executeTestGetEntity()
    {
        $car = Car::find(1);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $car);

        $car = Car::find(2);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $car);
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::find
     */
    public function testGetEntity()
    {
        Utils::switchDatabase('sqlite');
        $this->executeTestGetEntity();

        Utils::switchDatabase('mysql');
        $this->executeTestGetEntity();
    }




    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     * @covers \Nova\ORM\Entity::find
     * @covers \Nova\ORM\Entity::findBy
     * @covers \Nova\ORM\Entity::findMany
     * @throws \Exception
     */
    public function executeTestBasicFinding()
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

        $searched = Car::findBy('model', '=', 'Framework_ORM_Test_Search_1');
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);

        $searched = Car::findBy(array('model' => 'Framework_ORM_Test_Search_1', 'costs' => array('=' => 50000)));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);

        // Search multiple pk's
        $searched = Car::findMany(array($car->carid, $car2->carid));
        $this->assertEquals(2, count($searched));
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched[0]);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched[1]);



        // Query
        $query = Car::query();
        $one = $query->where('carid', 1)->one();
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $one);

        $query = Car::query();
        $all = $query->where('carid', 'IN', array(1, 2))->limit(2)->order('make')->all();

        $this->assertEquals(2, count($all));

        // First one should be the BMW, second Tesla
        $this->assertEquals("BMW", $all[0]->make);
        $this->assertEquals("Tesla", $all[1]->make);
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     * @covers \Nova\ORM\Entity::find
     * @throws \Exception
     */
    public function testBasicFinding()
    {
        Utils::switchDatabase('sqlite');
        $this->executeTestBasicFinding();

        Utils::switchDatabase('mysql');
        $this->executeTestBasicFinding();
    }




    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     */
    public function executeTestSave()
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
     * @covers \Nova\ORM\Entity::save
     */
    public function testSave()
    {
        Utils::switchDatabase('sqlite');
        $this->executeTestSave();

        Utils::switchDatabase('mysql');
        $this->executeTestSave();
    }




    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::delete
     */
    public function executeTestDelete()
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

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::delete
     */
    public function testDeleteSqlite()
    {
        Utils::switchDatabase('sqlite');
        $this->executeTestDelete();

        Utils::switchDatabase('mysql');
        $this->executeTestDelete();
    }



    /**
     * Cleanup
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function tearDown()
    {
        parent::tearDown();

        // CleanUp
        Utils::switchDatabase('sqlite');
        $connection = Manager::getConnection();
        $connection->delete(DB_PREFIX . 'car', array('make' => 'Nova Cars'));

        Utils::switchDatabase('mysql');
        $connection = Manager::getConnection();
        $connection->delete(DB_PREFIX . 'car', array('make' => 'Nova Cars'));
    }
}
