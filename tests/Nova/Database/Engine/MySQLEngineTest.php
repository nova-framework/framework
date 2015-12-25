<?php


namespace Nova\Tests\Database\Engine;

/**
 * Class MySQLEngineTest
 * @package Nova\Tests\Database\Engine
 *
 * @coversDefaultClass \Nova\Database\Engine\MySQL
 */
class MySQLEngineTest extends \PHPUnit_Framework_TestCase
{
    private $engine;

    private function prepareEngine()
    {
        $this->engine = \Nova\Database\Manager::getEngine();
        $this->assertInstanceOf('\Nova\Database\Engine\MySQL', $this->engine);
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\MySQL::__construct
     * @covers \Nova\Database\Engine\MySQL::getDriverName
     * @covers \Nova\Database\Engine\MySQL::getConfiguration
     * @covers \Nova\Database\Engine\MySQL::getConnection
     * @covers \Nova\Database\Engine\MySQL::getDriverCode
     */
    public function testEngineBasics()
    {
        $this->prepareEngine();

        $this->assertInstanceOf('\PDO', $this->engine->getConnection());

        $this->assertEquals('MySQL Driver', $this->engine->getDriverName());
        $this->assertEquals(\Nova\Database\Manager::DRIVER_MYSQL, $this->engine->getDriverCode());
        $this->assertEquals(\Nova\Config::get('database')['default']['config'], $this->engine->getConfiguration());

        $this->assertGreaterThanOrEqual(0, $this->engine->getTotalQueries());
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\MySQL::__construct
     * @covers \Nova\Database\Engine\MySQL::select
     * @covers \Nova\Database\Engine\MySQL::selectOne
     * @covers \Nova\Database\Engine\MySQL::selectAll
     */
    public function testSelecting()
    {
        $this->prepareEngine();

        // First, we will get ALL our cars, we will use the selectAll
        // TODO: Add selectAll test

        // TODO: Select One, only get the Model S

        // TODO: Select all, but different fetch method

        // TODO: fetch one, doesn't exists in db!
    }
}