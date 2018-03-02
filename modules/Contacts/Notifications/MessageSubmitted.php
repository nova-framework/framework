<?php

namespace Modules\Contacts\Notifications;

use Nova\Support\Arr;
use Nova\Support\Str;

use Nova\Notifications\Messages\MailMessage;
use Nova\Notifications\Notification;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;


class MessageSubmitted extends Notification
{
    /**
     * @var \Modules\Contacts\Models\Message
     */
    protected $message;

    /**
     * @var \Modules\Contacts\Models\Post
     */
    protected $contact;

    /**
     * @var array
     */
    protected $elements;


    /**
     * Create a new MessageSubmitted instance.
     *
     * @return void
     */
    public function __construct(Message $message, Contact $contact, array $elements)
    {
        $this->message  = $message;
        $this->contact  = $contact;
        $this->elements = $elements;
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
        $message = with(new MailMessage)
            ->subject(__d('contacts', 'New message received via {0}', $this->contact->name))
            ->line(__d('contacts', 'A new message was received via {0}.', $this->contact->name));

        foreach ($this->message->meta as $meta) {
            if (! Str::is('contact_*', $name = $meta->key) || ($name == 'contact_author_ip') || ($name == 'contact_path')) {
                continue;
            }

            $value = $meta->value;

            if ('select' == Arr::get($this->elements, $name .'.type')) {
                $value = Arr::get($this->elements, $value, $value);
            }

            $label = Arr::get($this->elements, $name .'.label', __d('contacts', 'Unknown'));

            $value = nl2br(e($value));

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
