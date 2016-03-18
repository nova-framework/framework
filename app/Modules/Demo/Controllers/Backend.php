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

use App\Modules\Demo\Models\User;
use App\Modules\Demo\Models\Profile;
use App\Modules\Demo\Models\Post;
use App\Modules\Demo\Models\Student;
use App\Modules\Demo\Models\Course;
use App\Modules\Demo\Helpers\TextHighlight as Highlighter;
use App\Modules\Demo\Helpers\ObjectDumper as Dumper;

use Nova\ORM\Model as BaseModel;

use \PDO;

/**
 * Sample Themed Controller with its typical usage.
 */
class Backend extends BaseController
{
    protected $template = 'Backend';

    protected $layout = 'demos';

    private $model;


    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new User();
    }

    protected function beforeAction()
    {
        $this->set('useClassicDb', true);

        // Leave to parent's method the Flight decisions.
        return parent::beforeAction();
    }

    protected function afterAction($result)
    {
        // Do some processing there, even deciding to stop the Flight, if case.

        // Leave to parent's method the Flight decisions.
        return parent::afterAction($result);
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function index()
    {
        //
        $message = '
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Details about Model').'</h3>
            </div>
            <div class="box-body">';

        //
        $text = '
Dumper::dumpObject($this->model);

var_export($this->model->getObjectVariables(), true);
        ';

        $message .= Highlighter::parse($text, true);
        $message .= '<pre>'. Dumper::dumpObject($this->model).'</pre>';
        $message .= '<pre>'. var_export($this->model->getObjectVars(), true).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Finding Records').'</h3>
            </div>
            <div class="box-body">';

        //
        $result = $this->model->find(1);

        $message .= Highlighter::parse('$this->model->find(1);', true);
        $message .= '<pre>'. Dumper::dumpObject($result).'</pre><br>';

        //
        $result = $this->model->orderBy('username', 'DESC')->findAll();

        $message .= Highlighter::parse('$this->model->orderBy(\'username\', \'DESC\')->findAll();', true);
        $message .= '<pre>'. Dumper::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->findBy('username', 'marcus');

        $message .= Highlighter::parse('$this->model->findBy(\'username\', \'marcus\');', true);
        $message .= '<pre>'. Dumper::dumpObject($result).'</pre><br>';

        //
        $result = $this->model->findAll('username', '!=', 'marcus');

        $message .= Highlighter::parse('$this->model->findAll(\'username\', \'!=\', \'marcus\');', true);
        $message .= '<pre>'. Dumper::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->where('username', '!=', 'virgil')->limit(2)->orderBy('email', 'DESC')->findAll();

        $message .= Highlighter::parse('$this->model->where(\'username\', \'!=\', \'virgil\')->limit(2)->orderBy(\'email\', \'DESC\')->findAll();', true);
        $message .= '<pre>'. Dumper::dumpObjectArray($result).'</pre><br>';

        //
        $result = $this->model->findMany(array(1, 3, 4));

        $message .= Highlighter::parse('$this->model->findMany(array(1, 3, 4));', true);
        $message .= '<pre>'. Dumper::dumpObjectArray($result).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Creating Records').'</h3>
            </div>
            <div class="box-body">';

        //
        $user = new User();

        $user->username = 'virgil';
        $user->email = 'virgil@novaframework.dev';

        $user2 = clone $user;

        $result = $user->save();

        $user3 = User::find($user->id);

        $text = "
\$user = new User();

\$user->username = 'virgil';
\$user->email = 'virgil@novaframework.dev';

\$user2 = clone \$user;

\$result = \$user->save();

\$user3 = User::find(\$user->id);

var_dump(\$result);
Dumper::dumpObject(\$user2);
Dumper::dumpObject(\$user);
Dumper::dumpObject(\$user3);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. var_export($result, true).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($user2).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($user).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($user3).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Modifying Records').'</h3>
            </div>
            <div class="box-body">';

        //
        $user->email = 'modified@novaframework.dev';

        $result = $user->save();

        $user2 = User::find($user->id);

        $text = "
\$user->email = 'modified@novaframework.dev';

\$result = \$user->save();

\$user2 = User::find(\$user->id);

var_dump(\$result);
Dumper::dumpObject(\$user);
Dumper::dumpObject(\$user2);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. var_export($result, true).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($user).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($user2).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Deleting Records').'</h3>
            </div>
            <div class="box-body">';

        //
        $result = $user->destroy();

        $text = "
\$result = \$user->destroy();

var_dump(\$result);
Dumper::dumpObject(\$user);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. var_export($result, true).'</pre>';
        $message .= '<pre>'.Dumper::dumpObject($user).'</pre><br>';

        //
        $result = $this->model->findAll();

        $message .= Highlighter::parse('$this->model->findAll();', true);
        $message .= '<pre>'. Dumper::dumpObjectArray($result).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Relations: hasOne').'</h3>
            </div>
            <div class="box-body">';

        //
        $user = $this->model->findBy('username', 'marcus');

        $profile = $user->profile()->get();

        $text = "
\$user = \$this->model->findBy('username', 'marcus');

\$profile = \$user->profile;

Dumper::dumpObject(\$user);
Dumper::dumpObject(\$profile);
Dumper::dumpObject(\$user->profile);
Dumper::dumpObject(\$profile->user);

        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($user).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($profile).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($user->profile).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($profile->user).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Relations: belongsTo').'</h3>
            </div>
            <div class="box-body">';

        //
        $user = $this->model->findBy('username', 'marcus');

        $profile = $user->profile;


        $text = "
\$user = \$this->model->findBy('username', 'marcus');

\$profile = \$user->profile;

\$user2 = \$profile->user;

Dumper::dumpObject(\$user);
Dumper::dumpObject(\$profile);
Dumper::dumpObject(\$profile->user);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($user).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($profile).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($profile->user).'</pre><br>';

        //

        $post = Post::find(1);

        $author = $post->author;

        $text = "
\$post = Post::find(1);

\$author = \$post->author;

Dumper::dumpObject(\$post);
Dumper::dumpObject(\$author);
Dumper::dumpObject(\$author->profile);
Dumper::dumpObjectArray(\$author->posts);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($post).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($author).'</pre>';
        $message .= '<pre>'. Dumper::dumpObject($author->profile).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($author->posts).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Relations: hasMany').'</h3>
            </div>
            <div class="box-body">';

        //
        $user = $this->model->findBy('username', 'marcus');

        $posts = $user->posts;

        $text = "
\$user = \$this->model->findBy('username', 'marcus');

\$posts = \$user->posts;

Dumper::dumpObject(\$user);
Dumper::dumpObjectArray(\$posts);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($user).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($posts).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Relations: belongsToMany').'</h3>
            </div>
            <div class="box-body">';

        //
        $student = Student::find(1);

        $courses = $student->courses;

        $text = "
\$student = Student::find(1);

\$courses = \$student->courses;

Dumper::dumpObject(\$student);
Dumper::dumpObjectArray(\$courses);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($student).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($courses).'</pre><br>';

        //
        $course = Course::find(1);

        $students = $course->students()
            ->where('username', '!=', 'tom')
            ->orderBy('username', 'DESC')
            ->limit(2)
            ->get();

        $text = "
\$course = Course::find(1);

\$students = \$course->students()
    ->where('username', '!=', 'tom')
    ->orderBy('username,' 'DESC')
    ->limit(2)
    ->get();

Dumper::dumpObject(\$course);
Dumper::dumpObjectArray(\$students);
Dumper::dumpObjectArray(\$course->students);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($course).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($students).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($course->students).'</pre><br>';

        //
        $message .= '
            </div>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">'.__d('demo', 'Relations: belongsToMany, operating with the Pivot').'</h3>
            </div>
            <div class="box-body">';

        //
        $course = Course::find(2);

        $relation = $course->students();

        $students = $relation->get();

        $pivot = $course->students()->pivot();

        $sids = $pivot->relatedIds();

        $text = "
\$course = Course::find(2);

\$relation = \$course->students();

\$students = \$relation->get();

\$sids = \$relation->pivot()->relatedIds();

Dumper::dumpObject(\$course);
var_export(\$sids, true);
Dumper::dumpObjectArray(\$students);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($course).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($students).'</pre>';
        $message .= '<pre>'. var_export($sids, true).'</pre><br>';

        //
        $relation->attach(3);

        $sids = $relation->pivot()->relatedIds();

        $students = $relation->get();

        $text = "
\$relation->attach(3);

\$sids = \$relation->pivot()->relatedIds();

\$students = \$relation->get();

var_export(\$sids, true);
Dumper::dumpObjectArray(\$students);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($students).'</pre><br>';

        //
        $relation->dettach(3);

        $sids = $relation->pivot()->relatedIds();

        $students = $relation->get();

        $text = "
\$relation->dettach(3);

\$sids = \$relation->pivot()->relatedIds();

\$students = \$relation->get();

var_export(\$sids, true);
Dumper::dumpObjectArray(\$students);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($students).'</pre><br>';

        //
        $relation->sync(array(1,2,4));

        $sids = $relation->pivot()->relatedIds();

        $students = $relation->get();

        $relation->dettach(4);

        $text = "
\$relation->sync(array(1,2,4));

\$sids = \$relation->pivot()->relatedIds();

\$students = \$relation->get();

\$relation->dettach(4);

var_export(\$sids, true);
Dumper::dumpObjectArray(\$students);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. var_export($sids, true).'</pre>';
        $message .= '<pre>'. Dumper::dumpObjectArray($students).'</pre><br>';

        $message .= '
            </div>
        </div>';

        // Setup the View variables.
        $this->title(__d('demo', 'Backend - Object Relational Model'));

        $this->set('message', $message);
    }
}
