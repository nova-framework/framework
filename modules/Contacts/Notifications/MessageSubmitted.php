<?php

namespace Modules\Contacts\Notifications;

use Nova\Bus\QueueableTrait;
use Nova\Queue\ShouldQueueInterface;
use Nova\Support\Arr;
use Nova\Support\Str;

use Nova\Notifications\Messages\MailMessage;
use Nova\Notifications\Notification;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;


class MessageSubmitted extends Notification implements ShouldQueueInterface
{
    use QueueableTrait;

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

        $this->pdf = utf8_encode($pdf);
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
        $content = nl2br(
            e($this->message->content)
        );

        $message = with(new MailMessage)
            ->subject(__d('contacts', 'New message received via {0}', $this->contact->name))
            ->line(__d('contacts', 'A new message was received via {0}.', $this->contact->name))
            ->line(__d('contacts', '<b>Remote IP:</b> {0}', $this->message->remote_ip))
            ->line('')
            ->line('');

        $contact = $this->message->contact;

        $fields = $this->message->fields;

        foreach ($contact->fieldGroups as $group) {
            $items = $group->fieldItems->filter(function ($item)
            {
                return ($item->type != 'file');
            });

            if ($items->isEmpty()) {
                continue;
            }

            $message->line('<h2>' .$group->title .'</h2>')->line('<hr>');

            foreach ($items as $item) {
                $field = $fields->where('field_item_id', $item->id)->first();

                if (is_null($field)) {
                    $value = '-';
                } else {
                    $value = $field->getValueString();
                }

                if ($item->type == 'textarea') {
                    $value = nl2br($value);

                    $message->line('<b>' .$item->title .':</b>')->line(e($value))->line('');
                } else {
                    $message->line('<b>' .$item->title .':</b> ' .e($value));
                }
            }
        }

        $message->line('<hr>')
            ->action(__d('contacts', 'View the Message'), url('admin/contacts', array($this->contact->id, 'messages', $this->message->id)))
            ->line(__d('contacts', 'Thank you for using our application!'));

        // Attach the Request information as PDF file.
        if (isset($this->pdf)) {
            $fileName = sprintf('message-%06d-%s.pdf', $this->message->id, $this->message->created_at->format('Hi_dmY'));

            $message->attachData(utf8_decode($this->pdf), $fileName, array(
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
