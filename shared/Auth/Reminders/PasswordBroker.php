<?php

namespace Shared\Auth\Reminders;

use Nova\Auth\UserProviderInterface;
use Nova\Support\Facades\Config;

use Closure;


class PasswordBroker
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var string
     */
    const REMINDER_SENT = 'sent';

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PASSWORD_RESET = 'reset';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'user';

    /**
     * Constant representing an invalid password.
     *
     * @var string
     */
    const INVALID_PASSWORD = 'password';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'token';

    /**
     * The password reminder repository.
     *
     * @var \Shared\Auth\Reminders\ReminderRepositoryInterface  $reminders
     */
    protected $reminders;

    /**
     * The user provider implementation.
     *
     * @var \Nova\Auth\UserProviderInterface
     */
    protected $users;

    /**
     * The custom password validator callback.
     *
     * @var \Closure
     */
    protected $passwordValidator;

    /**
     * The hash(_hmac) key.
     *
     * @var string
     */
    protected $hashKey;


    /**
     * Create a new password broker instance.
     *
     * @param  \Auth\Reminders\ReminderRepositoryInterface  $reminders
     * @param  \Auth\UserProviderInterface  $users
     * @param  \Nova\Mail\Mailer  $mailer
     * @param  string  $reminderView
     * @return void
     */
    public function __construct(ReminderRepositoryInterface $reminders, UserProviderInterface $users, $hashKey)
    {
        $this->users = $users;

        $this->reminders = $reminders;

        $this->hashKey = $hashKey;
    }

    /**
     * Send a password reminder to a user.
     *
     * @param  array     $credentials
     * @param  string    $clientIp
     * @param  \Closure  $callback
     * @return string
     */
    public function remind(array $credentials, $remoteIp)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return self::INVALID_USER;
        }

        // Once we have the reminder token, we are ready to send a message out to the
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token = $this->reminders->create($user);

        // Create the token timestamp and hash.
        $timestamp = time();

        $hash = hash_hmac('sha256', $token .'|' .$remoteIp .'|' .$timestamp, $this->hashKey);

        $user->sendPasswordResetNotification($hash, $timestamp, $token);

        return self::REMINDER_SENT;
    }

    /**
     * Reset the password for the given token.
     *
     * @param  array     $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback)
    {
        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        $user = $this->validateReset($credentials);

        if (! $user instanceof RemindableInterface) {
            return $user;
        }

        $pass = $credentials['password'];

        // Once we have called this callback, we will remove this token row from the
        // table and return the response from this callback so the user gets sent
        // to the destination given by the developers from the callback return.
        call_user_func($callback, $user, $pass);

        $this->reminders->delete($credentials['token']);

        return self::PASSWORD_RESET;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Shared\Auth\Reminders\RemindableInterface
     */
    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return self::INVALID_USER;
        }

        if (! $this->validNewPasswords($credentials)) {
            return self::INVALID_PASSWORD;
        }

        if (! $this->reminders->exists($user, $credentials['token'])) {
            return self::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Set a custom password validator.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function validator(Closure $callback)
    {
        $this->passwordValidator = $callback;
    }

    /**
     * Determine if the passwords match for the request.
     *
     * @param  array  $credentials
     * @return bool
     */
    protected function validNewPasswords(array $credentials)
    {
        list($password, $confirm) = array($credentials['password'], $credentials['password_confirmation']);

        if (isset($this->passwordValidator)) {
            return call_user_func($this->passwordValidator, $credentials) && $password === $confirm;
        }

        return $this->validatePasswordWithDefaults($credentials);
    }

    /**
     * Determine if the passwords are valid for the request.
     *
     * @param  array  $credentials
     * @return bool
     */
    protected function validatePasswordWithDefaults(array $credentials)
    {
        list($password, $confirm) = [$credentials['password'], $credentials['password_confirmation']];

        return $password === $confirm && mb_strlen($password) >= 6;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Shared\Auth\Reminders\RemindableInterface
     *
     * @throws \UnexpectedValueException
     */
    public function getUser(array $credentials)
    {
        $credentials = array_except($credentials, array('token'));

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof RemindableInterface) {
            throw new \UnexpectedValueException("User must implement Remindable interface.");
        }

        return $user;
    }

    /**
     * Get the password reminder repository implementation.
     *
     * @return \Shared\Auth\Reminders\ReminderRepositoryInterface
     */
    public function getRepository()
    {
        return $this->reminders;
    }

}
