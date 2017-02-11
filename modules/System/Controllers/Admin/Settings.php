<?php
/**
 * Settings - Implements a simple Administration Settings.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\System\Controllers\Admin;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Event;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;
use Nova\Support\Facades\View;

use App\Core\BackendController;
use App\Models\Option;


class Settings extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        //
        $this->beforeFilter('@adminUsersFilter');
    }

    protected function validate(array $data)
    {
        // Validation rules
        $rules = array(
            // The Application.
            'siteName'        => 'required|max:100',
            'siteSkin'        => 'required|alpha_dash',

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
            'valid_host' => __d('users', 'The :attribute field is not a valid host.'),
        );

        $attributes = array(
            // The Application.
            'siteName'        => __d('system', 'Site Name'),
            'siteSkin'        => __d('system', 'Site Skin'),

            // The Mailer
            'mailDriver'      => __d('system', 'Mail Driver'),
            'mailHost'        => __d('system', 'Server Name'),
            'mailPort'        => __d('system', 'Server Port'),
            'mailFromAddress' => __d('system', 'Mail from Adress'),
            'mailFromName'    => __d('system', 'Mail from Name'),
            'mailEncryption'  => __d('system', 'Encryption'),
            'mailUsername'    => __d('system', 'Server Username'),
            'mailPassword'    => __d('system', 'Server Password'),
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
            'siteName'        => Input::old('siteName', Config::get('app.name')),
            'siteSkin'        => Input::old('siteSkin', Config::get('app.color_scheme')),

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

        return $this->getView()
            ->shares('title', __d('system', 'Settings'))
            ->withOptions($options);
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::only(
            'siteName', 'siteSkin',
            'mailDriver', 'mailHost', 'mailPort', 'mailFromAddress', 'mailFromName', 'mailEncryption', 'mailUsername', 'mailPassword'
        );

        $validator = $this->validate($input);

        if($validator->passes()) {
            // The Application.
            Option::set('app.name',          $input['siteName']);
            Option::set('app.color_scheme',  $input['siteSkin']);

            // The Mailer
            Option::set('mail.driver',       $input['mailDriver']);
            Option::set('mail.host',         $input['mailHost']);
            Option::set('mail.port',         $input['mailPort']);
            Option::set('mail.from.address', $input['mailFromAddress']);
            Option::set('mail.from.name',    $input['mailFromName']);
            Option::set('mail.encryption',   $input['mailEncryption']);
            Option::set('mail.username',     $input['mailUsername']);
            Option::set('mail.password',     $input['mailPassword']);

            // Invalidate the cached system options.
            Cache::forget('system_options');

            // Fire the associated Event.
            $user = Auth::user();

            Event::fire('app.modules.system.settings.updated', array($user, $input));

            // Prepare the flash message.
            $status = __d('system', 'The Settings was successfully updated.');

            return Redirect::to('admin/settings')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

}
