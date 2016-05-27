<?php
namespace App\Controllers;

use Core\View;
use Core\Controller;
use Helpers\Password;
use Helpers\Url;

use Event;
use Validator;
use Input;
use Mailer;
use Redirect;
use Request;
use Session;

use App\Models\ORM\User;

use DB;

/*
*
* Demo controller
*/
class Demo extends Controller
{
    /**
     * Call the parent construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define Index method
     */
    public function index()
    {
        echo 'hello';
    }

    public function password($password)
    {
        $content = Password::make($password);

        return View::make('Default')
            ->shares('title', __('Password Sample'))
            ->with('content', $content);
    }

    public function test($param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        $params = array(
            'param1' => $param1,
            'param2' => $param2,
            'param3' => $param3,
            'param4' => $param4
        );

        $content = '<pre>' .var_export($params, true) .'</pre>';

        return View::make('Default')
            ->shares('title', __('Test'))
            ->with('content', $content);
    }

    public function request($param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        $content = '';

        $content .= '<pre>' .var_export(Request::root(), true).'</pre>';

        $content .= '<pre>' .var_export(Request::url(), true).'</pre>';

        $content .= '<pre>' .var_export(Request::path(), true).'</pre>';

        $content .= '<pre>' .var_export(Request::segments(), true).'</pre>';

        $content .= '<pre>' .var_export(Request::segment(1), true).'</pre>';

        $content .= '<pre>' .var_export(Request::isGet(), true).'</pre>';

        $content .= '<pre>' .var_export(Request::isPost(), true).'</pre>';

        $content .= '<pre>' .var_export(Input::all(), true).'</pre>';

        $content .= '<pre>' .var_export(Request::instance(), true).'</pre>';

        return View::make('Default')
            ->shares('title', __('Request API'))
            ->with('content', $content);
    }

    public function events()
    {
        $content = '';
        
        // Prepare the Event payload.
        $payload = array(
            'Hello, this is Event sent from ' .str_replace('::', '@', __METHOD__)
        );

        // Fire the Event 'test' and store the results.
        $results = Event::fire('test', $payload);

        // Print out the non-empty results returned by Event firing.
        $content .= implode('', array_filter($results, 'strlen')) .'<br>';

        // Fire the Event 'test' and echo the result.
        $content .= Event::until('test', $payload);

        return View::make('Default')
            ->shares('title', __('Events API'))
            ->with('content', $content);
    }

    public function database()
    {
        $content = '';

        $user = User::find(1);

        $content .= '<pre>' .var_export($user->toArray(), true) .'</pre>';

        //
        $users = User::all();

        $content .= '<pre>' .var_export($users->toArray(), true) .'</pre>';

        //
        $users = User::where('username', '!=', 'admin')->orderBy('username', 'desc')->get();

        $content .= '<pre>' .var_export($users->toArray(), true) .'</pre>';

        return View::make('Default')
            ->shares('title', __('Database API'))
            ->with('content', $content);
    }

    public function mailer()
    {
        $data = array(
            'title'   => __('Welcome to {0}!', SITETITLE),
            'content' => __('This is a test!!!'),
        );

        Mailer::pretend(true);

        Mailer::send('Emails/Welcome', $data, function($message)
        {
            $message->from('admin@novaframework', 'Administrator')
                ->to('john@novaframework', 'John Smith')
                ->subject('Welcome!');
        });

        // Prepare and return the View instance.
        $content = __('Message sent while pretending. Please, look on <code>{0}</code>', 'app/Storage/Logs/messages.log');

        return View::make('Default')
            ->shares('title', __('Mailing API'))
            ->with('content', $content);
    }

    public function session()
    {
        $content = '';

        $content .= '<pre>' .var_export(Session::get('language'), true) .'</pre>';

        $data = Session::all();

        $content .= '<pre>' .var_export($data, true) .'</pre>';

        return View::make('Default')
            ->shares('title', __('Session API'))
            ->with('content', $content);
    }

    public function validate()
    {
        $data = array(
            'username' => 'michael',
            'password' => 'password',
            'email'    => 'michael@novaframework.dev'
        );

        $rules = array(
            'username' => 'required|min:3|max:50|alpha_dash|unique:users',
            'password' => 'required|between:4,30',
            'email'    => 'required|email|max:100|unique:users',
        );

        $validator = Validator::make($data, $rules);

        //
        $content = '';

        if ($validator->passes()) {
            $content .= '<h3>Data validated with success!</h3>';

            $content .= '<pre>' .var_export($data, true) .'</pre>';
        } else {
            $errors = $validator->errors()->all();

            $content .= '<pre>' .var_export($errors, true) .'</pre>';
        }

        return View::make('Default')
            ->shares('title', __('Validation API'))
            ->with('content', $content);
    }

    public function paginate()
    {
        $paginate = DB::table('posts')->paginate(2);

        $paginate->appends(array(
            'testing'  => 1,
            'example' => 'the_example_string',
        ));

        $content = $paginate->links();

        foreach ($paginate as $post) {
            $content .= '<h3>' .$post->title .'</h3>';

            $content .= $post->content;

            $content .= '<br><br>';
        }

        return View::make('Default')
            ->shares('title', __('Pagination'))
            ->with('content', $content);
    }
}
