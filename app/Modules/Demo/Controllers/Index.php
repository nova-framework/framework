<?php
/**
 * Welcome controller
 *
 * @author Tom Valk - tomvalk@lt-box.info
 * @version 3.0
 * @date December 24th, 2015
 */

namespace App\Modules\Demo\Controllers;

use App\Core\ThemedController;

/**
 * Sample Themed Controller with its typical usage.
 */
class Index extends ThemedController
{
    private $basePath;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function home()
    {
        $this->title('Demo Home');
    }
}
