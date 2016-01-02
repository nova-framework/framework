<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Modules\Demo\Controllers;

use Nova\Core\View;
use App\Modules\Demo\Core\BaseController;

use Nova\DBAL\Manager as Database;


/**
 * Sample Themed Controller with its typical usage.
 */
class Doctrine extends BaseController
{
    private $db;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->db = Database::getConnection();
    }

    protected function beforeFlight()
    {
        // Leave to parent's method the Flight decisions.
        return parent::beforeFlight();
    }

    protected function afterFlight($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterFlight($result);
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function index()
    {
        //$message = var_export($this->db, true);

        //
        $members = $this->db->select("SELECT * FROM " .DB_PREFIX ."members", array(), array(), true);

        $message .= '<h4>$this->db->select("SELECT * FROM " .DB_PREFIX ."members", array(), array(), true);</h4>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';


        // Setup the View variables.
        $this->title(__d('demo', 'Database Abstraction Layer Demo'));

        $this->set('message', $message);
    }

}
