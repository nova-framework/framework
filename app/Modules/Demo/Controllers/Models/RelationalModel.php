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
use App\Modules\Demo\Models\Member;

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

        $this->model = new Member();
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
        $message = '<h3><strong>'.__d('demo', 'Details about Model').'</strong></h3><br>';

        //
        $message .= self::highlightText('var_export($this->model->getObjectVariables(), true);', true);
        $message .= '<pre>'. var_export($this->model->getObjectVariables(), true).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Finding Records').'</strong></h3><br>';

        //
        $result = $this->model->find(1);

        $message .= self::highlightText('$this->model->find(1);', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObject($result).'</pre><br>';

        //
        $result = $this->model->orderBy('username DESC')->findAll();

        $message .= self::highlightText('$this->model->orderBy(\'username DESC\')->findAll();', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->findBy('username', 'marcus');

        $message .= self::highlightText('$this->model->findBy(\'username\', \'marcus\');', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObject($result).'</pre><br>';

        //
        $result = $this->model->findManyBy('username != ?', 'marcus');

        $message .= self::highlightText('$this->model->findManyBy(\'username != ?\', \'marcus\');', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->where('username != ?', 'virgil')->limit(2)->orderBy('email DESC')->findAll();

        $message .= self::highlightText('$this->model->where(\'username != ?\', \'virgil\')->limit(2)->orderBy(\'email DESC\')->findAll();', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->findMany(array(1, 3));

        $message .= self::highlightText('$this->model->findMany(array(1, 3));', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Creating Records').'</strong></h3><br>';

        //
        $user = new Member();

        $user->username = 'Virgil';
        $user->email = 'virgil@novaframework.dev';

        $result = $user->save();

        $text = "
\$user = new Member();

\$user->username = 'Virgil';
\$user->email = 'virgil@novaframework.dev';

\$result = \$user->save();

var_dump(\$result);
self::dumpObjectArray(\$user);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. var_export($result, true).'</pre><br>';
        $message .= '<pre>'. self::dumpObject($user).'</pre><br>';

        //
        $result = $this->model->findAll();

        $message .= self::highlightText('$this->model->findAll();', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Modifying Records').'</strong></h3><br>';

        //
        $user->email = 'modified@novaframework.dev';

        $result = $user->save();

        $text = "
\$user->email = 'modified@novaframework.dev';

\$result = \$user->save();

var_dump(\$result);
self::dumpObjectArray(\$user);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. var_export($result, true).'</pre><br>';
        $message .= '<pre>'. self::dumpObject($user).'</pre><br>';

        //
        $result = $this->model->findAll();

        $message .= self::highlightText('$this->model->findAll();', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Deleting Records').'</strong></h3><br>';

        //
        $result = $user->delete();

        $text = "
\$result = \$user->delete();

var_dump(\$result);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. var_export($result, true).'</pre><br>';

        //
        $result = $this->model->findAll();

        $message .= self::highlightText('$this->model->findAll();', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        // Setup the View variables.
        $this->title(__d('demo', 'ORM - Object Relational Model'));

        $this->set('message', $message);
    }

    private static function highlightText($text)
    {
        $text = trim($text);
        $text = highlight_string("<?php " . $text, true);  // highlight_string requires opening PHP tag or otherwise it will not colorize the text
        $text = trim($text);
        $text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);  // remove prefix
        $text = preg_replace("|\\</code\\>\$|", "", $text, 1);  // remove suffix 1
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|\\</span\\>\$|", "", $text, 1);  // remove suffix 2
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "

        // Finall processing.
        $text = '<div style="font-weight: bold; margin-bottom: 10px;">'.$text.'</div>';

        return $text;
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
