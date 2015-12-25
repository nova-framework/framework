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

        // === Select All, No WHERE.

        // First, we will get ALL our cars, we will use the selectAll
        $all = $this->engine->selectAll("SELECT * FROM " . DB_PREFIX . "car");

        $this->assertGreaterThanOrEqual(2, count($all), "Select should return array of 2 or more");

        // Check one in the array
        $this->assertObjectHasAttribute('carid', $all[0]);
        $this->assertObjectHasAttribute('make', $all[0]);
        $this->assertObjectHasAttribute('model', $all[0]);
        $this->assertObjectHasAttribute('costs', $all[0]);


        // === Select One, Only get the Model S
        $model_s = $this->engine->selectOne("SELECT * FROM " . DB_PREFIX . "car WHERE model LIKE 'Model S';");

        $this->assertNotNull($model_s);
        $this->assertObjectHasAttribute('carid', $model_s);
        $this->assertObjectHasAttribute('make', $model_s);
        $this->assertObjectHasAttribute('model', $model_s);
        $this->assertObjectHasAttribute('costs', $model_s);
        $this->assertEquals(1, $model_s->carid);
        $this->assertEquals('Tesla', $model_s->make);



        // === Select All, Fetch With ASSOC
        $all_assoc = $this->engine->selectAll("SELECT * FROM " . DB_PREFIX . "car", array(), \PDO::FETCH_ASSOC);

        $this->assertGreaterThanOrEqual(2, count($all_assoc), "Select should return array of 2 or more");

        // Check one in the array
        $this->assertArrayHasKey('carid', $all_assoc[0]);
        $this->assertArrayHasKey('make', $all_assoc[0]);
        $this->assertArrayHasKey('model', $all_assoc[0]);
        $this->assertArrayHasKey('costs', $all_assoc[0]);



        // === Select One, But this doesn't exists.
        $notthere = $this->engine->selectOne("SELECT * FROM " . DB_PREFIX . "car WHERE make LIKE 'Renault';");

        $this->assertFalse($notthere);
    }


    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::selectAll
     * @covers \Nova\Database\Engine\SQLite::insert
     * @covers \Nova\Database\Engine\SQLite::insertAll
     * @covers \Nova\Database\Engine\SQLite::rawQuery
     * @covers \Nova\Database\Engine\SQLite::commit
     */
    public function testInserting()
    {
        $this->prepareEngine();

        // === Single insert
        $data_1 = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_1', 'costs' => 18000);
        $insert_1 = $this->engine->insert(DB_PREFIX . 'car', $data_1);

        $this->assertNotFalse($insert_1);
        $this->assertGreaterThan(2, $insert_1);


        // === Will try to insert wrong data, should give error/exception
        try{
            $this->engine->insert(null, null);
            $this->assertTrue(false, 'Exception isnt thrown when inserting errornous data!');
        }catch(\Exception $e) {
            $this->assertTrue(true, 'Exception IS thrown when inserting errornous data!');
        }


        // === Tripple, non transaction insert
        $data_2 = array(
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_2', 'costs' => 28000),
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_3', 'costs' => 38000),
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_4', 'costs' => 48000)
        );
        $insert_2 = $this->engine->insertAll(DB_PREFIX . 'car', $data_2, false);

        $this->assertEquals(3, count($insert_2));
        foreach($insert_2 as $key => $value) {
            $this->assertNotEmpty($value);
        }


        // === Tripple, with transaction insert
        $data_3 = array(
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_5', 'costs' => 21000),
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_6', 'costs' => 31000),
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_7', 'costs' => 41000)
        );
        $insert_3 = $this->engine->insertAll(DB_PREFIX . 'car', $data_3, true);

        $this->assertEquals(3, count($insert_3));
        foreach($insert_3 as $key => $value) {
            $this->assertNotEmpty($value);
        }


        // === Triple, with transaction but we will generate one error
        $data_4 = array(
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_8', 'costs' => NULL), // We MUST give costs, generate error!
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_9', 'costs' => 31000),
            array('make' => 'Nova Cars', 'model' => 'FrameworkCar_10', 'costs' => null) // We MUST give costs, generate error!
        );

        try {
            $this->engine->insertAll(DB_PREFIX . 'car', $data_4, true);
            $this->assertFalse(true, 'Inserting error data should give exceptions!');
        }catch(\Exception $e) {
            $this->assertContains("NOT NULL constraint failed", $e->getMessage());
        }

        // Check if the other one is still inserted!
        $wronginserted = $this->engine->selectAll("SELECT * FROM " . DB_PREFIX . "car WHERE model LIKE 'FrameworkCar_9';");

        // Should be false!
        $this->assertFalse($wronginserted, 'Transaction inserts should rollback after detecting errors!');

        // Cleanup all our test cars
        $this->engine->rawQuery("DELETE FROM " . DB_PREFIX . "car WHERE make LIKE 'Nova Cars';");
        $this->engine->commit();
    }
}