<?php
namespace App\Controllers;

use Core\View;
use Core\Controller;
use Helpers\Url;

use Event;

/*
*
* Demo controller
*/
class Demo extends Controller
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        // Add a Listener to the Event 'test'.
        Event::listen('test', 'App\Events\Test@handle');

        return parent::before();
    }

    /**
     * Define Index method
     */
    public function index()
    {
        echo 'hello';
    }

    public function test($param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        $params = array(
            'param1' => $param1,
            'param2' => $param2,
            'param3' => $param3,
            'param4' => $param4
        );

        echo '<h3 style="margin-top: 50px;">Action parameters</h3>';

        echo '<pre>' .var_export($params, true) .'</pre>';

        //
        echo '<h3 style="margin-top: 50px;">Events dispatching</h3>';

        // Prepare the Event payload.
        $payload = array(
            'Hello, this is Event sent from ' .str_replace('::', '@', __METHOD__),
            $params
        );

        // Fire the Event 'test'.
        Event::fire('test', $payload);
    }
}
