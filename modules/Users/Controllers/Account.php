<?php
/**
 * Users - A Controller for managing the Users Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Controllers;

use Nova\Database\ORM\Collection;
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
            'password'              => 'sometimes|required|confirmed|strong_password',
            'password_confirmation' => 'sometimes|required|same:password',
            'realname'              => 'required|valid_name',
        );

        $messages = array(
            'valid_name'      => __d('users', 'The :attribute field is not a valid name.'),
            'strong_password' => __d('users', 'The :attribute field is not strong enough.'),
        );

        $attributes = array(
            'password'              => __d('users', 'New Password'),
            'password_confirmation' => __d('users', 'Password Confirmation'),
            'realname'              => __d('users', 'Name and Surname'),
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

            $options = $item->options ?: array();

            if ($item->type == 'checkbox') {
                $choices = explode("\n", trim(
                    Arr::get($options, 'choices')
                ));

                $count = count($choices = array_filter($choices, function ($value)
                {
                    return ! empty($value);
                }));

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
            $pattern = '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+(?:$|\s+)){2,}$~u';

            return (preg_match($pattern, $value) === 1);
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
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $user->realname = $input['realname'];

        if (isset($input['password'])) {
            $password = $input['password'];

            // Update the password on the User Model instance.
            $user->password = Hash::make($password);
        }

        $user->save();

        //
        // Update the Custom Fields.

        foreach ($items as $item) {
            $value = Arr::get($input, $name = $item->name);

            if (! is_null($field = $user->fields->findBy('name', $name))) {
                $field->value = $value;

                $field->save();

                continue;
            }

            $field = Field::create(array(
                'name'  => $name,
                'type'  => $item->type,
                'value' => $value,

                // Resolve the relationships.
                'field_item_id' => $item->id,
                'user_id'       => $user->id,
            ));
        }

        //
        // Use a Redirect to avoid the reposting the data.

        return Redirect::back()
            ->with('success', __d('users', 'You have successfully updated your Account information.'));
    }

    public function picture(Request $request)
    {
        $user = Auth::user();

        // Create a Validator instance.
        $validator = Validator::make(
            $request->only('image'),
            array('image' => 'required|max:1024|mimes:png,jpg,jpeg,gif'),
            array(),
            array('image' => __d('users', 'Image'))
        );

        // Validate the Input.
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator->errors());
        }

        $user->image = $request->file('image');

        $user->save();

        // Prepare the flash message.
        $status = __d('users', 'The Profile Picture was successfully updated.');

        return Redirect::to('account')->withStatus($status);
    }
}
