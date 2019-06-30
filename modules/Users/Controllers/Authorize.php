<?php
/**
 * Authorize - A Controller for managing the User Authentication.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Modules\Users\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Hash;
use Nova\Support\Facades\Redirect;

use Shared\Support\ReCaptcha;

use Modules\Platform\Controllers\BaseController;
use Modules\Users\Models\User;


class Authorize extends BaseController
{
    /**
     * The currently used Layout.
     *
     * @var mixed
     */
    protected $layout = 'Default';


    /**
     * Display the login view.
     *
     * @return \Nova\View\View
     */
    public function index()
    {
        return $this->createView()
            ->shares('title', __d('users', 'User Login'));
    }

    /**
     * Handle a POST request to login the User.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // Verify the submitted reCAPTCHA
        if(! ReCaptcha::check($request->input('g-recaptcha-response'), $request->ip())) {
            return Redirect::back()->with('danger', __d('users', 'The reCaptcha verification failed.'));
        }

        // Retrieve the Authentication credentials.
        $credentials = $request->only('username', 'password');

        // Make an attempt to login the Guest with the given credentials.
        if(! Auth::attempt($credentials, $request->has('remember'))) {
            return Redirect::back()->with('danger', __d('users', 'Wrong username or password.'));
        }

        // The User is authenticated now; retrieve his Model instance.
        $user = Auth::user();

        if ($user->activated == 0) {
            Auth::logout();

            // User not activated; logout and redirect him to account activation page.
            return Redirect::to('register/verify')
                ->withInput(array('email' => $user->email))
                ->with('danger', __d('users', 'Please activate your Account!'));
        }

        // If the User's password needs rehash.
        else if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($credentials['password']);

            $user->save();
        }

        // Redirect to the User's Dashboard.
        return Redirect::intended('dashboard')
            ->with('success', __d('users', '<b>{0}</b>, you have successfully logged in.', $user->username));
    }

    /**
     * Handle a POST request to logout the current User.
     *
     * @return \Nova\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        // Redirect to the login page.
        $guard = Config::get('auth.defaults.guard', 'web');

        $uri = Config::get("auth.guards.{$guard}.authorize", 'login');

        return Redirect::to($uri)->with('success', __d('users', 'You have successfully logged out.'));
    }
}
