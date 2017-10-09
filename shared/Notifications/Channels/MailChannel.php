<?php

namespace Shared\Notifications\Channels;

use Nova\Mail\Mailer;
use Nova\Support\Str;

use Shared\Notifications\Notification;


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
        if (! $notifiable->routeNotificationFor('mail')) {
            return;
        }

        $mail = $notification->toMail($notifiable);

        $parameters = array($mail->view, $mail->data(), function ($message) use ($notifiable, $notification, $mail)
        {
            $recipients = empty($mail->to) ? $notifiable->routeNotificationFor('mail') : $mail->to;

            if (! empty($mail->from)) {
                $message->from($mail->from[0], isset($mail->from[1]) ? $mail->from[1] : null);
            }

            if (is_array($recipients)) {
                $message->bcc($recipients);
            } else {
                $message->to($recipients);
            }

            if (! empty($mail->cc)) {
                $message->cc($mail->cc);
            }

            if (! empty($mail->replyTo)) {
                $message->replyTo($mail->replyTo[0], isset($mail->replyTo[1]) ? $mail->replyTo[1] : null);
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

            if (! is_null($mail->priority)) {
                $message->setPriority($mail->priority);
            }
        });

        $method = $mail->queued ? 'queue' : 'send';

        call_user_func_array(array($this->mailer, $method), $parameters);
    }
}
