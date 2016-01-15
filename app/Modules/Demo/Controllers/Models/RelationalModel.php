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

use App\Modules\Demo\Models\User;
use App\Modules\Demo\Models\Profile;
use App\Modules\Demo\Models\Post;
use App\Modules\Demo\Models\Student;
use App\Modules\Demo\Models\Course;

use Nova\ORM\Model as BaseModel;

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

        $this->model = new User();
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
        $result = $this->model->findMany(array(1, 3, 4));

        $message .= self::highlightText('$this->model->findMany(array(1, 3, 4));', true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($result).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Creating Records').'</strong></h3><br>';

        //
        $user = new User();

        $user->username = 'Virgil';
        $user->email = 'virgil@novaframework.dev';

        $result = $user->save();

        $text = "
\$user = new User();

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

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: hasOne').'</strong></h3><br>';

        //
        $user = $this->model->findBy('username', 'marcus');

        $profile = $user->profile()->get();

        $text = "
\$user = \$this->model->findBy('username', 'marcus');

\$profile = \$user->profile;

self::dumpObject(\$user);
self::dumpObject(\$profile);
self::dumpObject(\$user->profile);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. self::dumpObject($user).'</pre>';
        $message .= '<pre>'. self::dumpObject($profile).'</pre>';
        $message .= '<pre>'. self::dumpObject($user->profile).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: belongsTo').'</strong></h3><br>';

        //
        $user = $this->model->findBy('username', 'marcus');

        $profile = $user->profile;

        $user2 = $profile->user;

        $text = "
\$user = \$this->model->findBy('username', 'marcus');

\$profile = \$user->profile;

\$user2 = \$profile->user;

self::dumpObject(\$user);
self::dumpObject(\$profile);
self::dumpObject(\$user2);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. self::dumpObject($user).'</pre>';
        $message .= '<pre>'. self::dumpObject($profile).'</pre>';
        $message .= '<pre>'. self::dumpObject($user2).'</pre><br>';

        //

        $post = Post::find(1);

        $author = $post->author;

        $text = "
\$post = Post::find(1);

\$author = \$post->author;

self::dumpObject(\$post);
self::dumpObject(\$author);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. self::dumpObject($post).'</pre>';
        $message .= '<pre>'. self::dumpObject($author).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: hasMany').'</strong></h3><br>';

        //
        $user = $this->model->findBy('username', 'marcus');

        $posts = $user->posts;

        $text = "
\$user = \$this->model->findBy('username', 'marcus');

\$posts = \$user->posts;


self::dumpObject(\$user);
self::dumpObjectArray(\$posts);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. self::dumpObject($user).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($posts).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: belongsToMany').'</strong></h3><br>';

        //
        $student = Student::find(1);

        $courses = $student->courses;

        $text = "
\$student = Student::find(1);

\$courses = \$student->courses;

self::dumpObject(\$student);
self::dumpObjectArray(\$posts);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObject($student).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($courses).'</pre><br>';

        //
        $course = Course::find(1);

        $students = $course->students()
            ->where('username != ?', 'tom')
            ->orderBy('username DESC')
            ->limit(2)
            ->get();

        $text = "
\$course = Course::find(1);

\$students = \$course->students()
    ->where('username != ?', 'tom')
    ->orderBy('username DESC')
    ->limit(2)
    ->get();

self::dumpObject(\$course);
self::dumpObjectArray(\$students);
self::dumpObjectArray(\$course->students);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. self::dumpObject($course).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($students).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($course->students).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: belongsToMany, operating with the Pivot').'</strong></h3><br>';

        //
        $course = Course::find(2);

        $students = $course->students()->get();

        $pivot = $course->students()->pivot();

        $sids = $pivot->get();

        $text = "
\$course = Course::find(2);

\$students = \$course->students()->get();

\$pivot = \$course->students()->pivot();

\$sids = \$pivot->get();

var_export(\$sids, true);
self::dumpObjectArray(\$students);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. $this->model->lastSqlQuery().'</pre>';
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($students).'</pre><br>';

        //
        $pivot->attach(3);

        $sids = $pivot->get();

        $students = $course->students()->get();

        $text = "
\$pivot->attach(3);

\$sids = \$pivot->get();

\$students = \$course->students()->get();

var_export(\$sids, true);
self::dumpObjectArray(\$students);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($students).'</pre><br>';

        //
        $pivot->dettach(3);

        $sids = $pivot->get();

        $students = $course->students()->get();

        $text = "
\$pivot->dettach(3);

\$sids = \$pivot->get();

\$students = \$course->students()->get();

var_export(\$sids, true);
self::dumpObjectArray(\$students);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($students).'</pre><br>';

        //
        $pivot->sync(array(1,2,4));

        $sids = $pivot->get();

        $students = $course->students()->get();

        $pivot->dettach(4);

        $text = "
\$sids = \$pivot->get();

\$students = \$course->students()->get();

\$pivot->dettach(4);

var_export(\$sids, true);
self::dumpObjectArray(\$students);
        ";

        $message .= self::highlightText($text, true);
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. self::dumpObjectArray($students).'</pre><br>';

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
            return 'null'; // NULL.
        }
        else if($object === false) {
            return 'false'; // Boolean false.
        }
        else if(is_string($object)) {
            return $object;
        }
        else if($object instanceof BaseModel) {
            return (string) $object;
        }

        //return var_export($object);
    }

    private static function dumpObjectArray($data)
    {
        if($data === null) {
            return 'null'; // NULL.
        }
        else if($data === false) {
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
