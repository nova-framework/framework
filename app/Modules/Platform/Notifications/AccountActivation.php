<?php

namespace App\Modules\Platform\Notifications;

use Shared\Notifications\Notification;
use Shared\Notifications\Messages\MailMessage;


class AccountActivation extends Notification
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
            ->subject(__d('platform', 'Account Activation'))
            ->line(__d('platform', 'Thanks for creating an Account with the {0}.', Config::get('app.name')))
            ->action(__d('platform', 'Activate Account'), site_url('register/verify/', $this->token))
            ->queued();
    }
}
