<?php

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicGet()
    {
        $called = false;
        // Add route
        \Nova\Net\Router::getInstance()->addRoute('get', '/test/get/1', function() use(&$called) {
            $called = true;
        });

        // Test running the route
        $_SERVER['REQUEST_URI'] = '/test/get/1';
        $_SERVER['SCRIPT_NAME'] = "index.php";

        $_SERVER['REQUEST_METHOD'] = "GET";
        $scriptName = $_SERVER['SCRIPT_NAME'];

        // Dispatch the router
        \Nova\Net\Router::getInstance()->dispatch();

        // Run detecter
        $this->assertTrue($called, 'Router should call the route (/test/get/1) we defined in the test. And call it with simulating the request.');
    }
}
