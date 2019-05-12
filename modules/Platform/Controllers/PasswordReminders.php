<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Platform\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\DB;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Redirect;
use Nova\Support\Str;

use Shared\Support\Facades\Password;
use Shared\Support\ReCaptcha;

use Modules\Platform\Controllers\BaseController;

use Carbon\Carbon;


class PasswordReminders extends BaseController
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
    public function postRemind(Request $request)
    {
        $remoteIp = $request->ip();

        // Verify the reCAPTCHA
        if(! ReCaptcha::check($request->input('g-recaptcha-response'), $remoteIp)) {
            return Redirect::back()->with('danger', __d('platform', 'The reCaptcha verification failed.'));
        }

        $credentials = $request->only('email');

        switch ($response = Password::remind($credentials, $remoteIp)) {
            case Password::INVALID_USER:
                return Redirect::back()
                    ->with('danger', __d('platform', 'We can\'t find an User with that e-mail address.'));

            case Password::REMINDER_SENT:
                return Redirect::back()
                    ->with('success', __d('platform', 'Reset instructions have been sent to your email address.'));
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Nova\Http\RedirectResponse|\Nova\View\View
     */
    public function reset(Request $request, $hash, $timestamp, $token)
    {
        $remoteIp = $request->ip();

        // Get the limiter constraints.
        $maxAttempts = Config::get('platform::throttle.maxAttempts', 5);
        $lockoutTime = Config::get('platform::throttle.lockoutTime', 1); // In minutes.

        // Compute the throttle key.
        $throttleKey = $this->getGuard() .'|reminders|' .$remoteIp;

        // Make a Rate Limiter instance, via Container.
        $limiter = App::make('Nova\Cache\RateLimiter');

        if ($limiter->tooManyAttempts($throttleKey, $maxAttempts, $lockoutTime)) {
            $seconds = $limiter->availableIn($throttleKey);

            return Redirect::to('password/remind')
                ->with('danger', __d('platform', 'Too many login attempts, please try again in {0} seconds.', $seconds));
        }

        $reminder = Config::get('auth.defaults.reminder', 'users');

        $validity = Config::get("auth.reminders.{$reminder}.expire", 60);

        $oldest = Carbon::parse('-' .$validity .' minutes');

        //
        $hashKey = Config::get('app.key');

        $data = $token .'|' .$remoteIp .'|' .$timestamp;

        if (! hash_equals($hash, hash_hmac('sha256', $data, $hashKey)) || ($oldest->timestamp > hexdec($timestamp))) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('password/remind')
                ->with('danger', __d('platform', 'Link is invalid, please request a new link.'));
        }

        $reminder = DB::table('password_reminders')
            ->where('token', $token)
            ->where('created_at', '>', $oldest)
            ->first();

        if (is_null($reminder)) {
            $limiter->hit($throttleKey, $lockoutTime);

            return Redirect::to('password/remind')
                ->with('danger', __d('platform', 'Link is invalid, please request a new link.'));
        }

        $limiter->clear($throttleKey);

        return $this->createView()
            ->shares('title', __d('platform', 'Password Reset'))
            ->with('email', $reminder->email)
            ->with('token', $token);
    }

    /**
     * Handle a POST request to reset a User's password.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function postReset(Request $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        // Add to Password Broker a custom validation.
        Password::validator(function ($credentials)
        {
            $pattern = "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/";

            return (preg_match($pattern, $credentials['password']) === 1);
        });

        $response = Password::reset($credentials, function ($user, $password)
        {
            $this->resetPassword($user, $password);
        });

        $message = Config::get('platform::reminders.messages.' .$response);

        // Calculate the User's Dashboard URI.
        $guard = $this->getGuard();

        $dashboard = Config::get("auth.guards.{$guard}.paths.dashboard", 'dashboard');

        switch ($response) {
            case Password::PASSWORD_RESET:
                return Redirect::to($dashboard)->with('success', $message);

            case Password::INVALID_TOKEN:
                return Redirect::to('password/remind')->with('danger', $message);

            default:
                return Redirect::back()->withInput($request->only('email'))->with('danger', $message);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Shared\Auth\Reminders\RemindableInterface  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->remember_token = Str::random(60);

        $user->save();

        Auth::guard($this->getGuard())->login($user);
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return string|null
     */
    protected function getGuard()
    {
        return 'web';
    }
}
