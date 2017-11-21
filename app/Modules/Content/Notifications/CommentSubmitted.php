<?php

namespace App\Modules\Content\Notifications;

use Shared\Notifications\Messages\MailMessage;
use Shared\Notifications\Notification;

use App\Modules\Content\Models\Comment;
use App\Modules\Content\Models\Post;


class CommentSubmitted extends Notification
{
    /**
     * @var \App\Modules\Content\Models\Comment
     */
    protected $comment;

    /**
     * @var \App\Modules\Content\Models\Post
     */
    protected $post;


    /**
     * Create a new CommentSubmitted instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, Post $post)
    {
        $this->comment = $comment;
        $this->post    = $post;
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
            ->subject(__d('contacts', 'New comment submitted'))
            ->line(__d('content', 'Comment submitted in response to your post {0}', $this->post->title))
            ->line(__d('content', 'Author: {0}', e($this->comment->author)))
            ->line(__d('content', 'Author E-mail: {0}', e($this->comment->author_email)))
            ->line(__d('content', 'Message: {0}', nl2br(e($this->comment->content))))
            ->action(__d('content', 'View the post'), url('content', $this->post->name))
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
            'message' => __d('content', 'Comment submitted in response to <b>{0}</b>', $this->post->title),
            'link'    => url('content', $this->post->name),
            'icon'    => 'comment',
        );
    }
}
