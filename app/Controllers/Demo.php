<?php
namespace App\Controllers;

use App\Services\Database\Car;
use Smvc\Core\View;
use Smvc\Core\Controller;
use Smvc\Database\EngineFactory;

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

        echo '<pre>'.var_export($params, true).'</pre>';
    }

    public function catchAll($str)
    {
        echo htmlspecialchars($str, ENT_COMPAT, 'ISO-8859-1', true);
    }

    public function database()
    {
        echo "<pre>Plain:<br>";

        // Use it without the Services:
        $engine = EngineFactory::getEngine();
        $result_plain = $engine->select('SELECT * FROM '.PREFIX.'car');
        var_dump($result_plain);

        // Using the select and prefix the SELECT in the sql is optional for the MySQL Engine!
        $result_plain = $engine->select('* FROM '.PREFIX.'car');
        var_dump($result_plain);

        echo "<br><br>Service:<br>";

        // Use with the Car service:
        $service = new Car();
        $result_service = $service->getAll();
        var_dump($result_service);

        echo "</pre>";
    }
}
