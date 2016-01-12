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
use Nova\Database\Manager;
use Nova\Database\Connection;

use App\Modules\Demo\Core\BaseController;


/**
 * Sample Themed Controller with its typical usage.
 */
class Database extends BaseController
{
    private $db;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->db = Manager::getConnection();
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
        $data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."members WHERE username != :username", array('username' => 'marcus'), array(\PDO::PARAM_STR), 'object');

        $message .= '<b>$this->db->selectAll("SELECT * FROM " .DB_PREFIX ."members WHERE username != :username", array(\'username\' => \'marcus\'), array(\PDO::PARAM_STR), \'object\');</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."members WHERE username != :username", array('username' => 'marcus'));

        $message .= '<b>$this->db->selectAll("SELECT * FROM " .DB_PREFIX ."members WHERE username != :username", array(\'username\' => \'marcus\'));</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->select("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array('username' => 'michael'), array(), 'object');

        $message .= '<b>$this->db->select("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array(\'username\' => \'michael\'), array(), \'object\');</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."members WHERE username LIKE :search LIMIT 0, 2", array('search' => '%micha%'));

        $message .= '<b>$this->db->selectAll("SELECT * FROM " .DB_PREFIX ."members WHERE username LIKE :search LIMIT 0, 2", array(\'search\' => \'%micha%\'));</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->select("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array('username' => 'michael'));

        $message .= '<b>$this->db->select("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array(\'username\' => \'michael\'));</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array('username' => 'marcus'));

        $message .= '<b>$this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array(\'username\' => \'marcus\'));</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."members WHERE id = :userid", array('userid' => 1));

        $message .= '<b>$this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."members WHERE id = :userid", array(\'userid\' => 1));</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchObject("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array('username' => 'michael'));

        $message .= '<b>$this->db->fetchObject("SELECT * FROM " .DB_PREFIX ."members WHERE username = :username", array(\'username\' => \'michael\'));</b>';
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<b>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'email'    => 'virgil@novaframework.dev'
        );

        $message .= '<b>$userInfo</b>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        //
        $retval = $this->db->insert(DB_PREFIX ."members", $userInfo);

        $message .= '<b>$this->db->insert(DB_PREFIX ."members", $userInfo);</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<b>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $userInfo = array(
            'email' => 'modified@novaframework.dev'
        );

        $message .= '<b>$userInfo</b>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        $retval = $this->db->update(DB_PREFIX ."members", $userInfo, array('username' => 'virgil'));

        $message .= '<b>$this->db->update(DB_PREFIX ."members", $userInfo, array(\'username\' => \'virgil\'));</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<b>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $retval = $this->db->delete(DB_PREFIX ."members", array('username' => 'virgil'));

        $message .= '<b>$this->db->delete(DB_PREFIX ."members", array(\'username\' => \'virgil\'));</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");

        $message .= '<b>$this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."members");</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        $message .= '<h3 style="margin-top: 40px;"><strong>'.__d('demo', 'Schema support').'</strong></h3><br>';

        //
        $result = $this->db->getTableFields(DB_PREFIX ."members");

        $message .= '<b>$this->db->getTableFields(DB_PREFIX ."members");</b>';
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        //
        $result = $this->db->listColumns(DB_PREFIX ."members");

        $message .= '<b>$this->db->listColumns(DB_PREFIX ."members")</b>';
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'Database Abstraction Layer Demo'));

        $this->set('message', $message);
    }
}
