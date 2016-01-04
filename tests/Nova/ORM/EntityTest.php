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
}
