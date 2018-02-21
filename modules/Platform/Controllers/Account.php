<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Platform\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Gate;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;
use Nova\Support\Arr;

use Modules\Platform\Controllers\BaseController;

use Modules\Users\Events\MetaFields\UpdateUserValidation;
use Modules\Users\Events\MetaFields\UserEditing;
use Modules\Users\Events\MetaFields\UserSaved;
use Modules\Users\Events\MetaFields\UserShowing;

use Modules\Users\Models\User;


class Account extends BaseController
{
    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Frontend';


    protected function validator(array $data, User $user)
    {
        // Prepare the Validation Rules, Messages and Attributes.
        $rules = array(
            'current_password'      => 'required|valid_password',
            'password'              => 'sometimes|required|confirmed|strong_password',
            'password_confirmation' => 'sometimes|required|same:password',
        );

        $messages = array(
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'valid_password'  => __d('users', 'The :attribute field is a valid password.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'current_password'      => __d('users', 'Current Password'),
            'password'              => __d('users', 'New Password'),
            'password_confirmation' => __d('users', 'Password Confirmation'),
        );

        // Create a Validator instance.
        $validator = Validator::make($data, $rules, $messages, $attributes);

        // Add the custom Validation Rule commands.
        $validator->addExtension('valid_name', function($attribute, $value, $parameters)
        {
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){1,}$~u';

            return (preg_match($pattern, $value) === 1);
        });

        $validator->addExtension('valid_password', function($attribute, $value, $parameters) use ($user)
        {
            return Hash::check($value, $user->password);
        });

        $validator->addExtension('strong_password', function($attribute, $value, $parameters)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $value) === 1);
        });

        return $validator;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Handle the User's Meta Fields for displaying.
        $responses = Event::fire(new UserShowing($user));

        //
        $fields = array();

        foreach ($responses as $response) {
            if (is_array($response) && ! empty($response)) {
                $fields = array_merge($fields, $response);
            }
        }

        // Handle the User's Meta Fields for editing.
        $responses = Event::fire(new UserEditing($user));

        $html = implode("\n", array_filter($responses, function ($response)
        {
            return ! is_null($response);
        }));

        return $this->createView()
            ->shares('title',  __d('users', 'Account'))
            ->with('user', $user)
            ->with('metaFields', $fields)
            ->with('metaEditor', $html);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Retrieve the Input data.
        $input = $request->all();

        if (empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password'], $input['password_confirmation']);
        }

        // Create a Validator instance.
        $validator = $this->validator($input, $user);

        Event::fire(new UpdateUserValidation($validator, $user));

        // Validate the Input.
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        if (isset($input['password'])) {
            $password = $input['password'];

            // Update the password on the User Model instance.
            $user->password = Hash::make($password);
        }

        // Save the User Model instance.
        $user->save();

        // Update the Meta / Custom Fields.
        Event::fire(new UserSaved($user));

        //
        // Use a Redirect to avoid the reposting the data.

        return Redirect::back()
            ->with('success', __d('users', 'You have successfully updated your Account information.'));
    }
}
