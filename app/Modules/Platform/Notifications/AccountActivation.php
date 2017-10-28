<?php

namespace App\Modules\Platform\Notifications;

use Nova\Support\Facades\Config;

use Shared\Notifications\Notification;
use Shared\Notifications\Messages\MailMessage;


class AccountActivation extends Notification
{
    /**
     * The account activation hash.
     *
     * @var string
     */
    public $hash;

    /**
     * The account activation token.
     *
     * @var string
     */
    public $token;


    /**
     * Create a notification instance.
     *
     * @param  string   $hash
     * @param  integer  $timestamp
     * @param  string   $token
     * @return void
     */
    public function __construct($hash, $token)
    {
        $this->hash  = $hash;
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
            ->action(__d('platform', 'Activate your Account'), url('register', array($this->hash, $this->token)))
            ->line(__d('platform', 'If you did not made an account registration, no further action is required.'))
            ->queued();
    }
}
