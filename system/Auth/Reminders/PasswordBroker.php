<?php

namespace Auth\Reminders;

use Auth\Reminders\ReminderRepository;
use Auth\UserProvider;
use Mail\Mailer;

use Closure;


class PasswordBroker
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var int
     */
    const REMINDER_SENT = 'reminders.sent';

    /**
     * Constant representing a successfully reset password.
     *
     * @var int
     */
    const PASSWORD_RESET = 'reminders.reset';

    /**
     * Constant representing the user not found response.
     *
     * @var int
     */
    const INVALID_USER = 'reminders.user';

    /**
     * Constant representing an invalid password.
     *
     * @var int
     */
    const INVALID_PASSWORD = 'reminders.password';

    /**
     * Constant representing an invalid token.
     *
     * @var int
     */
    const INVALID_TOKEN = 'reminders.token';

    /**
     * The password reminder repository.
     *
     * @var \Auth\Reminders\ReminderRepository  $reminders
     */
    protected $reminders;

    /**
     * The user provider implementation.
     *
     * @var \Auth\UserProvider
     */
    protected $users;

    /**
     * The mailer instance.
     *
     * @var \Mail\Mailer
     */
    protected $mailer;

    /**
     * The view of the password reminder e-mail.
     *
     * @var string
     */
    protected $reminderView;

    /**
     * The custom password validator callback.
     *
     * @var \Closure
     */
    protected $passwordValidator;

    /**
     * Create a new password broker instance.
     *
     * @param  \Auth\Reminders\ReminderRepository  $reminders
     * @param  \Auth\UserProviderInterface  $users
     * @param  \Mail\Mailer  $mailer
     * @param  string  $reminderView
     * @return void
     */
    public function __construct(ReminderRepository $reminders,
                                UserProvider $users,
                                Mailer $mailer,
                                $reminderView)
    {
        $this->users     = $users;
        $this->mailer    = $mailer;
        $this->reminders = $reminders;

        $this->reminderView = $reminderView;
    }

    /**
     * Send a password reminder to a user.
     *
     * @param  array    $credentials
     * @param  Closure  $callback
     * @return string
     */
    public function remind(array $credentials, Closure $callback = null)
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return self::INVALID_USER;
        }

        $token = $this->reminders->create($user);

        $this->sendReminder($user, $token, $callback);

        return self::REMINDER_SENT;
    }

    /**
     * Send the password reminder e-mail.
     *
     * @param  \Auth\Reminders\RemindableInterface  $user
     * @param  string   $token
     * @param  Closure  $callback
     * @return int
     */
    public function sendReminder(RemindableInterface $user, $token, Closure $callback = null)
    {
        $view = $this->reminderView;

        return $this->mailer->send($view, compact('token', 'user'), function($message) use ($user, $token, $callback)
        {
            $message->to($user->getReminderEmail());

            if ( ! is_null($callback)) call_user_func($callback, $message, $user, $token);
        });
    }

    /**
     * Reset the password for the given token.
     *
     * @param  array    $credentials
     * @param  Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback)
    {
        $user = $this->validateReset($credentials);

        if (! $user instanceof RemindableInterface) {
            return $user;
        }

        $password = $credentials['password'];

        call_user_func($callback, $user, $password);

        $this->reminders->delete($credentials['token']);

        return self::PASSWORD_RESET;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Auth\Reminders\RemindableInterface
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
            return (call_user_func($this->passwordValidator, $credentials) && ($password == $confirm));
        } else {
            return $this->validatePasswordWithDefaults($credentials);
        }
    }

    /**
     * Determine if the passwords are valid for the request.
     *
     * @param  array  $credentials
     * @return bool
     */
    protected function validatePasswordWithDefaults(array $credentials)
    {
        $matches = $credentials['password'] == $credentials['password_confirmation'];

        return ($matches && $credentials['password'] && (strlen($credentials['password']) >= 6));
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Auth\Reminders\RemindableInterface
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
     * Get the Password Reminder Repository implementation.
     *
     * @return \Auth\Reminders\ReminderRepository
     */
    protected function getRepository()
    {
        return $this->reminders;
    }

}
