<?php

namespace Nova\Tests\Net\Router;

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
     * @covers \Nova\Net\Router::addRoute
     * @covers \Nova\Net\Router::__callStatic
     * @covers \Nova\Net\Router::dispatch
     */
    public function testBasicGet()
    {
        $expected_1 = true;
        $expected_2 = true;
        $current_1 = false;
        $current_2 = false;

        // Add route with the new method
        \Nova\Net\Router::getInstance()->addRoute('get', '/test/get/basic/1', function() use(&$current_1) {
            $current_1 = true;
        });

        // Add route with old method
        \Nova\Net\Router::get('/test/get/basic/2', function() use(&$current_2) {
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
     * @covers \Nova\Net\Router::addRoute
     * @covers \Nova\Net\Router::dispatch
     */
    public function testParameterGet()
    {

    }
}
