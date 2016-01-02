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
        $message = '';

        //
        $data = $this->db->fetchAssoc("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array('username' => 'marcus'));

        $message .= '<h4>$this->db->fetchAssoc("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array(\'username\' => \'marcus\'));</h4>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."members WHERE id = :userid", array('userid' => 1));

        $message .= '<h4>$this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."members WHERE id = :userid", array(\'userid\' => 1));</h4>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchObject("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array('username' => 'michael'));

        $message .= '<h4>$this->db->fetchObject("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array(\'username\' => \'michael\'));</h4>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<h4>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</h4>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'email'    => 'virgil@novaframwork.dev'
        );

        $message .= '<h4>$userInfo</h4>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        //
        $retval = $this->db->insert(DB_PREFIX ."members", $userInfo);

        $message .= '<h4>$this->db->insert(DB_PREFIX ."members", $userInfo);</h4>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<h4>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</h4>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $retval = $this->db->delete(DB_PREFIX ."members", array('username' => 'virgil'));

        $message .= '<h4>$this->db->delete(DB_PREFIX ."members", array(\'username\' => \'virgil\'));</h4>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<h4>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</h4>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'Database Abstraction Layer Demo'));

        $this->set('message', $message);
    }

}
