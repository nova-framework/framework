<?php
/**
 * Builder Tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 28th, 2015
 */

namespace Nova\Tests\Database;

use Nova\Database\TableBuilder;

/**
 * Class BuilderTest
 * @package Nova\Tests\Database
 *
 * @coversDefaultClass \Nova\Database\Builder
 * @covers \Nova\Database\Builder
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Nova\Database\Builder
     */
    public function testSimpleTable()
    {
        $builder = new Builder(null, true);

        $builder->setName('testtable');
        $builder->addField('name', 'varchar(255)', false);
        $builder->addField('email', 'varchar(255', true);

        $sql = $builder->getSQL();

        $expected_1 = "CREATE TABLE testtable (`id` INT(11) NOT null AUTO_INCREMENT, `name` varchar(255) NOT null , `email` varchar(255  null , CONSTRAINT pk_id PRIMARY KEY (`id`))";

        $this->assertEquals($expected_1, $sql);
    }
}
