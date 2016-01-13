<?php
/**
 * Welcome controller
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date December 17th, 2015
 */

namespace App\Modules\Demo\Controllers\Models;

use Nova\Core\View;
use App\Modules\Demo\Core\BaseController;
use App\Modules\Demo\Models\Users as UserModel;

use \PDO;

/**
 * Sample Themed Controller with its typical usage.
 */
class BaseModel extends BaseController
{
    private $model;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new UserModel();
    }

    protected function beforeFlight()
    {
        $this->set('useClassicDb', true);

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
        //
        $message = '<h3><strong>'.__d('demo', 'CRUD Support').'</strong></h3><br>';

        //
        $result = $this->model->countBy('username != ?', 'admin');

        $message .= '<b>$this->model->countBy(\'username != ?\', \'admin\');</b>';
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        //
        $members = $this->model->limit(2)->findAll();

        $message .= '<b>$this->model->limit(2)->findAll();</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'password' => 'test',
            'email'    => 'virgil@novaframework.dev'
        );

        $message .= '<b>$userInfo</b>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        //
        $retval = $this->model->insert($userInfo);

        $message .= '<b>$this->model->insert($userInfo);</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->model->findAll();

        $message .= '<b>$this->model->findAll();</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $userInfo = array(
            'password' => 'testing',
            'email'    => 'modified@novaframework.dev'
        );

        $message .= '<b>$userInfo</b>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        //
        $retval = $this->model->updateBy('username', 'virgil', $userInfo);

        $message .= '<b>$this->model->updateBy(\'username\', \'virgil\', $userInfo);</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->model->findAll();

        $message .= '<b>$this->model->findAll();</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $retval = $this->model->deleteBy('username', 'virgil');

        $message .= '<b>$this->model->deleteBy(\'username\', \'virgil\');</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $members = $this->model->orderBy('username DESC')->findAll();

        $message .= '<b>$this->model->orderBy(\'username DESC\')->findAll();</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $members = $this->model->orderBy('username DESC')->limit(2)->offset(1)->findAll();

        $message .= '<b>$this->model->orderBy(\'username DESC\')->limit(2)->offset(1)->findAll();</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        $result = $this->model->findBy('username', 'marcus');

        $message .= '<b>$this->model->findBy(\'username\', \'marcus\');</b>';
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        //
        $result = $this->model->find(3);

        $message .= '<b>$this->model->find(3);</b><pre>'.var_export($result, true).'</pre><br>';

        //
        $members = $this->model->orderBy('username DESC')->findMany(array(1, 3, 4));

        $message .= '<b>$this->model->orderBy(\'username DESC\')->findMany(array(1, 3, 4));</b>';
        $message .= '<pre>'.var_export($members, true).'</pre><br>';

        //
        // QueryBuilder

        $message .= '<h3 style="margin-top: 40px;"><strong>'.__d('demo', 'Integrated QueryBuilder').'</strong></h3><br>';

        //
        $query = $this->model->asArray()->buildQuery('select')
            ->where('username', 'admin')
            ->fetch();

        $message .= '<b>$this->model->asArray()->buildQuery(\'select\')->where(\'username\', \'admin\')->fetch();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->where('username = ?', 'admin')
            ->fetch();

        $message .= '<b>$this->model->buildQuery(\'select\')->where(\'username = ?\', \'admin\')->fetch();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->where('username != :username', array(':username' => 'admin'))
            ->fetchAll();

        $message .= '<b>$this->model->buildQuery(\'select\')->where(\'username != :username\', array(\':username\' => \'admin\'))->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->where('id', array(1, 3))
            ->fetchAll();

        $message .= '<b>$this->model->buildQuery(\'select\')->where(\'id\', array(1, 3))->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->asArray()->buildQuery('select')
            ->orderBy('id DESC')
            ->fetchAll();

        $message .= '<b>$this->model->asArray()->buildQuery(\'select\')->orderBy(\'id DESC\')->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->where('username != ?', 'admin')
            ->orderBy('id ASC')
            ->limit(2)
            ->fetchAll();

        $message .= '<b>$this->model->buildQuery(\'select\')->where(\'username != ?\', \'admin\')->orderBy(\'id DESC\')->limit(2)->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'password' => 'test',
            'email'    => 'virgil@novaframework.dev'
        );

        $message .= '<b>$userInfo</b>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        //
        $retval = $this->model->buildQuery('insert')
            ->values($this->model->prepareData($userInfo))
            ->execute();

        $message .= '<b>$this->model->buildQuery(\'insert\')->values($this->model->prepareData($userInfo))->execute();</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->fetchAll();

        $message .= '<b>$this->model->buildQuery(\'select\')->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $userInfo = array(
            'password' => 'testing',
            'email' => 'modified@novaframework.dev'
        );

        $message .= '<b>$userInfo</b>';
        $message .= '<pre>'.var_export($userInfo, true).'</pre><br>';

        //
        $retval = $this->model->buildQuery('update')
            ->where('username = ?', 'virgil')
            ->set($this->model->prepareData($userInfo))
            ->execute();

        $message .= '<b>$this->model->buildQuery(\'update\')->where(\'username = ?\', \'virgil\')->set($this->model->prepareData($userInfo))->execute();</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->fetchAll();

        $message .= '<b>$this->model->buildQuery(\'select\')->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $retval = $this->model->buildQuery('delete')
            ->where('username = ?', 'virgil')
            ->execute();

        $message .= '<b>$this->model->buildQuery(\'delete\')->where(\'username = ?\', \'virgil\')->execute();</b>';
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $query = $this->model->buildQuery('select')
            ->fetchAll();

        $message .= '<b>$this->model->buildQuery(\'select\')->fetchAll();</b>';
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'Classic BaseModel'));

        $this->set('message', $message);
    }
}
