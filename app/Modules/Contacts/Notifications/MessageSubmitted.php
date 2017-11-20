<?php

namespace App\Modules\Contacts\Notifications;

use Shared\Notifications\Messages\MailMessage;
use Shared\Notifications\Notification;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Models\Message;


class MessageSubmitted extends Notification
{
    /**
     * @var \App\Modules\Contacts\Models\Message
     */
    protected $message;

    /**
     * @var \App\Modules\Contacts\Models\Post
     */
    protected $contact;


    /**
     * Create a new MessageSubmitted instance.
     *
     * @return void
     */
    public function __construct(Message $message, Contact $contact)
    {
        $this->message = $message;
        $this->contact    = $contact;
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
            ->subject(__d('contacts', 'New Message received via {0}', $this->contact->name))
            ->line(__d('contacts', 'A new Message was received via {0}', $this->contact->name))
            ->line(__d('contacts', 'Author: {0}', e($this->message->contact_author)))
            ->line(__d('contacts', 'Author E-mail: {0}', e($this->message->contact_author_email)))
            ->line(__d('contacts', 'Message: {0}', e($this->message->contact_message)))
            ->action(__d('contacts', 'View the Contact Messages'), url('admin/contacts/', $this->contact->id))
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
            'message' => __d('contacts', 'Contact Message recieved via {0}', $this->contact->name),
            'link'    => url('admin/contacts/', $this->contact->id),
            'icon'    => 'message',
        );
    }
}
