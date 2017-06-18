<?php

namespace Notifications\Messages;

use Notifications\Messages\SimpleMessage;


class MailMessage extends SimpleMessage
{
	/**
	 * Indicate that the message should be queued.
	 *
	 * @var string
	 */
	public $queued = false;

	/**
	 * The view for the message.
	 *
	 * @var string
	 */
	public $view = 'Notifications::Email';

	/**
	 * The view data for the message.
	 *
	 * @var array
	 */
	public $viewData = array();

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
	 * Mark the message as to be queued.
	 *
	 * @param  string  $file
	 * @param  array  $options
	 * @return $this
	 */
	public function queued()
	{
		$this->queued = true;

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
