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
use App\Modules\Demo\Helpers\TextHighlight as Highlighter;
use App\Modules\Demo\Helpers\ObjectDumper as Dumper;

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

    protected function before()
    {
        $this->set('useClassicDb', true);

        // Leave to parent's method the Flight decisions.
        return parent::before();
    }

    /**
     * CakePHP style - Define Welcome page message and set the Controller's variables.
     */
    public function index()
    {
        //
        $message = '<h3><strong>'.__d('demo', 'Details about Model').'</strong></h3><br>';

        //
        $text = '
Dumper::dumpObject($this->model);

var_export($this->model->getObjectVariables(), true);
        ';

        $message .= Highlighter::parse($text, true);
        $message .= '<pre>'. Dumper::dumpObject($this->model).'</pre>';
        $message .= '<pre>'. var_export($this->model->getObjectVars(), true).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Finding Records').'</strong></h3><br>';

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
        $message .= '<h3><strong>'.__d('demo', 'Creating Records').'</strong></h3><br>';

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
        $message .= '<h3><strong>'.__d('demo', 'Modifying Records').'</strong></h3><br>';

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
        $message .= '<h3><strong>'.__d('demo', 'Deleting Records').'</strong></h3><br>';

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
        $message .= '<h3><strong>'.__d('demo', 'Relations: hasOne').'</strong></h3><br>';

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
        $message .= '<h3><strong>'.__d('demo', 'Relations: belongsTo').'</strong></h3><br>';

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
        $message .= '<h3><strong>'.__d('demo', 'Relations: hasMany').'</strong></h3><br>';

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
        $user = User::with('profile', 'posts')->find(2);

        $data = $user->toArray();

        $text = "
\$user = User::with('profile', 'posts')->find(2);

\$data = \$user->toArray();

Dumper::dumpObject(\$user);
var_export(\$data);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($user).'</pre>';
        $message .= '<pre>'. var_export($data, true).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: belongsToMany').'</strong></h3><br>';

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
        $course = Course::with('students')->find(2);

        $data = $course->toArray();

        $text = "
\$course = Course::with('students')->find(2);

\$data = \$course->toArray();

Dumper::dumpObject(\$course);
var_export(\$data, true);
        ";

        $message .= Highlighter::parse($text);
        $message .= '<pre>'. Dumper::dumpObject($course).'</pre>';
        $message .= '<pre>'. var_export($data, true).'</pre><br>';

        //
        $message .= '<h3><strong>'.__d('demo', 'Relations: belongsToMany, operating with the Pivot').'</strong></h3><br>';

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

        // Setup the View variables.
        $this->title(__d('demo', 'ORM - Object Relational Model'));

        $this->set('message', $message);
    }
}
