<?php
namespace App\Controllers;

use App\Services\Database\Car;
use App\Services\Database\CarLite;
use Nova\Core\View;
use Nova\Core\Controller;
use Nova\Database\Manager;

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

        return '<pre>'.var_export($params, true).'</pre>';
    }

    public function catchAll($str)
    {
        return htmlspecialchars($str, ENT_COMPAT, 'ISO-8859-1', true);
    }
}
