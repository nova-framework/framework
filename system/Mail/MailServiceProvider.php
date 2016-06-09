<?php
/**
 * MailServiceProvider - Implements a Service Provider for Mailer.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

namespace Mail;

use Mail\Mailer;
use Support\ServiceProvider;

use Swift_Mailer;
use Swift_SmtpTransport as SmtpTransport;
use Swift_MailTransport as MailTransport;
use Swift_SendmailTransport as SendmailTransport;


class MailServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the Provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the Service Provider.
     *
     * @return void
     */
    public function register()
    {
        $me = $this;

        $this->app->bindShared('mailer', function($app) use ($me)
        {
            $me->registerSwiftMailer();

            $mailer = new Mailer($app['view'], $app['swift.mailer'], $app['events']);

            $from = $app['config']['mail.from'];

            if (is_array($from) && isset($from['address'])) {
                $mailer->alwaysFrom($from['address'], $from['name']);
            }

            return $mailer;
        });
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $config = $this->app['config']['mail'];

        $this->registerSwiftTransport($config);

        $this->app['swift.mailer'] = new Swift_Mailer($this->app['swift.transport']);
    }

    /**
     * Register the Swift Transport instance.
     *
     * @param  array  $config
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function registerSwiftTransport($config)
    {
        switch ($config['driver']) {
            case 'smtp':
                return $this->registerSmtpTransport($config);

            case 'sendmail':
                return $this->registerSendmailTransport($config);

            case 'mail':
                return $this->registerMailTransport($config);

            default:
                throw new \InvalidArgumentException('Invalid mail driver.');
        }
    }

    /**
     * Register the SMTP Swift Transport instance.
     *
     * @param  array  $config
     * @return void
     */
    protected function registerSmtpTransport($config)
    {
        $this->app['swift.transport'] = $this->app->share(function($app) use ($config)
        {
            extract($config);

            $transport = SmtpTransport::newInstance($host, $port);

            if (isset($encryption)) {
                $transport->setEncryption($encryption);
            }

            if (isset($username)) {
                $transport->setUsername($username);

                $transport->setPassword($password);
            }

            return $transport;
        });
    }

    /**
     * Register the Sendmail Swift Transport instance.
     *
     * @param  array  $config
     * @return void
     */
    protected function registerSendmailTransport($config)
    {
        $this->app['swift.transport'] = $this->app->share(function($app) use ($config)
        {
            return SendmailTransport::newInstance($config['sendmail']);
        });
    }

    /**
     * Register the Mail Swift Transport instance.
     *
     * @param  array  $config
     * @return void
     */
    protected function registerMailTransport($config)
    {
        $this->app['swift.transport'] = $this->app->share(function()
        {
            return MailTransport::newInstance();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('mailer');
    }

}
