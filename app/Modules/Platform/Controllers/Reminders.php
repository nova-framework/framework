<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace App\Modules\Platform\Controllers;

use Nova\Helpers\ReCaptcha;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Input;
use Nova\Support\Facades\Redirect;

use Shared\Support\Facades\Password;

use App\Modules\Platform\Controllers\BaseController;


class Authorize extends BaseController
{
    protected $layout = 'Default';


    /**
     * Display the password reminder view.
     *
     * @return \Nova\View\View
     */
    public function remind()
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Password Recovery'));
    }

    /**
     * Handle a POST request to remind a User of their password.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function postRemind()
    {

        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('platform', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        //
        $credentials = Input::only('email');

        switch ($response = Password::remind($credentials)) {
            case Password::INVALID_USER:
                $status = __d('platform', 'We can\'t find a User with that e-mail address.');

                return Redirect::back()->withStatus($status, 'danger');

            case Password::REMINDER_SENT:
                $status = __d('platform', 'Reset instructions have been sent to your email address');

                return Redirect::back()->withStatus($status);
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Nova\View\View
     */
    public function reset($token)
    {
        return $this->createView()
            ->shares('title', __d('platform', 'Password Reset'))
            ->with('token', $token);
    }

    /**
     * Handle a POST request to reset a User's password.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function postReset()
    {
        // Verify the reCAPTCHA
        if(! ReCaptcha::check()) {
            $status = __d('platform', 'Invalid reCAPTCHA submitted.');

            return Redirect::back()->withStatus($status, 'danger');
        }

        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );

        // Add to Password Broker a custom validation.
        Password::validator(function($credentials)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $credentials['password']) === 1);
        });

        $response = Password::reset($credentials, function($user, $password)
        {
            $user->password = Hash::make($password);

            $user->save();
        });

        // Parse the response.
        switch ($response) {
            case Password::INVALID_PASSWORD:
                $status = __d('platform', 'Passwords must be strong enough and match the confirmation.');

                break;
            case Password::INVALID_TOKEN:
                $status = __d('platform', 'This password reset token is invalid.');

                break;
            case Password::INVALID_USER:
                $status = __d('platform', 'We can\'t find a User with that e-mail address.');

                break;
            case Password::PASSWORD_RESET:
                $status = __d('platform', 'You have successfully reset your Password.');

                return Redirect::to('login')->withStatus($status);
        }

        return Redirect::back()->withStatus($status, 'danger');
    }
}
