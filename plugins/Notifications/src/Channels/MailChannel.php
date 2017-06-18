<?php

namespace Notifications\Channels;

use Nova\Mail\Mailer;
use Nova\Support\Str;

use Notifications\Notification;


class MailChannel
{
	/**
	 * The mailer implementation.
	 *
	 * @var \Nova\Mail\Mailer
	 */
	protected $mailer;


	/**
	 * Create a new Mail Channel instance.
	 *
	 * @param  \Nova\Mail\Mailer  $mailer
	 * @return void
	 */
	public function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * Send the given notification.
	 *
	 * @param  mixed  $notifiable
	 * @param  \Notifications\Notification  $notification
	 * @return void
	 */
	public function send($notifiable, Notification $notification)
	{
		if (is_null($recipient = $notifiable->routeNotificationFor('mail'))) {
			return;
		}

		$mail = $notification->toMail($notifiable);

		$parameters = array($mail->view, $mail->data(), function ($message) use ($notification, $recipient, $mail)
		{
			if (is_array($recipient)) {
				$message->bcc($recipient);
			} else {
				$message->to($recipient);
			}

			$message->subject($mail->subject ?: Str::title(
				Str::snake(class_basename($notification), ' ')
			));

			foreach ($mail->attachments as $attachment) {
				$message->attach($attachment['file'], $attachment['options']);
			}

			foreach ($mail->rawAttachments as $attachment) {
				$message->attachData($attachment['data'], $attachment['name'], $attachment['options']);
			}
		});

		$method = $mail->queued ? 'queue' : 'send';

		call_user_func_array(array($this->mailer, $method), $parameters);
	}
}
