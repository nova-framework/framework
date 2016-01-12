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
use App\Modules\Demo\Models\Member as MembersModel;

use \PDO;

/**
 * Sample Themed Controller with its typical usage.
 */
class RelationalModel extends BaseController
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
        $message = '';

        //
        $message .= '<b>var_export($this->model->getObjectVariables(), true);</b>';
        $message .= '<pre>'. var_export($this->model->getObjectVariables(), true).'</pre><br>';

        //
        $result = $this->model->find(1);

        $message .= '<b>$this->model->find(1);</b>';
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObject($result).'</pre><br>';

        //
        $result = $this->model->orderBy('username DESC')->findAll();

        $message .= '<b>$this->model->orderBy(\'username DESC\')->findAll();</b>';
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->findBy('username', 'marcus');

        $message .= '<b>$this->model->findBy(\'username\', \'marcus\');</b>';
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObject($result).'</pre><br>';

        //
        $result = $this->model->findManyBy('username != ?', 'marcus');

        $message .= '<b>$this->model->findManyBy(\'username != ?\', \'marcus\');</b>';
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->where('username != ?', 'virgil')->limit(2)->orderBy('email DESC')->findAll();

        $message .= '<b>$this->model->(\'username != ?\', \'virgil\')->limit(2)->orderBy(\'email DESC\')->findAll();</b>';
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->findMany(array(1, 3));

        $message .= '<b>$this->model->findMany(array(1, 3));</b>';
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'ORM - Object Relational Model'));

        $this->set('message', $message);
    }

    private static function dumpObject($object)
    {
        if($object === null) {
            return 'null'; // Empty string.
        }

        return (string) $object;
    }

    private static function dumpObjectArray($data)
    {
        if($data === false) {
            return 'false'; // Empty string.
        }

        $result = '';

        $cnt = 0;

        foreach($data as $object) {
            if($cnt > 0) {
                $result .= "\n";
            }
            else {
                $cnt++;
            }

            $result .= (string) $object;
        }

        return $result;
    }

}
