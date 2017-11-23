<?php

namespace App\Modules\Contacts\Notifications;

use Nova\Support\Arr;
use Nova\Support\Str;

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
     * @var array
     */
    protected $labels;


    /**
     * Create a new MessageSubmitted instance.
     *
     * @return void
     */
    public function __construct(Message $message, Contact $contact, array $labels)
    {
        $this->message = $message;
        $this->contact = $contact;
        $this->labels  = $labels;
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
        $message = with(new MailMessage)
            ->subject(__d('contacts', 'New message received via {0}', $this->contact->name))
            ->line(__d('contacts', 'A new message was received via {0}.', $this->contact->name));

        foreach ($this->message->meta as $meta) {
            if (! Str::is('contact_*', $key = $meta->key) || ($key == 'contact_author_ip') || ($key == 'contact_path')) {
                continue;
            }

            $label = Arr::get($this->labels, str_replace('contact_', '', $key), __d('contacts', 'Unknown Label'));

            $value = nl2br(e($meta->value));

            if (strlen($value) < 50) {
                $message->line($label .': ' .$value);

                continue;
            }

            $message->line($label .':');
            $message->line($value);
            $message->line('');
        }

        return $message->action(__d('contacts', 'View the Message'), url('admin/contacts', array($this->contact->id, 'messages', $this->message->id)))
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
            'message' => __d('contacts', 'Contact Message received via {0}', $this->contact->name),
            'link'    => url('admin/contacts', array($this->contact->id, 'messages', $this->message->id)),
            'icon'    => 'envelope',
        );
    }
}
