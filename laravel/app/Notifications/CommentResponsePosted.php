<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Comment;

class CommentResponsePosted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public int $threadId, public int $commentId)
    {
        //$this->threadId = $threadId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('A response to a comment of yours has been posted.')
                    ->action('View comment', url('/threads/' . $this->threadId . '#' . $this->commentId));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
