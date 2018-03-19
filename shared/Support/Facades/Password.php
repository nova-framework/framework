<?php

namespace Shared\Support\Facades;

use Nova\Support\Facades\Facade;


/**
 * @see \Nova\Auth\Reminders\PasswordBroker
 */
class Password extends Facade
{
    /**
     * Constant representing a successfully sent reminder.
     *
     * @var int
     */
    const REMINDER_SENT = 'sent';

    /**
     * Constant representing a successfully reset password.
     *
     * @var int
     */
    const PASSWORD_RESET = 'reset';

    /**
     * Constant representing the user not found response.
     *
     * @var int
     */
    const INVALID_USER = 'user';

    /**
     * Constant representing an invalid password.
     *
     * @var int
     */
    const INVALID_PASSWORD = 'password';

    /**
     * Constant representing an invalid token.
     *
     * @var int
     */
    const INVALID_TOKEN = 'token';

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'auth.password'; }

}
