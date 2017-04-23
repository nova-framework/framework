<?php

namespace App\Modules\Demos\Controllers;

use App\Core\Controller;

use Nova\Routing\Route;

use App\Models\Option;
use App\Models\User;

use App;
use Cache;
use Config;
use DB;
use Event;
use Hash;
use Input;
use Mailer;
use Module;
use Paginator;
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
        $options = Option::all();

        $content = '<pre>' .var_export($options->toArray(), true).'</pre>';

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

    public function validation()
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

    public function pagination()
    {
        // Populate the items.
        $items = array_map(function ($value)
        {
            $data = array(
                'name' => 'Blog post #' .$value,
                'url'  => 'posts/' .$value,
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi bibendum viverra aliquet. Cras sed auctor erat. Curabitur lobortis lacinia risus, et imperdiet dolor vehicula ac. Nullam venenatis lectus non nisl molestie iaculis. Pellentesque eleifend porta arcu et efficitur. Praesent pulvinar non nulla vitae consectetur. Curabitur a odio nec neque euismod luctus. Curabitur euismod felis sed lacus tempor pharetra.',
            );

            return $data;

        }, range(1, 100));

        //
        if (Input::get('mode', 'default') == 'simple') {
            $defaultMode = false;
        } else {
            $defaultMode = true;
        }

        //
        $page = Input::get('offset', 1);

        if (($page > count($items)) || ($page < 1)) {
            $page = 1;
        }

        //
        $perPage = 5;

        if ($defaultMode) {
            // We use the Standard Pagination.
            $offset = ($page * $perPage) - $perPage;

            $slices = array_slice($items, $offset, $perPage);

            $posts = Paginator::make($slices, count($items), $perPage);
        } else {
            // We use the Simple Pagination.
            $offset = ($page - 1) * $perPage;

            $slices = array_slice($items, $offset, $perPage + 1);

            $posts = Paginator::make($slices, $perPage);
        }

        //
        $posts->appends(array(
            'mode' => $defaultMode ? 'default' : 'simple',
        ));

        $content = $posts->links();

        foreach ($posts->getItems() as $post) {
            $content .= '<h4><a href="' .site_url($post['url']) .'"><strong>' .$post['name'] .'</strong></a></h4>';

            $content .= '<p style="text-align: justify">' .$post['body'] .'</p><br>';
        }

        $content .= $posts->links();

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

    public function modules()
    {
        $modules = Module::all();

        $content = "<h3 style='text-align: center'>" .__d('demos', 'The Modules configured on this Application') ."</h3>
<table class='table table-striped table-hover responsive'>
    <tr class='bg-navy disabled'>
        <th style='text-align: center; vertical-align: middle;'>" .__d('demos', 'Name') ."</th>
        <th style='text-align: center; vertical-align: middle;'>" .__d('demos', 'Slug') ."</th>
        <th style='text-align: center; vertical-align: middle;'>" .__d('demos', 'Enabled') ."</th>
        <th style='text-align: center; vertical-align: middle;'>" .__d('demos', 'Order') ."</th>
    </tr>";

        $modules->each(function($properties) use (&$content)
        {
            $name  = array_get($properties,'name');
            $slug  = array_get($properties,'slug');
            $order = array_get($properties,'order');
            $enabled = array_get($properties,'enabled', true) ? __d('demos', 'Yes') : __d('demos', 'No');
            $content .= "
    <tr>
        <td style='text-align: center; vertical-align: middle;' width='20%'>$name</td>
        <td style='text-align: center; vertical-align: middle;' width='20%'>$slug</td>
        <td style='text-align: center; vertical-align: middle;' width='15%'>$enabled</td>
        <td style='text-align: center; vertical-align: middle;' width='15%'>$order</td>
    <tr>";

        });

        $content .= "
</table>
";

        return View::make('Default')
            ->shares('title', __d('demos', 'Modules'))
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
