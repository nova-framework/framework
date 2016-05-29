<?php

namespace Mail;

use Core\View;
use Events\Dispatcher;

use Closure;
use Swift_Mailer;
use Swift_Message;


class Mailer
{
    /**
     * The Swift Mailer instance.
     *
     * @var \Swift_Mailer
     */
    protected $swift;

    /**
     * The event dispatcher instance.
     *
     * @var \Events\Dispatcher
     */
    protected $events;

    /**
     * The global from address and name.
     *
     * @var array
     */
    protected $from;

    /**
     * Indicates if the actual sending is disabled.
     *
     * @var bool
     */
    protected $pretending = false;

    /**
     * Array of failed recipients.
     *
     * @var array
     */
    protected $failedRecipients = array();

    /**
     * Create a new Mailer instance.
     *
     * @param  \View\Factory  $views
     * @param  \Swift_Mailer            $swift
     * @return void
     */
    public function __construct(Swift_Mailer $swift, Dispatcher $events = null)
    {
        $this->swift  = $swift;
        $this->events = $events;
    }

    /**
     * Send a new message when only a plain part.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  mixed   $callback
     * @return int
     */
    public function plain($view, array $data, $callback)
    {
        return $this->send(array('text' => $view), $data, $callback);
    }

    /**
     * Set the global from address and name.
     *
     * @param  string  $address
     * @param  string  $name
     * @return void
     */
    public function alwaysFrom($address, $name = null)
    {
        $this->from = compact('address', 'name');
    }

    /**
     * Send a new message using a view.
     *
     * @param  string|array  $view
     * @param  array          $data
     * @param  Closure           $callback
     * @return void
     */
    public function send($view, array $data, Closure $callback)
    {
        list($view, $plain) = $this->parseView($view);

        $data['message'] = $message = $this->createMessage();

        $this->callMessageBuilder($callback, $message);

        $this->addContent($message, $view, $plain, $data);

        $message = $message->getSwiftMessage();

        $this->sendSwiftMessage($message);
    }

    /**
     * Add the content to a given message.
     *
     * @param  \Mail\Message  $message
     * @param  string                   $view
     * @param  string                    $plain
     * @param  array                     $data
     * @return void
     */
    protected function addContent($message, $view, $plain, $data)
    {
        if (isset($view)) {
            $message->setBody($this->getView($view, $data), 'text/html');
        }

        if (isset($plain)) {
            $message->addPart($this->getView($plain, $data), 'text/plain');
        }
    }

    /**
     * Parse the given view name or array.
     *
     * @param  string|array  $view
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseView($view)
    {
        if (is_string($view)) return array($view, null);

        if (is_array($view) && isset($view[0])) {
            return $view;
        } elseif (is_array($view)) {
            return array(
                array_get($view, 'html'), array_get($view, 'text')
            );
        }

        throw new \InvalidArgumentException('Invalid view.');
    }

    /**
     * Send a Swift Message instance.
     *
     * @param  \Swift_Message  $message
     * @return void
     */
    protected function sendSwiftMessage($message)
    {
        if ($this->events) {
            $this->events->fire('mailer.sending', array($message));
        }

        if (! $this->pretending) {
            $this->swift->send($message, $this->failedRecipients);
        } else {
            $this->logMessage($message);
        }
    }

    /**
     * Call the provided message builder.
     *
     * @param  Closure  $callback
     * @param  \Mail\Message  $message
     * @return mixed
     */
    protected function callMessageBuilder(Closure $callback, $message)
    {
        return call_user_func($callback, $message);
    }

    /**
     * Create a new message instance.
     *
     * @return \Mail\Message
     */
    protected function createMessage()
    {
        $message = new Message(new Swift_Message);

        if (isset($this->from['address'])) {
            $message->from($this->from['address'], $this->from['name']);
        }

        return $message;
    }

    /**
     * Render the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @return string
     */
    protected function getView($view, $data)
    {
        return View::make($view, $data);
    }

    /**
     * Tell the mailer to not really send messages.
     *
     * @param  bool  $value
     * @return void
     */
    public function pretend($value = true)
    {
        $this->pretending = $value;
    }

    /**
     * Get the Swift Mailer instance.
     *
     * @return \Swift_Mailer
     */
    public function getSwiftMailer()
    {
        return $this->swift;
    }

    /**
     * Get the array of failed recipients.
     *
     * @return array
     */
    public function failures()
    {
        return $this->failedRecipients;
    }

    /**
     * Set the Swift Mailer instance.
     *
     * @param  \Swift_Mailer  $swift
     * @return void
     */
    public function setSwiftMailer($swift)
    {
        $this->swift = $swift;
    }

    /**
     * Log that a message was sent.
     *
     * @param  \Swift_Message  $message
     * @return void
     */
    protected function logMessage($message)
    {
        $filePath = str_replace('/', DS, APPDIR .'Storage/Logs/messages.log');

        // Prepare the content of the log.
        $emails = implode(', ', array_keys((array) $message->getTo()));

        $content = "Pretending to mail message to: {$emails}"
            .PHP_EOL
            .PHP_EOL
            .$message->toString()
            .PHP_EOL;

        // Append the text to the messages.log
        file_put_contents($filePath, $content, FILE_APPEND);
    }
}
