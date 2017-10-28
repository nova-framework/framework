<?php

namespace App\Modules\Platform\Notifications;

use Shared\Notifications\Notification;
use Shared\Notifications\Messages\MailMessage;


class AuthenticationToken extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;


    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return array('mail');
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__d('centers', 'Authentication Token'))
            ->line(__d('centers', 'You are receiving this email because we received an one-time login request for your account.'))
            ->action(__d('centers', 'Login'), url('authorize', $this->token))
            ->line(__d('centers', 'If you did not request an one-time login, no further action is required.'))
            ->queued();
    }
}
