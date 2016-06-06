<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Users\Controllers;

use Core\Controller;
use Core\View;
use Helpers\Url;
use Helpers\ReCaptcha;

use Auth;
use Hash;
use Input;
use Mailer;
use Password;
use Redirect;
use Response;
use Session;
use Validator;


class Registrar extends Controller
{
    protected $template = 'AdminLte';
    protected $layout   = 'default';


    public function __construct()
    {
        parent::__construct();
    }

    protected function before()
    {
        View::share('currentUri', Url::detectUri());

        return parent::before();
    }

    protected function validate(array $data)
    {
        // Validation rules.
        $rules = array(
            'realname' => 'required|min:6|valid_name',
            'username' => 'required|min:6|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed|strong_password'
        );

        $messages = array(
            'valid_name'      => __('The :attribute field is not a valid name.'),
            'strong_password' => __('The :attribute field is not strong enough.'),
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

    /**
     * Display the register view.
     *
     * @return \Core\View
     */
    public function register()
    {
        return $this->getView()
            ->shares('title', __d('users', 'User Registration'))
            ->with('csrfToken', Session::token());
    }

    /**
     * Handle a POST request to login the User.
     *
     * @return Response
     */
    public function postRegister()
    {
        $input = Input::only(
            'username',
            'realname',
            'email',
            'password',
            'password_confirmation'
        );

        // Verify the submitted reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('users', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        // Create a Validator instance.
        $validator = $this->validate($input);

        if($validator->fails()) {
            $status = $validator->errors();

            return Redirect::back()->withInput()->withStatus($status, 'danger');
        }

        // Encrypt the given Password.
        $password = Hash::make($input['password']);

        // Create the Activation code.
        $value = str_shuffle(sha1($input['email'] .spl_object_hash($this) .microtime(true)));

        $token = hash_hmac('sha256', $value, ENCRYPT_KEY);

        // Create the User record.
        User::create(array(
            'username'        => $input['username'],
            'realname'        => $input['realname'],
            'email'           => $input['email'],
            'password'        => $password,
            'activation_code' => $token
        ));

        // Send the associated Activation E-mail.
        Mail::send('Emails/Activation', compact($token), function($message) use ($input)
        {
            $subject = __d('users', 'Please verify your Account registration!');

            $message->to($input['email'], $input['username'])->subject($subject);
        });

        // Prepare the flash message.
        $status = __d('users', 'Thanks for signing up! Please check your E-mail.');

        return Redirect::to('register/status')->withStatus($status);
    }

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function verify($token)
    {
        $user = User::where('activation_code', $token)->first();

        if ($user === null) {
            $status = __d('users', 'Invalid Account Activation code');

            return Redirect::to('register/status')->withStatus($status);
        }

        // Mark the User instance as active.
        $user->active = 1;

        $user->activation_code = null;

        $user->save();

        // Prepare the flash message.
        $status = __d('users', 'You have successfully verified your Account.');

        return Redirect::to('login')->withStatus($status);
    }

    public function status()
    {
        return $this->getView()->shares('title', __d('users', 'Registration Status'));
    }
}
