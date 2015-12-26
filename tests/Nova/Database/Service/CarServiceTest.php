<?php
/**
 * Car Service tests
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 26th, 2015
 */

namespace Nova\Tests\Database\Service;
use App\Modules\Demo\Services\Database\Car;

/**
 * Class CarServiceTest
 * @package Nova\Tests\Database\Service
 * @coversDefaultClass \Nova\Database\Service
 * @covers \Nova\Database\Manager::getService
 */
class CarServiceTest extends \PHPUnit_Framework_TestCase
{

    /** @var Car */
    private $carservice;

    private function prepareService($linkName = 'default')
    {
        $this->carservice = \Nova\Database\Manager::getService('Car', 'Demo', $linkName);
        $this->assertInstanceOf('\Nova\Database\Service', $this->carservice);
        $this->assertInstanceOf('\App\Modules\Demo\Services\Database\Car', $this->carservice);
    }

    /**
     * @covers \Nova\Database\Manager::getService
     * @covers \Nova\Database\Service
     * @covers \App\Modules\Demo\Services\Database\Car
     */
    public function testPrepareService()
    {
        // Test MySQL Link
        $this->prepareService();

        // Test SQLite Link
        $this->prepareService('sqlite');
    }
}