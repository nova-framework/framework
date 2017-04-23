<?php
/**
 * Mailer - Initialize a simple Mailing Handler for Logging.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */
namespace Shared\Log;

use Nova\Foundation\Application;

use Monolog\Logger;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Formatter\HtmlFormatter;

use Swift_Message;


class Mailer
{
    public static function initHandler(Application $app, $logLevel = Logger::ERROR)
    {
        // Retrieve the Config from Application.
        $config = $app['config']['mail.from'];

        // Prepare the instances for Swift Mailer and Message.
        $swiftMailer = $app['mailer']->getSwiftMailer();

        $swiftMessage = Swift_Message::newInstance(SITETITLE.' [Log] ERROR!')
            ->setContentType('text/html')
            ->setFrom($config['address'], $config['name'])
            ->setTo(SITEEMAIL);

        // Create a SwiftMailerHandler instance.
        $monologHandler = new SwiftMailerHandler(
            $swiftMailer,
            $swiftMessage,
            $logLevel,     // Set the minimal Log Level for Mail
            true           // Bubble to next handler?
        );

        // Setup a HTML Formatter to this handler.
        $monologFormatter = new HtmlFormatter();

        $monologHandler->setFormatter($monologFormatter);

        // Push the handler to Monolog instance.
        $app['log']->getMonolog()->pushHandler($monologHandler);
    }
}
