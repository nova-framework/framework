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
use App\Modules\Demo\Helpers\TextHighlight as Highlighter;


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
        $message = '<h3><strong>'.__d('demo', 'CRUD Support').'</strong></h3><br>';

        //
        $data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."users WHERE username != :username", array('username' => 'marcus'), array(\PDO::PARAM_STR), 'object');

        $text = '
$data = $this->db->selectAll(\"SELECT * FROM \" .DB_PREFIX .\"users WHERE username != :username\", array(\'username\' => \'marcus\'), array(\PDO::PARAM_STR), \'object\');

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."users WHERE username != :username", array('username' => 'marcus'));

        $text = '
$data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."users WHERE username != :username", array(\'username\' => \'marcus\'));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->select("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array('username' => 'michael'), array(), 'object');

       $text = '
$data = $this->db->select("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array(\'username\' => \'michael\'), array(), \'object\');

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."users WHERE username LIKE :search LIMIT 0, 2", array('search' => '%micha%'));

        $text = '
$data = $this->db->selectAll("SELECT * FROM " .DB_PREFIX ."users WHERE username LIKE :search LIMIT 0, 2", array(\'search\' => \'%micha%\'));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->select("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array('username' => 'michael'));

        $text = '
$data = $this->db->select("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array(\'username\' => \'michael\'));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array('username' => 'marcus'));

        $text = '
$data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array(\'username\' => \'marcus\'));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE id = :userid", array('userid' => 1));

        $text = '
$data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE id = :userid", array(\'userid\' => 1));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchObject("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array('username' => 'michael'));

        $text = '
$data = $this->db->fetchObject("SELECT * FROM " .DB_PREFIX ."users WHERE username = :username", array(\'username\' => \'michael\'));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."users");

        $text = '
$data = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."users");

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'email'    => 'virgil@novaframework.dev'
        );

        $userId = $this->db->insert(DB_PREFIX ."users", $userInfo);

        $text = '
$userInfo = array(
    \'username\' => \'virgil\',
    \'email\'    => \'virgil@novaframework.dev\'
);

$userId = $this->db->insert(DB_PREFIX ."users", $userInfo);

var_export($userId, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($userId, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE id = :userId", array('userId' => $userId));

        $text = '
$data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE id = :userId", array(\'userId\' => $userId));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'email' => 'modified@novaframework.dev'
        );

        $retval = $this->db->update(DB_PREFIX ."users", $userInfo, array('id' => $userId));

        $text = '
$userInfo = array(
    \'email\' => \'modified@novaframework.dev\'
);

$retval = $this->db->update(DB_PREFIX ."users", $userInfo, array(\'id\' => $userId));

var_export($retval, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE id = :userId", array('userId' => $userId));

        $text = '
$data = $this->db->fetchArray("SELECT * FROM " .DB_PREFIX ."users WHERE id = :userId", array(\'userId\' => $userId));

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $retval = $this->db->delete(DB_PREFIX ."users", array('username' => 'virgil'));

        $text = '
$retval = $this->db->delete(DB_PREFIX ."users", array(\'username\' => \'virgil\'));

var_export($retval, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $data = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."users");

        $text = '
$data = $this->db->fetchAll("SELECT * FROM " .DB_PREFIX ."users");

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        $message .= '<h3 style="margin-top: 40px;"><strong>'.__d('demo', 'Schema support').'</strong></h3><br>';

        //
        $data = $this->db->getTableFields(DB_PREFIX ."users");

        $text = '
$data = $this->db->getTableFields(DB_PREFIX ."users");

var_export($data, true);
        ';

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        // QueryBuilder

        $message .= '<h3 style="margin-top: 40px;"><strong>'.__d('demo', 'Integrated QueryBuilder').'</strong></h3><br>';

        //
        $query = $this->db->getQueryBuilder();

        $data = $query->table('users')->where('username', 'admin')->asAssoc()->first();

        $text = "
\$query = \$this->db->getQueryBuilder();

\$data = \$query->table('users')->where('username', 'admin')->asAssoc()->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = $this->db->getQueryBuilder();

        $data = $query->table('users')->where('username', '=', 'admin')->first();

        $text = "
\$query = \$this->db->getQueryBuilder();

\$data = \$query->table('users')->where('username', '=', 'admin')->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = $this->db->getQueryBuilder();

        $data = $query->table('users')->whereIn('id', array(1, 3))->get();

        $text = "
\$query = \$this->db->getQueryBuilder();

\$data = \$query->table('users')->whereIn('id', array(1, 2, 4))->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = $this->db->getQueryBuilder();

        $query = $query->table('users')->orderBy('id', 'DESC')->asAssoc()->get();

        $text = "
\$query = \$this->db->getQueryBuilder();

\$data = \$query->table('users')->orderBy('id', 'DESC')->asAssoc()->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->db->getQueryBuilder();

        $data = $query
            ->table('users')
            ->where('username', '!=', 'admin')
            ->orderBy('id', 'ASC')
            ->limit(2)
            ->get();

        $text = "
\$query = \$this->db->getQueryBuilder();

\$data = \$query
    ->table('users')
    ->where('username', '!=', 'admin')
    ->orderBy('id', 'DESC')
    ->limit(2)
    ->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'email'    => 'virgil@novaframework.dev'
        );

        $query = $this->db->getQueryBuilder();

        $userId = $query->table('users')->insert($userInfo);

        $text = "
\$userInfo = array(
    'username' => 'virgil',
    'email'    => 'virgil@novaframework.dev'
);

\$query = \$this->db->getQueryBuilder();

\$userId = \$query->table('users')->insert(\$userInfo);

var_export(\$userId, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($userId, true).'</pre><br>';

        //
        $data = $query->table('users')->find($userId);

        $text = "
\$data = \$query->table('users')->find(\$userId);

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'email' => 'modified@novaframework.dev'
        );

        $query = $this->db->getQueryBuilder();

        $retval = $query->table('users')->where('id', $userId)->update($userInfo);

        $text = "
\$userInfo = array(
    'email'    => 'modified@novaframework.dev'
);

\$query = \$this->db->getQueryBuilder();

\$retval = \$query->table('users')->where('id', \$userId)->update(\$userInfo);

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $query = $query->table('users')->where('id', $userId)->first();

        $text = "
\$data = \$query->table('users')->where('id', \$userId)->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->db->getQueryBuilder();

        $retval = $query->table('users')->where('id', '=', $userId)->delete();

        $text = "
\$query = \$this->db->getQueryBuilder();

\$retval = \$query->table('users')->where('username', '=', \$userId)->delete();

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $query = $this->db->getQueryBuilder();

        $data = $query->table('users')->get();

        $text = "
\$data = \$query->table('users')->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'Database Abstraction Layer Demo'));

        $this->set('message', $message);
    }
}
