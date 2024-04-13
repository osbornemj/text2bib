<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\ErrorReport;
use App\Models\User;

class ErrorReportCommentPosted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ErrorReport $errorReport, public User $user)
    {
        $this->errorReport = $errorReport;
        $this->user = $user;
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
        if ($this->user->id == $this->errorReport->output->conversion->user->id) {
            $message = 'A comment has been posted on an error report you submitted.  Please respond to the comment.';
        } else {
            $message = 'A comment has been posted on an error report.';
        }

        return (new MailMessage)
                    ->line($message)
                    ->action('View comment', url('/errorReport/' . $this->errorReport->id));
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
