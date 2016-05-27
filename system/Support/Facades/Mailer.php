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
     * Create a proper Swift Transport instance.
     *
     * @param array $config
     * @return \Swift_Transport
     */
    protected static function getSwiftTransport(array $config)
    {
        extract($config);

        // Get a default Swift Transport instance.
        if ($driver == 'smtp') {
            $instance = SmtpTransport::newInstance($host, $port);

            if (isset($encryption)) {
                $instance->setEncryption($encryption);
            }

            if (isset($username)) {
                $instance->setUsername($username);
                $instance->setPassword($password);
            }

            return $instance;
        } else if ($driver == 'sendmail') {
            return SendmailTransport::newInstance($sendmail);
        } else if ($driver == 'mail') {
            return MailTransport::newInstance();
        } else if ($driver != 'custom') {
            throw new \InvalidArgumentException('Invalid mail driver.');
        }

        if(class_exists($transport)) {
            return call_user_func(array($transport, 'newInstance'), $config);
        }

        throw new \InvalidArgumentException('Invalid class specified for the mail driver.');
    }

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

        // Get the pretending mode.
        $pretend = $config['pretend'];

        // Get the specified Swift Transport instance.
        $transport = static::getSwiftTransport($config);

        // Get a Swift Mailer instance.
        $swift = new SwiftMailer($transport);

        // Get the Events Dispatcher instance.
        $events = Events::getInstance();

        static::$mailer = $mailer = new NovaMailer($swift, $events);

        $mailer->pretend($pretend);

        return $mailer;
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
