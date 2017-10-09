<?php

namespace Shared\Notifications\Messages;

class MailMessage
{
    /**
     * The view for the message.
     *
     * @var array
     */
    public $view = array(
        'Emails/Notifications/Default',
        'Emails/Notifications/Plain',
    );

    /**
     * The view data for the message.
     *
     * @var array
     */
    public $viewData = array();

    /**
     * The "from" information for the message.
     *
     * @var array
     */
    public $from = array();

    /**
     * The recipient information for the message.
     *
     * @var array
     */
    public $to = array();

    /**
     * The "cc" recipients of the message.
     *
     * @var array
     */
    public $cc = array();

    /**
     * The "reply to" information for the message.
     *
     * @var array
     */
    public $replyTo = array();

    /**
     * The attachments for the message.
     *
     * @var array
     */
    public $attachments = array();

    /**
     * The raw attachments for the message.
     *
     * @var array
     */
    public $rawAttachments = array();

    /**
     * Priority level of the message.
     *
     * @var int
     */
    public $priority;

    /**
     * Whether or not this message should be queued.
     *
     * @var bool
     */
    public $queued = false;

    /**
     * The "level" of the notification (info, success, error).
     *
     * @var string
     */
    public $level = 'info';

    /**
     * The subject of the notification.
     *
     * @var string
     */
    public $subject;

    /**
     * The notification's greeting.
     *
     * @var string
     */
    public $greeting;

    /**
     * The "intro" lines of the notification.
     *
     * @var array
     */
    public $introLines = array();

    /**
     * The "outro" lines of the notification.
     *
     * @var array
     */
    public $outroLines = array();

    /**
     * The text / label for the action.
     *
     * @var string
     */
    public $actionText;

    /**
     * The action URL.
     *
     * @var string
     */
    public $actionUrl;


    /**
     * Set the view for the mail message.
     *
     * @param  string  $view
     * @param  array  $data
     * @return $this
     */
    public function view($view, array $data = array())
    {
        $this->view = $view;

        $this->viewData = $data;

        return $this;
    }

    /**
     * Set the from address for the mail message.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return $this
     */
    public function from($address, $name = null)
    {
        $this->from = array($address, $name);

        return $this;
    }

    /**
     * Set the recipient address for the mail message.
     *
     * @param  string|array  $address
     * @return $this
     */
    public function to($address)
    {
        $this->to = $address;

        return $this;
    }

    /**
     * Set the recipients of the message.
     *
     * @param  string|array  $address
     * @return $this
     */
    public function cc($address)
    {
        $this->cc = $address;

        return $this;
    }

    /**
     * Set the "reply to" address of the message.
     *
     * @param  array|string $address
     * @param null $name
     * @return $this
     */
    public function replyTo($address, $name = null)
    {
        $this->replyTo = array($address, $name);

        return $this;
    }

    /**
     * Attach a file to the message.
     *
     * @param  string  $file
     * @param  array  $options
     * @return $this
     */
    public function attach($file, array $options = array())
    {
        $this->attachments[] = compact('file', 'options');

        return $this;
    }

    /**
     * Attach in-memory data as an attachment.
     *
     * @param  string  $data
     * @param  string  $name
     * @param  array  $options
     * @return $this
     */
    public function attachData($data, $name, array $options = array())
    {
        $this->rawAttachments[] = compact('data', 'name', 'options');

        return $this;
    }

    /**
     * Set the priority of this message.
     *
     * The value is an integer where 1 is the highest priority and 5 is the lowest.
     *
     * @param  int  $level
     * @return $this
     */
    public function priority($level)
    {
        $this->priority = $level;

        return $this;
    }

    /**
     * Set whether or not this message should be queued.
     *
     * @param  bool  $queued
     * @return $this
     */
    public function queued($queued = true)
    {
        $this->queued = $queued;

        return $this;
    }

    /**
     * Get the data array for the mail message.
     *
     * @return array
     */
    public function data()
    {
        return array_merge($this->toArray(), $this->viewData);
    }

    /**
     * Indicate that the notification gives information about a successful operation.
     *
     * @return $this
     */
    public function success()
    {
        $this->level = 'success';

        return $this;
    }

    /**
     * Indicate that the notification gives information about an error.
     *
     * @return $this
     */
    public function error()
    {
        $this->level = 'error';

        return $this;
    }

    /**
     * Set the "level" of the notification (success, error, etc.).
     *
     * @param  string  $level
     * @return $this
     */
    public function level($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Set the subject of the notification.
     *
     * @param  string  $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the greeting of the notification.
     *
     * @param  string  $greeting
     * @return $this
     */
    public function greeting($greeting)
    {
        $this->greeting = $greeting;

        return $this;
    }

    /**
     * Add a line of text to the notification.
     *
     * @param  \Notifications\Action|string  $line
     * @return $this
     */
    public function line($line)
    {
        return $this->with($line);
    }

    /**
     * Add a line of text to the notification.
     *
     * @param  \Notifications\Action|string|array  $line
     * @return $this
     */
    public function with($line)
    {
        if ($line instanceof Action) {
            $this->action($line->text, $line->url);
        } else if (is_null($this->actionText)) {
            $this->introLines[] = $this->formatLine($line);
        } else {
            $this->outroLines[] = $this->formatLine($line);
        }

        return $this;
    }

    /**
     * Format the given line of text.
     *
     * @param  string|array  $line
     * @return string
     */
    protected function formatLine($line)
    {
        if (is_array($line)) {
            return implode(' ', array_map('trim', $line));
        }

        $lines = preg_split('/\\r\\n|\\r|\\n/', $line);

        return trim(implode(' ', array_map('trim', $lines)));
    }

    /**
     * Configure the "call to action" button.
     *
     * @param  string  $text
     * @param  string  $url
     * @return $this
     */
    public function action($text, $url)
    {
        $this->actionText = $text;

        $this->actionUrl = $url;

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'level'      => $this->level,
            'subject'    => $this->subject,
            'greeting'   => $this->greeting,
            'introLines' => $this->introLines,
            'outroLines' => $this->outroLines,
            'actionText' => $this->actionText,
            'actionUrl'  => $this->actionUrl,
        );
    }
}
