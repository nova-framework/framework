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
     * @covers \Nova\Net\Router::invokeController
     * @covers \Nova\Net\Router::dispatch
     */
    public function testBasicGet()
    {
        $expected = true;
        $current = false;

        // Add route
        \Nova\Net\Router::getInstance()->addRoute('get', '/test/get/1', function() use(&$current) {
            $current = true;
        });

        // Test running the route
        $this->spoofRouter('/test/get/1');

        // Dispatch the router
        \Nova\Net\Router::getInstance()->dispatch();

        // Run detecter
        $this->assertEquals($expected, $current, 'Router should call the callback!');
    }

    public function testParameterGet()
    {

    }
}
