<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers\Admin;

use Core\View;
use Helpers\Url;
use Helpers\ReCaptcha;

use App\Models\User;
use App\Modules\Users\Core\BaseController;
use App\Modules\Users\Helpers\RoleVerifier as Authorize;

use Carbon\Carbon;

use Auth;
use Hash;
use Input;
use Redirect;
use Session;
use Validator;


class Users extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // Prepare the Users Model instance - while using the Database Auth Driver.
        //$this->model = new \App\Modules\Users\Models\Users();
    }

    protected function before()
    {
        // Check the User Authorization - while using the Extended Auth Driver.
        if (! Auth::user()->hasRole('administrator')) {
            $status = __('You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }

        // Check the User Authorization - while using the Database Auth Driver.
        /*
        if (! Authorize::userHasRole('administrator')) {
            $status = __('You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }
        */

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
    }

    protected function validate(array $data, $id = 0)
    {
        // Get the Users table - while using the Extended Auth Driver.
        $table = User::getTableName();

        // Get the Users table - while using the Database Auth Driver.
        //$table = $this->model->getTable();

        //
        $unique = "|unique:$table,username";

        if ($id > 0) {
            $unique .= ',' .intval($id);

            $required = 'sometimes|required';
        } else {
            $required = 'required';
        }

        // The Validation rules.
        $rules = array(
            'username'              => 'required|min:4|max:100|alpha_dash' .$unique,
            'realname'              => 'required|min:5|max:100|valid_name',
            'password'              => $required .'|confirmed|strong_password',
            'password_confirmation' => $required .'|same:password',
            'email'                 => 'required|min:5|max:100|email',
        );

        $messages = array(
            'strong_password' => __('The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'username'              => __('Username'),
            'realname'              => __('Name and Surname'),
            'password'              => __('Password'),
            'password_confirmation' => __('Password confirmation'),
            'email'                 => __('E-mail'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_name', function($attribute, $value, $parameters)
        {
            return (preg_match('/^[\p{L}\p{N}_\-\s]+$/', $value) === 1);
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
        // Get all User records for current page - used with the Extended Auth Driver.
        $users = User::paginate(25);

        // Get all User records for current page - used with the Database Auth Driver.
        //$users = $this->model->paginate(25);

        return $this->getView()
            ->shares('title', __('Users'))
            ->with('users', $users);
    }

    public function create()
    {
        return $this->getView()
            ->shares('title', __('Create User'));
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::only('username', 'realname', 'password', 'password_confirmation', 'email');

        $validator = $this->validate($input);

        if($validator->passes()) {
            unset($input['password_confirmation']);

            // Encrypt the Password.
            $input['password'] = Hash::make($input['password']);

            // Create a User Model instance - used with the Extended Auth Driver.
            User::create($input);

            // Create a User Model instance - used with the Database Auth Driver.
            // $input['created_at'] = $input['created_at'] = new Carbon();
            //
            //$this->model->insert($input);

            // Prepare the flash message.
            $status = __('The User <b>{0}</b> was successfully created.', $input['username']);

            return Redirect::to('admin/users')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    public function show($id)
    {
        // Get the User Model instance - used with the Extended Auth Driver.
        $user = User::find($id);

        // Get the User Model instance - used with the Database Auth Driver.
        //$user = $this->model->find($id);

        if($user === null) {
            // There is no User with this ID.
            $status = __('User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        return $this->getView()
            ->shares('title', __('Show User'))
            ->with('user', $user);
    }

    public function edit($id)
    {
        // Get the User Model instance - used with the Extended Auth Driver.
        $user = User::find($id);

        // Get the User Model instance - used with the Database Auth Driver.
        //$user = $this->model->find($id);

        if($user === null) {
            // There is no User with this ID.
            $status = __('User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        return $this->getView()
            ->shares('title', __('Edit User'))
            ->with('user', $user);
    }

    public function update($id)
    {
        // Get the User Model instance - used with the Extended Auth Driver.
        $user = User::find($id);

        // Get the User Model instance - used with the Database Auth Driver.
        //$user = $this->model->find($id);

        if($user === null) {
            // There is no User with this ID.
            $status = __('User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Validate the Input data.
        $input = Input::only('username', 'realname', 'password', 'password_confirmation', 'email');

        if(empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password']);
            unset($input['password_confirmation']);
        }

        $validator = $this->validate($input, $id);

        if($validator->passes()) {
            $origName = $user->username;

            // Update the User Model instance.
            $user->username = $input['username'];
            $user->realname = $input['realname'];
            $user->email    = $input['email'];

            if(isset($input['password'])) {
                // Encrypt and add the given Password.
                $user->password = Hash::make($input['password']);
            }

            // Save the User information - used with the Extended Auth Driver.
            $user->save();

            // Save the User information - used with the Database Auth Driver.
            // $user->updated_at = new Carbon();
            //
            //$this->model->update($id, (array) $user);

            // Prepare the flash message.
            $status = __('The User <b>{0}</b> was successfully updated.', $origName);

            return Redirect::to('admin/users')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

    public function destroy($id)
    {
        // Get the User Model instance - used with the Extended Auth Driver.
        $user = User::find($id);

        // Get the User Model instance - used with the Database Auth Driver.
        //$user = $this->model->find($id);

        if($user === null) {
            // There is no User with this ID.
            $status = __('User not found: #{0}', $id);

            return Redirect::to('admin/users')->withStatus($status, 'danger');
        }

        // Destroy the requested User record - used with the Extended Auth Driver.
        $user->delete();

        // Destroy the requested User record - used with the Database Auth Driver.
        //$this->model->delete($id);

        // Prepare the flash message.
        $status = __('The User <b>{0}</b> was successfully deleted.', $user->username);

        return Redirect::to('admin/users')->withStatus($status);
    }

    public function profile()
    {
        $user = Auth::user();

        return $this->getView()
            ->shares('title',  __d('users', 'User Profile'))
            ->with('user', $user);
    }

    public function postProfile()
    {
        $user = Auth::user();

        // Retrieve the Input data.
        $input = Input::only('current_password', 'password', 'password_confirmation');

        // Prepare the Validation Rules, Messages and Attributes.
        $rules = array(
            'current_password'      => 'required|valid_password',
            'password'              => 'required|strong_password',
            'password_confirmation' => 'required|same:password',
        );

        $messages = array(
            'valid_password'  => __d('users', 'The :attribute field is invalid.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'current_password'      => __d('users', 'Current Password'),
            'password'              => __d('users', 'New Password'),
            'password_confirmation' => __d('users', 'Password Confirmation'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_password', function($attribute, $value, $parameters) use ($user)
        {
            return Hash::check($value, $user->password);
        });

        Validator::extend('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        // Create a Validator instance.
        $validator = Validator::make($input, $rules, $messages, $attributes);

        // Validate the Input.
        if ($validator->passes()) {
            $password = $input['password'];

            // Update the password on the User Model instance.
            $user->password = Hash::make($password);

            // Save the User Model instance - used with the Extended Auth Driver.
            $user->save();

            // Save the User Model instance - used with the Database Auth Driver.
            // $user->updated_at = new Carbon();
            //
            //$this->model->updateGenericUser($user);

            // Use a Redirect to avoid the reposting the data.
            $status = __d('users', 'You have successfully updated your Password.');

            return Redirect::back()->withStatus($status);
        }

        // Collect the Validation errors.
        $status = $validator->errors()->all();

        return Redirect::back()->withStatus($status, 'danger');
    }

    public function search()
    {
        // Validation rules
        $rules = array(
            'query' => 'required|min:4|valid_query'
        );

        $messages = array(
            'valid_query' => __('The :attribute field is not a valid query string.'),
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
            ->orWhere('realname', 'LIKE', '%' .$search .'%')
            ->orWhere('email', 'LIKE', '%' .$search .'%')
            ->get();

        // Prepare the Query for displaying.
        $search = htmlentities($search);

        return $this->getView()
            ->shares('title', __('Searching Users for: {0}', $search))
            ->with('search', $search)
            ->with('users', $users);
    }
}
