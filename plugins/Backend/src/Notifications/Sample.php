<?php

namespace AcmeCorp\Backend\Notifications;

use Notifications\Messages\MailMessage;
use Notifications\Notification;


class Sample extends Notification
{

    /**
     * Create a new Sample instance.
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
     * @return \Nova\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return with(new MailMessage)
            ->line('This is just a sample notification.')
            ->action('View your Dashboard', site_url('admin/dashboard'))
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
            'message'    => 'Just a sample notification.',
            'link'        => site_url('admin/dashboard'),
        );
    }
}
