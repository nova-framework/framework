<?php

namespace App\Modules\Demos\Controllers;

use App\Core\Controller;

use Routing\Route;

use App\Models\User;

use App;
use Cache;
use DB;
use Event;
use Hash;
use Input;
use Mailer;
use Redirect;
use Request;
use Session;
use Validator;
use View;


/*
*
* Demo controller
*/
class Demos extends Controller
{
    /**
     * Define Index method
     */
    public function index()
    {
        echo 'hello';
    }

    public function password($password)
    {
        $content = '';

        $content .= '<p><b>' .__d('demos', 'Password:') .'</b> : <code>'. Hash::make($password) .'</code></p>';

        $content .= '<p><b>' .__d('demos', 'Timestamp:') .'</b> : <code>'.time() .'<b></code>';

        return View::make('Default')
            ->shares('title', __d('demos', 'Password Sample'))
            ->with('content', $content);
    }

    public function test()
    {
        //$uri = 'demo/test(/(:any)(/(:any)(/(:any)(/(:all)))))';
        $uri = 'demo/test/(:any)(/(:any)(/(:any)(/(:all))))';
        //$uri = '(:all)';

        //
        $route = new Route('GET', $uri, function()
        {
            echo 'Hello, World!';

        }, false);

        // Match the Route.
        $request = Request::instance();

        if ($route->matches($request)) {
            $route->bind($request);

            $content = '<pre>Route matched!</pre>';
        } else {
            $content = '<pre>Route not matched!</pre>';
        }

        $content .= '<pre>' .htmlspecialchars(var_export($route, true)) .'</pre>';

        return View::make('Default')
            ->shares('title', __d('demos', 'Test'))
            ->with('content', $content);
    }

    public function request($param1 = '', $param2 = '', $param3 = '', $param4 = '')
    {
        $content = '<pre>' .var_export(gethostname(), true).'</pre>';

        //
        $app = App::instance();

        $content .= '<pre>' .var_export($app['env'], true).'</pre>';

        //
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
            ->shares('title', __d('demos', 'Request API'))
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
            ->shares('title', __d('demos', 'Events API'))
            ->with('content', $content);
    }

    public function database()
    {
        $content = '';

        //
        $query = DB::table('users')->where('username', 'admin');

        $sql = $query->toSql();

        $user = $query->first();

        $content .= '<pre>' .var_export($sql, true) .'</pre>';
        $content .= '<pre>' .var_export($user, true) .'</pre>';

        //
        $user = User::find(1);

        $content .= '<pre>' .var_export($user->toArray(), true) .'</pre>';

        //
        $users = User::all();

        $content .= '<pre>' .var_export($users->toArray(), true) .'</pre>';

        //
        $users = User::where('username', '!=', 'admin')->orderBy('username', 'desc')->get();

        $content .= '<pre>' .var_export($users->toArray(), true) .'</pre>';

        return View::make('Default')
            ->shares('title', __d('demos', 'Database API'))
            ->with('content', $content);
    }

    public function mailer()
    {
        $data = array(
            'title'   => __d('demos', 'Welcome to {0}!', SITETITLE),
            'content' => __d('demos', 'This is a test!!!'),
        );

        Mailer::pretend(true);

        Mailer::send('Emails/Welcome', $data, function($message)
        {
            $message->from('admin@novaframework', 'Administrator')
                ->to('john@novaframework', 'John Smith')
                ->subject('Welcome!');
        });

        // Prepare and return the View instance.
        $content = __d('demos', 'Message sent while pretending. Please, look on <code>{0}</code>', 'app/Storage/Logs/messages.log');

        return View::make('Default')
            ->shares('title', __d('demos', 'Mailing API'))
            ->with('content', $content);
    }

    public function session()
    {
        $content = '';

        $content .= '<pre>' .var_export(Session::get('language'), true) .'</pre>';

        $data = Session::all();

        $content .= '<pre>' .var_export($data, true) .'</pre>';

        return View::make('Default')
            ->shares('title', __d('demos', 'Session API'))
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
            ->shares('title', __d('demos', 'Validation API'))
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
            ->shares('title', __d('demos', 'Pagination'))
            ->with('content', $content);
    }

    public function cache()
    {
        $key = "test_page";

        $content = Cache::get($key);

        if (is_null($content)) {
            $content = "Files Cache --> Well done !";

            // Write products to Cache in 10 minutes with same keyword
            Cache::put($key, $content, 10);
        } else {
            $content = "READ FROM CACHE // " .$content;
        }

        return View::make('Default')
            ->shares('title', __d('demos', 'Cache'))
            ->with('content', $content);
    }

    public function catchAll($slug)
    {
        $content = '<pre>' .htmlspecialchars($slug) .'</pre>';

        return View::make('Default')
            ->shares('title', __d('demos', 'The catch-all Route'))
            ->with('content', $content);
    }
}
