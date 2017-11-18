<?php

namespace App\Modules\Content\Notifications;

use Shared\Notifications\Messages\MailMessage;
use Shared\Notifications\Notification;


class CommentSubmitted extends Notification
{

    /**
     * Create a new CommentSubmitted instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return array('mail', 'database');
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Shared\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return with(new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', 'https://www.novaframework.com')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return array(
            //
        );
    }
}
