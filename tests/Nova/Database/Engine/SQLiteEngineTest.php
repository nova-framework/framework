<?php
/**
 * SQLite Engine Tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 25th, 2015
 */

namespace Nova\Tests\Database\Engine;

/**
 * Class SQLiteEngineTest
 * @package Nova\Tests\Database\Engine
 *
 * @coversDefaultClass \Nova\Database\Engine\SQLite
 *
 * @covers \Nova\Database\Engine\Base
 * @covers \Nova\Database\Engine\SQLite
 */
class SQLiteEngineTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Nova\Database\Engine\SQLite */
    private $engine;

    private function prepareEngine()
    {
        $this->engine = \Nova\Database\Manager::getEngine('sqlite');

        $this->engine->returnType('object');

        $this->assertInstanceOf('\Nova\Database\Engine\SQLite', $this->engine);
    }

    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::getDriverName
     * @covers \Nova\Database\Engine\SQLite::getOptions
     * @covers \Nova\Database\Engine\SQLite::getLink
     * @covers \Nova\Database\Engine\SQLite::getDriverCode
     */
    public function testEngineBasics()
    {
        $this->prepareEngine();

        $this->assertInstanceOf('\PDO', $this->engine->getLink());

        $this->assertEquals('SQLite Driver', $this->engine->getDriverName());
        $this->assertEquals(\Nova\Database\Manager::DRIVER_SQLITE, $this->engine->getDriverCode());
        $this->assertEquals(\Nova\Config::get('database')['sqlite']['config'], $this->engine->getOptions());

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
        $all = $this->engine->selectAll("SELECT * FROM " .DB_PREFIX ."car");

        $this->assertGreaterThanOrEqual(2, count($all), "Select should return array of 2 or more");

        // Check one in the array
        $this->assertObjectHasAttribute('carid', $all[0]);
        $this->assertObjectHasAttribute('make', $all[0]);
        $this->assertObjectHasAttribute('model', $all[0]);
        $this->assertObjectHasAttribute('costs', $all[0]);


        // === Select One, Only get the Model S
        $model_s = $this->engine->selectOne("SELECT * FROM " .DB_PREFIX ."car WHERE model LIKE 'Model S';");

        $this->assertNotNull($model_s);
        $this->assertObjectHasAttribute('carid', $model_s);
        $this->assertObjectHasAttribute('make', $model_s);
        $this->assertObjectHasAttribute('model', $model_s);
        $this->assertObjectHasAttribute('costs', $model_s);
        $this->assertEquals(1, $model_s->carid);
        $this->assertEquals('Tesla', $model_s->make);



        // === Select All, Fetch With ASSOC
        $all_assoc = $this->engine->selectAll("SELECT * FROM " .DB_PREFIX ."car", array(), 'array');

        $this->assertGreaterThanOrEqual(2, count($all_assoc), "Select should return array of 2 or more");

        // Check one in the array
        $this->assertArrayHasKey('carid', $all_assoc[0]);
        $this->assertArrayHasKey('make', $all_assoc[0]);
        $this->assertArrayHasKey('model', $all_assoc[0]);
        $this->assertArrayHasKey('costs', $all_assoc[0]);



        // === Select One, But this doesn't exists.
        $notthere = $this->engine->selectOne("SELECT * FROM " .DB_PREFIX ."car WHERE make LIKE 'Renault';");

        $this->assertFalse($notthere);
    }


    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::selectAll
     * @covers \Nova\Database\Engine\SQLite::insert
     * @covers \Nova\Database\Engine\SQLite::insertBatch
     * @covers \Nova\Database\Engine\SQLite::rawQuery
     * @covers \Nova\Database\Engine\SQLite::commit
     */
    public function testInserting()
    {
        $this->prepareEngine();

        // === Single insert
        $data_1 = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_1', 'costs' => 18000);
        $insert_1 = $this->engine->insert(DB_PREFIX .'car', $data_1);

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
        $insert_2 = $this->engine->insertBatch(DB_PREFIX .'car', $data_2, false);

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
        $insert_3 = $this->engine->insertBatch(DB_PREFIX .'car', $data_3, true);

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
            $this->engine->insertBatch(DB_PREFIX .'car', $data_4, true);
            $this->assertFalse(true, 'Inserting error data should give exceptions!');
        }catch(\Exception $e) {
            $this->assertContains("NULL", $e->getMessage());
        }

        // Check if the other one is still inserted!
        $wronginserted = $this->engine->selectAll("SELECT * FROM " .DB_PREFIX ."car WHERE model LIKE 'FrameworkCar_9';");

        // Should be false!
        $this->assertFalse($wronginserted, 'Transaction inserts should rollback after detecting errors!');

        // Cleanup all our test cars
        $this->engine->rawQuery("DELETE FROM " .DB_PREFIX ."car WHERE make LIKE 'Nova Cars';");
        $this->engine->commit();
    }


    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::selectOne
     * @covers \Nova\Database\Engine\SQLite::insert
     * @covers \Nova\Database\Engine\SQLite::update
     * @covers \Nova\Database\Engine\SQLite::updateBatch
     * @covers \Nova\Database\Engine\SQLite::rawQuery
     */
    public function testUpdating()
    {
        $this->prepareEngine();

        // === Will add our test car fist, We will edit this one a few times
        $data = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_Update_1', 'costs' => 18000);
        $id_1 = $this->engine->insert(DB_PREFIX .'car', $data);

        $data = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_Update_2', 'costs' => 18000);
        $id_2 = $this->engine->insert(DB_PREFIX .'car', $data);

        $data = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_Update_3', 'costs' => 18000);
        $id_3 = $this->engine->insert(DB_PREFIX .'car', $data);

        $this->assertGreaterThanOrEqual(2, $id_1);
        $this->assertGreaterThanOrEqual(2, $id_2);
        $this->assertGreaterThanOrEqual(2, $id_3);

        // === Basic simple update
        $data_update_1 = array('costs' => 20000);
        $update_1 = $this->engine->update(DB_PREFIX .'car', $data_update_1, array('carid' => $id_1));

        $this->assertNotFalse($update_1);

        // Test if it's changed
        $result_1 = $this->engine->selectOne("SELECT * FROM " .DB_PREFIX ."car WHERE carid = :carid", array('carid' => $id_1));

        $this->assertNotNull($result_1);
        $this->assertObjectHasAttribute('carid', $result_1);
        $this->assertObjectHasAttribute('make', $result_1);
        $this->assertObjectHasAttribute('model', $result_1);
        $this->assertObjectHasAttribute('costs', $result_1);

        $this->assertEquals(20000, $result_1->costs);


        // Batch update
        $data = array(
            array(
                'carid' => $id_2,
                'model' => 'FrameworkCar_Update_Done_2'
            ),
            array(
                'carid' => $id_3,
                'model' => 'FrameworkCar_Update_Done_3'
            )
        );

        $status = $this->engine->updateBatch(DB_PREFIX . 'car', $data, 'carid');

        $this->assertTrue($status);


        $all = $this->engine->selectAll("SELECT * FROM " . DB_PREFIX . "car WHERE model LIKE 'FrameworkCar_Update_Done%' ORDER BY model ASC;");

        $this->assertEquals(2, count($all));
        $this->assertEquals('FrameworkCar_Update_Done_2', $all[0]->model);
        $this->assertEquals('FrameworkCar_Update_Done_3', $all[1]->model);

        // Cleanup
        $this->engine->rawQuery("DELETE FROM " .DB_PREFIX ."car WHERE make LIKE 'Nova Cars';");
    }


    /**
     * @covers \Nova\Database\Manager::getEngine
     * @covers \Nova\Database\Engine\SQLite::__construct
     * @covers \Nova\Database\Engine\SQLite::insert
     * @covers \Nova\Database\Engine\SQLite::selectAll
     * @covers \Nova\Database\Engine\SQLite::delete
     */
    public function testDeleting()
    {
        $this->prepareEngine();

        // === Basic test by inserting and deleting several records
        $data_1 = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_Delete_1', 'costs' => 18000);
        $id_1 = $this->engine->insert(DB_PREFIX .'car', $data_1);

        $data_2 = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_Delete_2', 'costs' => 18000);
        $id_2 = $this->engine->insert(DB_PREFIX .'car', $data_2);

        $data_3 = array('make' => 'Nova Cars', 'model' => 'FrameworkCar_Delete_3', 'costs' => 99999);
        $id_3 = $this->engine->insert(DB_PREFIX .'car', $data_3);

        $this->assertGreaterThanOrEqual(2, $id_1);
        $this->assertGreaterThanOrEqual(2, $id_2);
        $this->assertGreaterThanOrEqual(2, $id_3);

        // Delete all 3 with several styles
        $delete_1 = $this->engine->delete(DB_PREFIX .'car', array('carid' => $id_1));
        $delete_2 = $this->engine->delete(DB_PREFIX .'car', array('model' => 'FrameworkCar_Delete_2'));
        $delete_3 = $this->engine->delete(DB_PREFIX .'car', array('costs' => 99999));

        $this->assertNotFalse($delete_1);
        $this->assertNotFalse($delete_2);
        $this->assertNotFalse($delete_3);

        // Check if we still can find the inserted records
        $sql = "SELECT * FROM " .DB_PREFIX ."car WHERE carid = :carid1 OR carid = :carid2 OR carid = :carid3;";
        $all = $this->engine->selectAll($sql, array('carid1' => $id_1,'carid2' => $id_2,'carid3' => $id_3));

        // Should be empty => false.
        $this->assertFalse($all);
    }


    /**
     * @covers \Nova\Database\Engine\Base::rawPrepare
     */
    public function testRawPrepare()
    {
        $this->prepareEngine();

        $statement = $this->engine->rawPrepare("SELECT * FROM " . DB_PREFIX . "car WHERE make = :make", array('make' => 'Tesla'));

        $this->assertInstanceOf('\PDOStatement', $statement);

        $execute = $statement->execute();

        $this->assertTrue($execute);

        $all = $statement->fetchAll(\PDO::FETCH_OBJ);

        $this->assertEquals(1, count($all));
    }


    /**
     * @covers \Nova\Database\Engine\SQLite::listFields
     */
    public function testListFields()
    {
        $this->prepareEngine();

        $fields = $this->engine->listFields(DB_PREFIX . 'car');

        $this->assertEquals(array(
            'carid', 'make', 'model', 'costs'
        ), $fields);
    }
}
