<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Http\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Session;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use App\Core\BackendController;

use Modules\Users\Models\User;
use Modules\Users\Models\Role;

use Carbon\Carbon;


class Users extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        //
        $this->middleware('admin');
    }

    protected function validator(array $data, $id = null)
    {
        if (! is_null($id)) {
            $ignore = ',' .intval($id);

            $required = 'sometimes|required';
        } else {
            $ignore = '';

            $required = 'required';
        }

        // The Validation rules.
        $rules = array(
            'username'              => 'required|min:4|max:100|alpha_dash|unique:users,username' .$ignore,
            'role'                  => 'required|numeric|exists:roles,id',
            'first_name'            => 'required|min:4|max:100|valid_name',
            'last_name'             => 'required|min:4|max:100|valid_name',
            'location'              => 'min:2|max:100|valid_location',
            'password'              => $required .'|confirmed|strong_password',
            'password_confirmation' => $required .'|same:password',
            'email'                 => 'required|min:5|max:100|email|unique:users,email' .$ignore,
            'image'                 => 'max:1024|mimes:png,jpeg,jpg,gif',
        );

        $messages = array(
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'valid_location'  => __d('users', 'The :attribute field is not a valid location.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'              => __d('users', 'Username'),
            'role'                  => __d('users', 'Role'),
            'first_name'            => __d('users', 'First Name'),
            'last_name'             => __d('users', 'Last Name'),
            'location'              => __d('users', 'Location'),
            'password'              => __d('users', 'Password'),
            'password_confirmation' => __d('users', 'Password confirmation'),
            'email'                 => __d('users', 'E-mail'),
            'image'                 => __d('users', 'Profile Picture'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        Validator::extend('valid_location', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\',\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        Validator::extend('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function index()
    {
        // Get all User records for current page.
        $users = User::where('active', 1)->paginate(25);

        return $this->getView()
            ->shares('title', __d('users', 'Users'))
            ->with('users', $users);
    }

    public function create()
    {
        // Get all available User Roles.
        $roles = Role::all();

        return $this->getView()
            ->shares('title', __d('users', 'Create User'))
            ->with('roles', $roles);
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::only('username', 'role', 'first_name', 'last_name', 'location', 'password', 'password_confirmation', 'email', 'image');

        if (empty($input['location'])) unset($input['location']);

        //
        $validator = $this->validator($input);

        if($validator->passes()) {
            // Encrypt the given Password.
            $password = Hash::make($input['password']);

            // Create a User Model instance.
            $user = new User();

            //
            $user->username   = $input['username'];
            $user->password   = $password;
            $user->role_id    = $input['role'];
            $user->first_name = $input['first_name'];
            $user->last_name  = $input['last_name'];
            $user->email      = $input['email'];
            $user->active     = 1;

            // Setup the optional User location.
            if (isset($input['location'])) {
                $user->location = $input['location'];
            }

            // If a file has been uploaded.
            if (Input::hasFile('image')) {
                $user->image = Input::file('image');
            }

            // Save the User information.
            $user->save();

            // Prepare the flash message.
            $status = __d('users', 'The User <b>{0}</b> was successfully created.', $input['username']);

            return Redirect::to('admin/users')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    public function show($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('users', 'The User with ID: {0} was not found.', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        return $this->getView()
            ->shares('title', __d('users', 'Show User'))
            ->with('user', $user);
    }

    public function edit($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('users', 'The User with ID: {0} was not found.', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Get all available User Roles.
        $roles = Role::all();

        return $this->getView()
            ->shares('title', __d('users', 'Edit User'))
            ->with('roles', $roles)
            ->with('user', $user);
    }

    public function update($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('users', 'The User with ID: {0} was not found.', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Validate the Input data.
        $input = Input::only('username', 'role', 'first_name', 'last_name', 'location', 'password', 'password_confirmation', 'email', 'image');

        if (empty($input['location'])) unset($input['location']);

        if(empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password']);
            unset($input['password_confirmation']);
        }

        $validator = $this->validator($input, $id);

        if($validator->passes()) {
            $origName = $user->username;

            // Update the User Model instance.
            $user->username   = $input['username'];
            $user->role_id    = $input['role'];
            $user->first_name = $input['first_name'];
            $user->last_name  = $input['last_name'];
            $user->email      = $input['email'];

            // Setup the optional User location.
            if (isset($input['location'])) {
                $user->location = $input['location'];
            }

            // If a file has been uploaded.
            if (Input::hasFile('image')) {
                $user->image = Input::file('image');
            }

            if(isset($input['password'])) {
                // Encrypt and add the given Password.
                $user->password = Hash::make($input['password']);
            }

            // Save the User information.
            $user->save();

            // Prepare the flash message.
            $status = __d('users', 'The User <b>{0}</b> was successfully updated.', $origName);

            return Redirect::to('admin/users')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    public function destroy($id)
    {
        // Get the User Model instance.
        try {
            $user = User::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            $status = __d('users', 'The User with ID: {0} was not found.', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Destroy the requested User record.
        $user->delete();

        // Prepare the flash message.
        $status = __d('users', 'The User <b>{0}</b> was successfully deleted.', $user->username);

        return Redirect::to('admin/users')->withStatus($status);
    }

    public function search()
    {
        // Validation rules
        $rules = array(
            'query' => 'required|min:4|valid_query'
        );

        $messages = array(
            'valid_query' => __d('users', 'The :attribute field is not a valid query string.'),
        );

        $attributes = array(
            'query' => __('Search Query'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_query', function($attribute, $value, $parameters)
        {
            return (preg_match('/^[\p{L}\p{N}_\-\s]+$/', $value) === 1);
        });

        // Validate the Input data.
        $input = Input::only('query');

        $validator = Validator::make($input, $rules, $messages, $attributes);

        if($validator->fails()) {
            // Prepare the flash message.
            $status = $validator->errors();

            return Redirect::back()->withStatus($status, 'danger');
        }

        // Search the Records on Database.
        $search = $input['query'];

        $users = User::where('username', 'LIKE', '%' .$search .'%')
            ->orWhere('first_name', 'LIKE', '%' .$search .'%')
            ->orWhere('last_name', 'LIKE', '%' .$search .'%')
            ->orWhere('email', 'LIKE', '%' .$search .'%')
            ->get();

        // Prepare the Query for displaying.
        $search = htmlentities($search);

        return $this->getView()
            ->shares('title', __d('users', 'Searching Users for: {0}', $search))
            ->with('search', $search)
            ->with('users', $users);
    }

    //------------------------------------------------------------------------------
    // An Users list using DataTables
    //------------------------------------------------------------------------------

    public function listUsers()
    {
        $pageLength = 25;

        //
        $query = User::with('role')->where('active', 1);

        $totalCount = $query->count();

        $users = $query->take($pageLength)->get();

        return $this->getView()
            ->shares('title', __d('users', 'Users'))
            ->with('users', $users)
            ->with('totalCount', $totalCount)
            ->with('pageLength', $pageLength);
    }

    public function processor()
    {
        $format = __d('users', '%d %b %Y, %H:%M');

        $columns = array(
            array('data' => 'userid',   'field' => 'id'),
            array('data' => 'username', 'field' => 'username'),
            array('data' => 'name',     'field' => 'first_name'),
            array('data' => 'surname',  'field' => 'last_name'),
            array('data' => 'email',    'field' => 'email'),

            array('data' => 'role', 'field' => 'role_id', 'uses' => function($user)
            {
                return $user->role->name;
            }),

            array('data' => 'date', 'uses' => function($user) use ($format)
            {
                return $user->created_at->formatLocalized($format);
            }),

            array('data' => 'actions', 'uses' => function($user)
            {
                return View::make('Partials/UsersTableActions', array(), 'Users')
                    ->with('user', $user)
                    ->render();
            }),
        );

        $input = Input::only('draw', 'columns', 'start', 'length', 'search', 'order');

        $query = User::with('role')->where('active', 1);

        return $this->dataTable($query, $input, $columns);
    }

}
