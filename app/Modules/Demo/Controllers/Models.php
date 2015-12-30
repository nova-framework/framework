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
use App\Core\BaseModel;
use App\Modules\Demo\Core\BaseController;

use App\Modules\Demo\Models\Members as MembersModel;


/**
 * Sample Themed Controller with its typical usage.
 */
class Models extends BaseController
{
    private $model;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new MembersModel();
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
        $members = $this->model->limit(2, 0)->findAll();

        $message .= '<h4>$this->model->limit(2, 0)->findAll();</h4><pre>'.var_export($members, true).'</pre>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'password' => 'test',
            'email'    => 'virgil@novaframwork.dev'
        );

        $message .= '<h4>$userInfo</h4><pre>'.var_export($userInfo, true).'</pre>';

        $retval = $this->model->insert($userInfo);

        $message .= '<h4>$this->model->insert($userInfo);</h4><pre>'.var_export($retval, true).'</pre>';

        //
        $members2 = $this->model->findAll();

        $message .= '<h4>$this->model->findAll()</h4><pre>'.var_export($members2, true).'</pre>';

        //
        $retval = $this->model->deleteBy('username', 'virgil');

        $message .= '<h4>$this->model->deleteBy(\'username\', \'virgil\');</h4><pre>'.var_export($retval, true).'</pre>';

        //
        $members3 = $this->model->findAll();

        $message .= '<h4>$this->model->findAll()</h4><pre>'.var_export($members3, true).'</pre>';

        //
        $members4 = $this->model->orderBy('username', 'desc')->limit(2, 0)->findAll();

        $message .= '<h4>$this->model->orderBy(\'username\', \'desc\')->limit(2, 0)->findAll();</h4><pre>'.var_export($members4, true).'</pre>';

        //
        $result = $this->model->findBy('username', 'marcus');

        $message .= '<h4>$this->model->findBy(\'username\', \'marcus\');</h4><pre>'.var_export($result, true).'</pre>';

        //
        $result = $this->model->find(1);

        $message .= '<h4>$this->model->find(1)</h4><pre>'.var_export($result, true).'</pre>';

        //
        $members5 = $this->model->orderBy('username', 'desc')->findMany(array(1, 3));

        $message .= '<h4>$this->model->orderBy(\'username\', \'desc\')->findMany(array(1, 3));</h4><pre>'.var_export($members5, true).'</pre>';

        // Setup the View variables.
        $this->title(__d('demo', 'BaseModel Demo'));

        $this->set('message', $message);
    }

}
