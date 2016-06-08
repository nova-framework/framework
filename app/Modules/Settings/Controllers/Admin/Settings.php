<?php
/**
 * Settings - Implements a simple Administration Settings.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Settings\Controllers\Admin;

use Core\View;
use Helpers\FastCache;
use Helpers\Url;
use Helpers\Csrf;

use App\Core\Controller;

use Auth;
use Config;
use Input;
use Session;
use Redirect;
use Request;
use Validator;


class Settings extends Controller
{
    protected $template = 'AdminLte';
    protected $layout   = 'backend';


    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        // Check the User Authorization.
        if (! Auth::user()->hasRole('administrator')) {
            $status = __d('settings', 'You are not authorized to access this resource.');

            return Redirect::to('admin/dashboard')->withStatus($status, 'warning');
        }

        // Leave to parent's method the Execution Flow decisions.
        return parent::before();
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
            'mailHost'        => 'url',
            'mailPort'        => 'numeric',
            'mailFromAddress' => 'required|email',
            'mailFromName'    => 'required|max:100',
            'mailEncryption'  => 'alpha',
            'mailUsername'    => 'max:100',
            'mailPassword'    => 'max:100',
        );

        $attributes = array(
            // The Application.
            'siteName'        => __d('settings', 'Site Name'),
            'siteSkin'        => __d('settings', 'Site Skin'),

            // The Mailer
            'mailDriver'      => __d('settings', 'Mail Driver'),
            'mailHost'        => __d('settings', 'Server Name'),
            'mailPort'        => __d('settings', 'Server Port'),
            'mailFromAddress' => __d('settings', 'Mail from Adress'),
            'mailFromName'    => __d('settings', 'Mail from Name'),
            'mailEncryption'  => __d('settings', 'Encryption'),
            'mailUsername'    => __d('settings', 'Server Username'),
            'mailPassword'    => __d('settings', 'Server Password'),
        );

        return Validator::make($data, $rules, array(), $attributes);
    }

    public function index()
    {
        // Load the options from database.
        $options = array(
            // The Application.
            'siteName'        => Config::get('app.siteName'),
            'siteSkin'        => Config::get('app.siteSkin'),
            'cronToken'       => Config::get('app.cronToken'),

            // The Mailer
            'mailDriver'      => Config::get('mail.driver'),
            'mailHost'        => Config::get('mail.host'),
            'mailPort'        => Config::get('mail.port'),
            'mailFromAddress' => Config::get('mail.from.address'),
            'mailFromName'    => Config::get('mail.from.name'),
            'mailEncryption'  => Config::get('mail.encryption'),
            'mailUsername'    => Config::get('mail.username'),
            'mailPassword'    => Config::get('mail.password'),
        );

        return $this->getView()
            ->shares('title', __d('settings', 'Settings'))
            ->withOptions($options);
    }

    public function store()
    {
        // Validate the Input data.
        $input = Input::all();

        $validator = $this->validate($input);

        if($validator->passes()) {
            // The Application.
            Config::set('app.siteName',  $input['siteName']);
            Config::set('app.siteSkin',  $input['siteSkin']);

            // The Mailer
            Config::set('mail.driver',       $input['mailDriver']);
            Config::set('mail.host',         $input['mailHost']);
            Config::set('mail.port',         $input['mailPort']);
            Config::set('mail.from.address', $input['mailFromAddress']);
            Config::set('mail.from.name',    $input['mailFromName']);
            Config::set('mail.encryption',   $input['mailEncryption']);
            Config::set('mail.username',     $input['mailUsername']);
            Config::set('mail.password',     $input['mailPassword']);

            // Prepare the flash message.
            $status = __d('settings', 'The Settings was successfully updated.');

            return Redirect::to('admin/settings')->withStatus($status);
        }

        // Errors occurred on Validation.
        $status = $validator->errors();

        return Redirect::back()->withInput()->withStatus($status, 'danger');
    }

}
