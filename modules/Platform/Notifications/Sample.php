<?php

namespace Modules\Platform\Notifications;

use Nova\Bus\QueueableTrait;
use Nova\Queue\ShouldQueueInterface;

use Nova\Notifications\Messages\MailMessage;
use Nova\Notifications\Notification;


class Sample extends Notification implements ShouldQueueInterface
{
    use QueueableTrait;


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
            ->subject('A sample notification')
            ->line('This is just a sample notification.')
            ->action('View your Dashboard', site_url('dashboard'))
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
            'message' => 'Just a sample notification.',
            'link'    => site_url('dashboard'),
            'icon'    => 'globe',
        );
    }
}
