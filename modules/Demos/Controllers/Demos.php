<?php

namespace Demos\Controllers;

use Nova\Routing\Route;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\DB;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Mail;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\Session;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use App\Core\Controller;

use Users\Models\User;


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

    /**
     * Returns the next static character in the Route pattern that will serve as a separator.
     *
     * @param string $pattern The route pattern
     *
     * @return string The next static character that functions as separator (or empty string when none available)
     */
    private static function findNextSeparator($pattern)
    {
        if ('' == $pattern) {
            // return empty string if pattern is empty or false (false which can be returned by substr)
            return '';
        }

        // first remove all placeholders from the pattern so we can find the next real static character
        $pattern = preg_replace('#\(:\w+\)#', '', $pattern);

        return (isset($pattern[0]) && (false !== strpos(static::SEPARATORS, $pattern[0]))) ? $pattern[0] : '';
    }

    public function test()
    {
        $content = '';

        //
        $uri = 'demo/test/{param1?}/{param2?}/{param3?}/{slug?}';

        $route = new Route('GET', $uri, function() {
            //
        });

        $route->where('slug', '(.*)');

        // Match the Route.
        $request = Request::instance();

        if ($route->matches($request)) {
            $content = '<pre>' .e(var_export($route->getUri(), true)) .'</pre>';
        } else {
            $content = '<pre>' .e($uri) .'</pre>';
        }

        $className = 'Social\Commands\FollowUserCommand';

        $content = preg_replace('~Command(?!.*Command)~', 'CommandHandler', $className);

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
            'title'   => __d('demos', 'Welcome to {0}!', SITE_TITLE),
            'content' => __d('demos', 'This is a test!!!'),
        );

        Mail::pretend(true);

        Mail::send('Emails/Welcome', $data, function($message)
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
