<?php

namespace Shared\Notifications\Messages;

use Shared\Notifications\Messages\SimpleMessage;


class MailMessage extends SimpleMessage
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
}
