<?php

namespace App\Modules\Platform\Notifications;

use Shared\Notifications\Messages\MailMessage;
use Shared\Notifications\Notification;


class Sample extends Notification
{

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
            ->subject('A sample notification')
            ->line('This is just a sample notification.')
            ->action('View your Dashboard', site_url('dashboard'))
            ->line('Thank you for using our application!')
            ->queued();
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
            'message' => 'Just a sample notification.',
            'link'    => site_url('dashboard'),
            'icon'    => 'globe',
        );
    }
}
