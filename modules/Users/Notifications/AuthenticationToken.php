<?php

namespace Modules\Users\Notifications;

use Nova\Bus\QueueableTrait;
use Nova\Notifications\Notification;
use Nova\Notifications\Messages\MailMessage;
use Nova\Queue\ShouldQueueInterface;


class AuthenticationToken extends Notification implements ShouldQueueInterface
{
    use QueueableTrait;

    /**
     * The login hash.
     *
     * @var string
     */
    public $hash;

    /**
     * The login timestamp.
     *
     * @var string
     */
    public $timestamp;

    /**
     * The login token.
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
            ->subject(__d('users', 'Authentication Token'))
            ->line(__d('users', 'You are receiving this email because we received an one-time login request for your account.'))
            ->action(__d('users', 'Login'), url('authorize', array($this->hash, $this->timestamp, $this->token)))
            ->line(__d('users', 'If you did not request an one-time login, no further action is required.'));
    }
}
