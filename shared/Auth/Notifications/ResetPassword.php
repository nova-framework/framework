<?php

namespace Shared\Auth\Notifications;

use Shared\Notifications\Notification;
use Shared\Notifications\Messages\MailMessage;


class ResetPassword extends Notification
{
    /**
     * The password reset hash.
     *
     * @var string
     */
    public $hash;

    /**
     * The password reset timestamp.
     *
     * @var string
     */
    public $timestamp;

    /**
     * The password reset token.
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
            ->subject(__d('shared', 'Reset Password'))
            ->line(__d('shared', 'You are receiving this email because we received a password reset request for your account.'))
            ->action(__d('shared', 'Reset Password'), url('password/reset', array($this->hash, $this->timestamp, $this->token)))
            ->line(__d('shared', 'If you did not request a password reset, no further action is required.'))
            ->queued();
    }
}
