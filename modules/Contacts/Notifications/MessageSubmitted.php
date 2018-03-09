<?php

namespace Modules\Contacts\Notifications;

use Nova\Support\Arr;
use Nova\Support\Str;

use Nova\Notification\Messages\MailMessage;
use Nova\Notification\Notification;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;


class MessageSubmitted extends Notification
{
    /**
     * @var \Modules\Contacts\Models\Message
     */
    protected $message;

    /**
     * @var \Modules\Contacts\Models\Contact
     */
    protected $contact;

    /**
     * The the rendered PDF content.
     *
     * @var mixed
     */
    protected $pdf;


    /**
     * Create a new MessageSubmitted instance.
     *
     * @return void
     */
    public function __construct(Message $message, Contact $contact, $pdf = null)
    {
        $this->message = $message;
        $this->contact = $contact;

        $this->pdf = $pdf;
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
     * @return \Nova\Notification\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $content = nl2br(
            e($this->message->content)
        );

        $message = with(new MailMessage)
            ->subject(__d('contacts', 'New message received via {0}', $this->contact->name))
            ->line(__d('contacts', 'A new message was received via {0}.', $this->contact->name))
            ->line('<hr>')
            ->line(__d('contacts', '<b>Name:</b> {0}', e($this->message->author)))
            ->line(__d('contacts', '<b>E-Mail Address:</b> {0}', e($this->message->author_email)))
            ->line(__d('contacts', '<b>Remote IP:</b> {0}', $this->message->author_ip))
            ->line(__d('contacts', '<b>Subject:</b> {0}', e($this->message->subject)))
            ->line('<b>' .__d('contacts', 'Message:') .'</b>')
            ->line($content)
            ->line('<hr>')
            ->action(__d('contacts', 'View the Message'), url('admin/contacts', array($this->contact->id, 'messages', $this->message->id)))
            ->line(__d('contacts', 'Thank you for using our application!'));

        // Attach the Request information as PDF file.
        if (isset($this->pdf)) {
            $fileName = sprintf('message-%06d-%s.pdf', $this->message->id, $this->message->created_at->format('Hi_dmY'));

            $message->attachData($this->pdf, $fileName, array(
                'mime' => 'application/pdf',
            ));
        }

        // Attach the Request's Attachments.
        foreach ($this->message->attachments as $attachment) {
            if (empty($path = $attachment->path)) {
                continue;
            }

            $message->attach($path, array(
                'as'   => $attachment->name,
                'mime' => $attachment->type,
            ));
        }

        return $message;
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
