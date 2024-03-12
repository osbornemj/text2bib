<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\ErrorReport;

class ErrorReportCommentPosted extends Notification
{
    use Queueable;

    public $errorReport;

    /**
     * Create a new notification instance.
     */
    public function __construct(ErrorReport $errorReport)
    {
        $this->errorReport = $errorReport;
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
                    ->line('A comment has been posted on an error report you submitted.  Please respond to the comment.')
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
