<?php
/**
 * Database - A Facade to the Database Connection.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Support\Facades;

use Core\Config;
use Events\Dispatcher as Events;
use Mail\Mailer as NovaMailer;

use Swift_Mailer as SwiftMailer;
use Swift_SmtpTransport as SmtpTransport;
use Swift_MailTransport as MailTransport;
use Swift_SendmailTransport as SendmailTransport;


class Mailer
{
    /**
     * The SwiftMailer instance being handled.
     *
     * @var \Mail\Mailer|null
     */
    protected static $mailer;


    /**
     * Return the default \Mail\Mailer instance.
     *
     * @return \Mail\Mailer
     *
     * @throws Exception
     */
    protected static function getMailer()
    {
        if (isset(static::$mailer)) {
            return static::$mailer;
        }

        // Get the Mailer configuration.
        $config = Config::get('mail');

        // Get a Swift Transport instance.
        switch ($config['driver']) {
            case 'smtp':
                extract($config);

                $transport = SmtpTransport::newInstance($host, $port);

                if (isset($encryption)) {
                    $transport->setEncryption($encryption);
                }

                if (isset($username)) {
                    $transport->setUsername($username);
                    $transport->setPassword($password);
                }

                break;
            case 'sendmail':
                $transport = SendmailTransport::newInstance($config['sendmail']);

                break;
            case 'mail':
                $transport = MailTransport::newInstance();

                break;
            default:
                throw new \InvalidArgumentException('Invalid mail driver.');
        }

        // Get a Swift Mailer instance.
        $swift = SwiftMailer($transport);

        // Get the Events Dispatcher instance.
        $events = Events::getInstance();

        return static::$mailer = new NovaMailer($swift, $events);
    }

    /**
     * Return the default SwiftMailer instance.
     *
     * @return \Mail\Mailer
     */
    public static function instance()
    {
        return static::getMailer();
    }

    /**
     * Magic Method for calling the methods on the default SwiftMailer instance.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        $instance = static::getMailer();

        // Call the non-static method from the Connection instance.
        return call_user_func_array(array($instance, $method), $params);
    }
}
