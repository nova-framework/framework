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
use App\Modules\Demo\Helpers\TextHighlight as Highlighter;

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
        $result = $this->model->countBy('username', '!=', 'admin');

        $text = "
\$result = \$this->model->countBy('username', '!=', 'admin');

var_export(\$result, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        //
        $data = $this->model->limit(2)->get();

        $text = "
\$data = \$this->model->limit(2)->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'username' => 'virgil',
            'password' => 'test',
            'email'    => 'virgil@novaframework.dev'
        );

        $retval = $this->model->insert($userInfo);

        $text = "
\$userInfo = array(
    'username' => 'virgil',
    'password' => 'test',
    'email'    => 'virgil@novaframework.dev'
);

\$retval = \$this->model->insert(\$userInfo);

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $data = $this->model->findAll();

        $text = "
\$data = \$this->model->findAll();;

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'password' => 'testing',
            'email'    => 'modified@novaframework.dev'
        );

        $retval = $this->model->updateBy('username', 'virgil', $userInfo);

        $text = "
\$userInfo = array(
    'password' => 'testing',
    'email'    => 'modified@novaframework.dev'
);

\$retval = \$this->model->updateBy('username', 'virgil', $userInfo);

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $data = $this->model->findAll();

        $text = "
\$data = \$this->model->findAll();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $retval = $this->model->deleteBy('username', 'virgil');

        $text = "
\$retval = \$this->model->deleteBy('username', 'virgil');

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $data = $this->model->orderBy('username', 'DESC')->get();

        $text = "
\$data = \$this->model->orderBy('username', 'DESC')->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->model
            ->orderBy('username', 'DESC')
            ->limit(2)
            ->offset(1)
            ->get();

        $text = "
\$data = \$this->model
    ->orderBy('username', 'DESC')
    ->limit(2)
    ->offset(1)
    ->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->model->findBy('username', 'marcus');

        $text = "
\$data = \$this->model->findBy('username', 'marcus');

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($result, true).'</pre><br>';

        //
        $data = $this->model->find(3);

        $text = "
\$data = \$this->model->find(3);

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $data = $this->model
            ->orderBy('username', 'DESC')
            ->whereIn('id', array(1, 3, 4))
            ->get();

        $text = "
\$this->model->
    orderBy('username', 'DESC')
    ->whereIn('id', array(1, 3, 4))
    ->get()

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        // QueryBuilder

        $message .= '<h3 style="margin-top: 40px;"><strong>'.__d('demo', 'Integrated QueryBuilder').'</strong></h3><br>';

        //
        $query = $this->model->buildQuery();

        $data = $query->where('username', 'admin')->asAssoc()->first();

        $text = "
\$query = \$this->model->buildQuery();

\$data = \$query->where('username', 'admin')->asAssoc()->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = $this->model->buildQuery();

        $data = $query->where('username', '=', 'admin')->first();

        $text = "
\$query = \$this->model->buildQuery();

\$data = \$query->where('username', '=', 'admin')->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = $this->model->buildQuery();

        $data = $query->whereIn('id', array(1, 3))->get();

        $text = "
\$query = \$this->model->buildQuery();

\$data = \$query->whereIn('id', array(1, 2, 4))->first();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $query = $this->model->buildQuery();

        $query = $query->orderBy('id', 'DESC')->asAssoc()->get();

        $text = "
\$query = \$this->model->buildQuery();

\$data = \$query->orderBy('id', 'DESC')->asAssoc()->get();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->buildQuery();

        $data = $query
            ->where('username', '!=', 'admin')
            ->orderBy('id', 'ASC')
            ->limit(2)
            ->get();

        $text = "
\$query = \$this->model->buildQuery();

\$data = \$query
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
            'password' => 'test',
            'email'    => 'virgil@novaframework.dev'
        );

        $data = $this->model->prepareData($userInfo);

        $query = $this->model->buildQuery();

        $userId = $query->insert($data);

        $text = "
\$userInfo = array(
    'username' => 'virgil',
    'password' => 'test',
    'email'    => 'virgil@novaframework.dev'
);

\$data = \$this->model->prepareData(\$userInfo);

\$query = \$this->model->buildQuery();

\$userId = \$query->insert(\$data);

var_export(\$userId, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($userId, true).'</pre><br>';

        //
        $data = $this->model->find($userId);

        $text = "
\$data = \$this->model->find(\$userId);

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        //
        $userInfo = array(
            'password' => 'testing',
            'email' => 'modified@novaframework.dev'
        );

        $data = $this->model->prepareData($userInfo);

        $query = $this->model->buildQuery();

        $retval = $query->where('id', $userId)->update($data);

        $text = "
\$userInfo = array(
    'password' => 'testing',
    'email'    => 'modified@novaframework.dev'
);

\$data = \$this->model->prepareData(\$userInfo);

\$query = \$this->model->buildQuery();

\$retval = \$query->where('id', \$userId)->update(\$data);

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $query = $this->model->find($userId);

        $text = "
\$data = \$this->model->find(\$userId);

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($query, true).'</pre><br>';

        //
        $query = $this->model->buildQuery();

        $retval = $query->where('username', '=', 'virgil')->delete();

        $text = "
\$query = \$this->model->buildQuery();

\$retval = \$query->where('username', '=', 'virgil')->delete();

var_export(\$retval, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($retval, true).'</pre><br>';

        //
        $data = $this->model->findAll();

        $text = "
\$data = \$this->model->findAll();

var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'.var_export($data, true).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'Classic BaseModel'));

        $this->set('message', $message);
    }

}
