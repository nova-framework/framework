<?php
/**
 * Settings - Implements a simple Administration Settings.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace AcmeCorp\Backend\Controllers\Admin;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use AcmeCorp\Backend\Controllers\BaseController;
use App\Models\Option;


class Settings extends BaseController
{

    public function __construct()
    {
        $this->middleware('role:administrator');
    }

    protected function validator(array $data)
    {
        // Validation rules
        $rules = array(
            // The Application.
            'siteName'        => 'required|max:100',

            // The Mailer
            'mailDriver'      => 'required|alpha',
            'mailHost'        => 'valid_host',
            'mailPort'        => 'numeric',
            'mailFromAddress' => 'required|email',
            'mailFromName'    => 'required|max:100',
            'mailEncryption'  => 'alpha',
            'mailUsername'    => 'max:100',
            'mailPassword'    => 'max:100',
        );

        $messages = array(
            'valid_host' => __d('backend', 'The :attribute field is not a valid host.'),
        );

        $attributes = array(
            // The Application.
            'siteName'        => __d('backend', 'Site Name'),
            'siteSkin'        => __d('backend', 'Site Skin'),

            // The Mailer
            'mailDriver'      => __d('backend', 'Mail Driver'),
            'mailHost'        => __d('backend', 'Server Name'),
            'mailPort'        => __d('backend', 'Server Port'),
            'mailFromAddress' => __d('backend', 'Mail from Adress'),
            'mailFromName'    => __d('backend', 'Mail from Name'),
            'mailEncryption'  => __d('backend', 'Encryption'),
            'mailUsername'    => __d('backend', 'Server Username'),
            'mailPassword'    => __d('backend', 'Server Password'),
        );

        // Add the custom Validation Rule commands.
        Validator::extend('valid_host', function($attribute, $value, $parameters)
        {
            return (filter_var($value, FILTER_VALIDATE_URL, ~FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED) !== false);
        });

        return Validator::make($data, $rules, $messages, $attributes);
    }

    public function index()
    {
        // Load the Options from database.
        $options = array(
            // The Application.
            'siteName' => Input::old('siteName', Config::get('app.name')),

            // The Mailer
            'mailDriver'      => Input::old('mailDriver',      Config::get('mail.driver')),
            'mailHost'        => Input::old('mailHost',        Config::get('mail.host')),
            'mailPort'        => Input::old('mailPort',        Config::get('mail.port')),
            'mailFromAddress' => Input::old('mailFromAddress', Config::get('mail.from.address')),
            'mailFromName'    => Input::old('mailFromName',    Config::get('mail.from.name')),
            'mailEncryption'  => Input::old('mailEncryption',  Config::get('mail.encryption')),
            'mailUsername'    => Input::old('mailUsername',    Config::get('mail.username')),
            'mailPassword'    => Input::old('mailPassword',    Config::get('mail.password')),
        );

        return $this->createView()
            ->shares('title', __d('backend', 'Settings'))
            ->with('options', $options);
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::only(
            'siteName',
            'mailDriver', 'mailHost', 'mailPort', 'mailFromAddress', 'mailFromName', 'mailEncryption', 'mailUsername', 'mailPassword'
        );

        $validator = $this->validator($input);

        if($validator->passes()) {
            // The Application.
            Option::set('app.name', $input['siteName']);

            // The Mailer
            Option::set('mail.driver',       $input['mailDriver']);
            Option::set('mail.host',         $input['mailHost']);
            Option::set('mail.port',         $input['mailPort']);
            Option::set('mail.from.address', $input['mailFromAddress']);
            Option::set('mail.from.name',    $input['mailFromName']);
            Option::set('mail.encryption',   $input['mailEncryption']);
            Option::set('mail.username',     $input['mailUsername']);
            Option::set('mail.password',     $input['mailPassword']);

            // Invalidator the cached system options.
            Cache::forget('system_options');

            // Fire the associated Event.
            $user = Auth::user();

            Event::fire('app.modules.system.settings.updated', array($user, $input));

            // Prepare the flash message.
            $status = __d('backend', 'The Settings was successfully updated.');

            return Redirect::to('admin/settings')->with('success', $status);
        }

        // Errors occurred on Validation.
        return Redirect::back()->withInput()->withErrors($validator->errors());
    }

}
