<?php


namespace Nova\ORM;

use App\Modules\Demo\Models\Entities\Car;

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
     * @covers \Nova\ORM\Entity::get
     * @throws \Exception
     */
    public function testGetEntity()
    {
        $car = Car::get(1);

        $this->assertInstanceOf('\App\Modules\Demo\Models\Entities\Car', $car);

        var_dump($car);
    }


    public function testSave()
    {
        $car = new Car();

        $car->make = 'Nova Cars';
        $car->model = 'Framework_ORM_Test_1';
        $car->costs = 50000;

        $insert = $car->save();


        $car->costs = 55000;
        $update = $car->save();

        var_dump($update);
    }
}
