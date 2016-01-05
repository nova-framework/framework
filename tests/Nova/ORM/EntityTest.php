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
     *
     * @param string $linkName
     */
    public function testBasic($linkName = 'default')
    {
        $car = new Car();

        $cols = Structure::getTableColumns($car);

        $this->assertEquals(4, count($cols));
    }

    /**
     * @covers \Nova\ORM\Entity
     */
    public function testBasicSqlite()
    {
        $this->testBasic('sqlite');
    }


    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::find
     * @throws \Exception
     * @param string $linkName
     */
    public function testGetEntity($linkName = null)
    {
        $car = Car::find(1, $linkName);

        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $car);
    }

    /**
 * @covers \Nova\ORM\Entity
 * @covers \Nova\ORM\Entity::find
 */
    public function testGetEntitySqlite()
    {
        $this->testGetEntity('sqlite');
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     * @covers \Nova\ORM\Entity::find
     * @throws \Exception
     * @param string $linkName
     */
    public function testBasicFinding($linkName = null)
    {
        $car = new Car();

        // Insert 1
        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_Search_1';
        $car->costs = 50000;

        $insert = $car->save($linkName);
        $this->assertEquals(1, $insert);

        $car2 = new Car();

        // Insert 2
        $car2->make = 'Nova Cars';
        $car2->model = 'Framework_ORM_Test_Search_2';
        $car2->costs = 50000;

        $insert = $car2->save($linkName);
        $this->assertEquals(1, $insert);



        // Search single
        $searched = Car::find($car->carid, $linkName);
        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);

        //$searched = Car::find(array('model' => 'Framework_ORM_Test_Search_1'), $linkName);
        //$this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);

        //$searched = Car::find(array('model' => 'Framework_ORM_Test_Search_1', 'costs' => 50000), $linkName);
        //$this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $searched);
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     * @covers \Nova\ORM\Entity::find
     * @throws \Exception
     */
    public function testBasicFindingSqlite()
    {
        $this->testBasicFinding('sqlite');
    }


    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     * @param string $linkName
     */
    public function testSave($linkName = null)
    {
        $car = new Car();

        // Insert
        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_1';
        $car->costs = 50000;

        $insert = $car->save($linkName);

        // Check if primary key is filled in
        $carid = $car->carid;
        $this->assertGreaterThan(2, $carid);


        // Update
        $car->costs = 55000;
        $update = $car->save($linkName);

        // Check if the pk is still the same
        $this->assertEquals($carid, $car->carid);
    }


    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::save
     */
    public function testSaveSqlite()
    {
        $this->testSave('sqlite');
    }


    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::delete
     * @param string $linkName
     */
    public function testDelete($linkName = null)
    {
        $car = new Car();

        // Insert
        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_1';
        $car->costs = 50000;

        $car->save($linkName);

        $this->assertGreaterThan(2, $car->carid);
        $carid = $car->carid;

        // Delete
        $car->delete($linkName);

        $car = Car::find($carid, $linkName);

        $this->assertFalse($car);
    }

    /**
     * @covers \Nova\ORM\Entity
     * @covers \Nova\ORM\Entity::delete
     */
    public function testDeleteSqlite()
    {
        $this->testDelete('sqlite');
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
        $connection = Manager::getConnection('default');
        $connection->delete(DB_PREFIX . 'car', array('make' => 'Nova Cars'));

        // CleanUp
        $connection = Manager::getConnection('sqlite');
        $connection->delete(DB_PREFIX . 'car', array('make' => 'Nova Cars'));
    }
}
