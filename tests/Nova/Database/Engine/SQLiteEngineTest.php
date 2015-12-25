<?php


namespace Nova\Tests\Database\Engine;

/**
 * Class SQLiteEngineTest
 * @package Nova\Tests\Database\Engine
 *
 * @coversDefaultClass \Nova\Database\Engine\SQLite
 */
class SQLiteEngineTest extends \PHPUnit_Framework_TestCase
{
    private $engine;

    private function prepareEngine()
    {
        $this->engine = \Nova\Database\Manager::getEngine('sqlite');
        $this->assertInstanceOf('\Nova\Database\Engine\SQLite', $this->engine);
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::getDriverName
     * @covers \Nova\Database\Engine\SQLite::getConfiguration
     * @covers \Nova\Database\Engine\SQLite::getConnection
     * @covers \Nova\Database\Engine\SQLite::getDriverCode
     */
    public function testEngineBasics()
    {
        $this->prepareEngine();

        $this->assertInstanceOf('\PDO', $this->engine->getConnection());

        $this->assertEquals('SQLite Driver', $this->engine->getDriverName());
        $this->assertEquals(\Nova\Database\Manager::DRIVER_SQLITE, $this->engine->getDriverCode());
        $this->assertEquals(\Nova\Config::get('database')['sqlite']['config'], $this->engine->getConfiguration());

        $this->assertGreaterThanOrEqual(0, $this->engine->getTotalQueries());
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::select
     * @covers \Nova\Database\Engine\SQLite::selectOne
     * @covers \Nova\Database\Engine\SQLite::selectAll
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