<?php
/**
 * Structure Tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date January 4th, 2016
 */

namespace Nova\Tests\ORM;


use App\Modules\Demo\Models\Entities\Car;
use Nova\ORM\Structure;

class StructureTest extends \PHPUnit_Framework_TestCase
{

    /** @var Car */
    private $car;

    public function setUp()
    {
        // Force the index of Car entity
        $this->car = new Car();
    }

    /**
     * @covers \Nova\ORM\Structure::getTableColumns
     * @covers \Nova\ORM\Structure::indexEntity
     */
    public function testTableStructure()
    {
        $columns = Structure::getTableColumns($this->car);

        $this->assertEquals(4, count($columns));
    }
}
