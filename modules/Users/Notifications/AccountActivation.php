<?php

namespace Modules\Users\Notifications;

use Nova\Bus\QueueableTrait;
use Nova\Notifications\Notification;
use Nova\Notifications\Messages\MailMessage;
use Nova\Queue\ShouldQueueInterface;
use Nova\Support\Facades\Config;


class AccountActivation extends Notification implements ShouldQueueInterface
{
    use QueueableTrait;

    /**
     * The account activation hash.
     *
     * @var string
     */
    public $hash;

    /**
     * The activation timestamp.
     *
     * @var string
     */
    public $timestamp;

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
    public function __construct($hash, $timestamp, $token)
    {
        $this->hash      = $hash;
        $this->timestamp = $timestamp;
        $this->token     = $token;
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
            ->subject(__d('users', 'Account Activation'))
            ->line(__d('users', 'Thanks for creating an Account with the {0}.', Config::get('app.name')))
            ->action(__d('users', 'Activate your Account'), url('register', array($this->hash, $this->timestamp, $this->token)))
            ->line(__d('users', 'If you did not made an account registration, no further action is required.'));
    }
}
