<?php
/**
 * Router Methods Test
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 25th, 2015
 */

namespace Nova\Tests\Net;

/**
 * Class RouterMethodTest
 * @package Nova\Tests\Net\Router
 * @coversDefaultClass \Nova\Net\Router
 */
class RouterMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Spoof the $_SERVER variables for router.
     *
     * @param string $url part of the url (request)
     * @param string $method method, GET, POST, etc
     * @param string $script script, mostly index.php
     */
    private function spoofRouter($url, $method = 'GET', $script = 'index.php')
    {
        $_SERVER['REQUEST_URI'] = $url;
        $_SERVER['SCRIPT_NAME'] = $script;
        $_SERVER['REQUEST_METHOD'] = $method;
    }

    /**
     * @covers \Nova\Net\Router
     * @covers \Nova\Net\Router::__callStatic
     * @covers \Nova\Net\Route
     * @covers \Nova\Net\Url::detectUri
     */
    public function testBasicGet()
    {
        $expected_1 = true;
        $expected_2 = true;
        $current_1 = false;
        $current_2 = false;

        // Add route with the new method
        \Nova\Net\Router::getInstance()->addRoute('get', 'test/get/basic/1', function () use (&$current_1) {
            $current_1 = true;
        });

        // Add route with old method
        \Nova\Net\Router::get('/test/get/basic/2', function () use (&$current_2) {
            $current_2 = true;
        });

        // Test running the route, first one
        $this->spoofRouter('/test/get/basic/1');
        \Nova\Net\Router::getInstance()->dispatch();

        // Second route
        $this->spoofRouter('/test/get/basic/2');
        \Nova\Net\Router::getInstance()->dispatch();

        // Run detecter
        $this->assertEquals($expected_1, $current_1, 'Router should call the callback!');
        $this->assertEquals($expected_2, $current_2, 'Router should call the callback!');
    }

    /**
     * @covers \Nova\Net\Router
     * @covers \Nova\Net\Router::__callStatic
     * @covers \Nova\Net\Route
     * @covers \Nova\Net\Url::detectUri
     * @covers \Nova\Net\Request::query
     */
    public function testBasicQueryGet()
    {
        $expected_query = array('test' => 1);
        $expected_query_test = 1;

        $current_query = null;
        $current_query_test = null;

        // Add route with the new method
        \Nova\Net\Router::getInstance()->addRoute('get', 'test/get/query/1', function () use (&$current_query, &$current_query_test) {
            $current_query = \Nova\Net\Request::query();
            $current_query_test = \Nova\Net\Request::query('test');
        });

        // Test running the route
        $this->spoofRouter('/test/get/query/1?test=1');
        $_GET['test'] = '1';

        \Nova\Net\Router::getInstance()->dispatch();

        // Run detecter
        $this->assertEquals($expected_query, $current_query);
        $this->assertEquals($expected_query_test, $current_query_test);
    }

    /**
     * @covers \Nova\Net\Router
     * @covers \Nova\Net\Route
     * @covers \Nova\Net\Url::detectUri
     */
    public function testParameterGet()
    {
        $expected_1 = 'Anyparam123';
        $current_1 = '';

        $expected_2 = 1234;
        $current_2 = 0;

        // Add testing routes
        \Nova\Net\Router::getInstance()->addRoute('GET', 'test/get/param/any/(:any)', function ($param) use (&$current_1) {
            $current_1 = $param;
        });

        // Add testing routes
        \Nova\Net\Router::getInstance()->addRoute('GET', 'test/get/param/num/(:num)', function ($param) use (&$current_2) {
            $current_2 = $param;
        });


        $this->spoofRouter('/test/get/param/any/Anyparam123');
        \Nova\Net\Router::getInstance()->dispatch();

        $this->spoofRouter('/test/get/param/num/1234');
        \Nova\Net\Router::getInstance()->dispatch();


        $this->assertEquals($expected_1, $current_1);
        $this->assertEquals($expected_2, $current_2);
    }


    /**
     * @covers \Nova\Net\Router
     * @covers \Nova\Net\Route
     * @covers \Nova\Net\Url::detectUri
     * @covers \Nova\Net\Request::getMethod
     * @covers \Nova\Net\Request::isPost
     */
    public function testBasicPost()
    {
        $expected_method = 'POST';
        $expected_url = 'test/post/1';
        $expected_method_match = true;

        $current_method = '';
        $current_url = '';
        $current_method_match = false;

        // Add testing routes
        \Nova\Net\Router::getInstance()->addRoute('POST', 'test/post/1', function () use (&$current_method, &$current_method_match, &$current_url) {
            $current_url = \Nova\Net\Url::detectUri();
            $current_method = \Nova\Net\Request::getMethod();
            $current_method_match = \Nova\Net\Request::isPost();
        });

        // Spoof and execute
        $this->spoofRouter('/test/post/1', 'POST');
        \Nova\Net\Router::getInstance()->dispatch();

        // Assert
        $this->assertEquals($expected_method, $current_method);
        $this->assertEquals($expected_url, $current_url);
        $this->assertEquals($expected_method_match, $current_method_match);
    }


    /**
     * @covers \Nova\Net\Router
     * @covers \Nova\Net\Route
     * @covers \Nova\Net\Url::detectUri
     * @covers \Nova\Net\Request::getMethod
     * @covers \Nova\Net\Request::isPut
     */
    public function testBasicPut()
    {
        $expected_method = 'PUT';
        $expected_url = 'test/put/1';
        $expected_method_match = true;

        $current_method = '';
        $current_url = '';
        $current_method_match = false;

        // Add testing routes
        \Nova\Net\Router::getInstance()->addRoute('PUT', 'test/put/1', function () use (&$current_method, &$current_method_match, &$current_url) {
            $current_url = \Nova\Net\Url::detectUri();
            $current_method = \Nova\Net\Request::getMethod();
            $current_method_match = \Nova\Net\Request::isPut();
        });

        // Spoof and execute
        $this->spoofRouter('/test/put/1', 'PUT');
        \Nova\Net\Router::getInstance()->dispatch();

        // Assert
        $this->assertEquals($expected_method, $current_method);
        $this->assertEquals($expected_url, $current_url);
        $this->assertEquals($expected_method_match, $current_method_match);
    }


    /**
     * @covers \Nova\Net\Router
     * @covers \Nova\Net\Route
     * @covers \Nova\Net\Url::detectUri
     * @covers \Nova\Net\Request::getMethod
     * @covers \Nova\Net\Request::isDelete
     */
    public function testBasicDelete()
    {
        $expected_method = 'DELETE';
        $expected_url = 'test/delete/1';
        $expected_method_match = true;

        $current_method = '';
        $current_url = '';
        $current_method_match = false;

        // Add testing routes
        \Nova\Net\Router::getInstance()->addRoute('DELETE', 'test/delete/1', function () use (&$current_method, &$current_method_match, &$current_url) {
            $current_url = \Nova\Net\Url::detectUri();
            $current_method = \Nova\Net\Request::getMethod();
            $current_method_match = \Nova\Net\Request::isDelete();
        });

        // Spoof and execute
        $this->spoofRouter('/test/delete/1', 'DELETE');
        \Nova\Net\Router::getInstance()->dispatch();

        // Assert
        $this->assertEquals($expected_method, $current_method);
        $this->assertEquals($expected_url, $current_url);
        $this->assertEquals($expected_method_match, $current_method_match);
    }
}
