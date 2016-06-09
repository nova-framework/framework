<?php
/**
 * Password - A Facade to the Auth System's Password Broker.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Database\Connection;
use Auth\DatabaseUserProvider;
use Auth\ExtendedUserProvider;
use Auth\Reminders\PasswordBroker;
use Auth\Reminders\ReminderRepository;
use Support\Facades\Config;
use Support\Facades\Mailer;

class Password
{
    /**
     * The Password Broker instance being handled.
     *
     * @var \Auth\Reminders\PasswordBroker|null
     */
    protected static $passwordBroker;

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
     * Create a proper Auth User Provider instance.
     *
     * @param array $config
     * @return \Auth\UserProviderInterface
     *
     * @throw \InvalidArgumentException
     */
    protected static function getUserProvider(array $config)
    {
        // Get the current Authentication Driver.
        $driver = $config['driver'];

        if ($driver == 'database') {
            $table = $config['table'];

            // Get a Database Connection instance.
            $connection = Connection::getInstance();

            return new DatabaseUserProvider($connection, $table);
        } else if ($driver == 'extended') {
            $model = '\\'.ltrim($config['model'], '\\');

            if(! class_exists($model)) {
                throw new \InvalidArgumentException('Invalid Auth Model.');
            }

            return new ExtendedUserProvider($model);
        }

        throw new \InvalidArgumentException('Invalid Auth Driver.');
    }

    /**
     * Create a proper Reminder Repository instance.
     *
     * @param array $config
     * @return \Auth\Reminders\ReminderRepository
     *
     * @throw \InvalidArgumentException
     */
    protected static function getReminderRepository(array $config)
    {
        $config = $config['reminder'];

        // Get a Connection instance.
        $connection = Connection::getInstance();

        return new ReminderRepository(
            $connection,
            $config['table'],
            ENCRYPT_KEY,
            $config['expire']
        );
    }

    /**
     * Get an Password Broker instance.
     *
     * @return \Auth\Reminders\PasswordBroker
     */
    protected static function getPasswordBroker()
    {
        if (isset(static::$passwordBroker)) {
            return static::$passwordBroker;
        }

        return static::$passwordBroker = static::factory();
    }

    /**
     * Create a new Password Broker instance.
     *
     * @return \Auth\Reminders\PasswordBroker
     */
    protected static function factory()
    {
        $config = Config::get('auth');

        // Get a User Provider instance.
        $provider = static::getUserProvider($config);

        // Get a Reminder Repository instance.
        $repository = static::getReminderRepository($config);

        // Get a Mailer instance.
        $mailer = Mailer::instance();

        // Get the Reminder View.
        $email = array_get($config, 'reminder.email', 'Emails/Auth/Reminder');

        return new PasswordBroker($repository, $provider, $mailer, $email);
    }

    /**
     * Magic Method for calling the methods on the default Password Broker instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        // Get a HttpRequest instance.
        $instance = static::getPasswordBroker();

        // Call the non-static method from the Request instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
