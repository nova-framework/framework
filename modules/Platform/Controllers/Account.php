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
use Nova\Support\Collection;

use Modules\Platform\Controllers\BaseController;
use Modules\Users\Models\FieldItem;
use Modules\Users\Models\User;


class Account extends BaseController
{
    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Frontend';


    protected function validator(array $data, User $user, Collection $items)
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

        // Prepare the dynamic rules and attributes for Field Items.
        foreach ($items as $item) {
            if (empty($rule = $item->rule)) {
                continue;
            }

            $key = str_replace('-', '_', $item->name);

            if (isset($data[$key]) && empty($data[$key]) && Str::contains($rule, 'sometimes')) {
                unset($data[$key]);
            }

            if ($item->type == 'checkbox') {
                $options = $item->options ?: array();

                $count = count(explode("\n", trim(
                    Arr::get($options, 'choices')
                )));

                if ($count > 1) {
                    foreach (range(0, $count - 1) as $index) {
                        $name = $key .'.' .$index;

                        //
                        $rules[$name] = $rule;

                        $attributes[$name] = $item->title;
                    }

                    $rule = Str::contains($rule, 'required') ? 'required|array' : 'array';
                }
            }

            $rules[$key] = $rule;

            $attributes[$key] = $item->title;
        }

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

        $items = FieldItem::all();

        return $this->createView()
            ->shares('title',  __d('users', 'Account'))
            ->with(compact('user', 'items'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $items = FieldItem::orderBy('order', 'asc')->get();

        // Retrieve the Input data.
        $input = $request->all();

        if (empty($input['password']) && empty($input['password_confirm'])) {
            unset($input['password'], $input['password_confirmation']);
        }

        // Create a Validator instance.
        $validator = $this->validator($input, $user, $items);

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

        //
        // Use a Redirect to avoid the reposting the data.

        return Redirect::back()
            ->with('success', __d('users', 'You have successfully updated your Account information.'));
    }

    public function picture(Request $request)
    {
        $user = Auth::user();

        // Retrieve the Input data.
        $input = $request->only('image');

        // Create a Validator instance.
        $validator = Validator::make(
            $input,
            array('image' => 'required|max:1024|mimes:png,jpg,jpeg,gif'),
            array(),
            array('image' => __d('users', 'Image'))
        );

        // Validate the Input.
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        // Update the User record.
        $user->image = $input['image'];

        $user->save();

        // Prepare the flash message.
        $status = __d('users', 'The Profile Picture was successfully updated.');

        return Redirect::to('account')->withStatus($status);
    }
}
