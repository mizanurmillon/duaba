<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\NotificationMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $from;

    protected $owner;

    protected $subject;

    protected $message;

    protected $actionText;

    protected $type; //Enums -> Notification Type

    protected $actionUrl;

    protected $channels; // for pass only email or store notification to database use ['database'], for both email and database

    /**
     * Create a new notification instance.
     */
    public function __construct(
        $from = 'duabad@example.com',
        $owner = 'Duaba',
        $subject = 'New Notification',
        $message = null,
        $actionText = 'Visit Now',
        $actionUrl = '#',
        $channels = ['database'],
        $type = 'success',
    ) {
        $this->from = $from;
        $this->owner = $owner;
        $this->subject = $subject;
        $this->message = $message;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->channels = $channels;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new NotificationMail(
            $this->from,
            $this->owner,
            $this->subject,
            $this->message,
            $this->actionText,
            url($this->actionUrl),
            $this->channels,
            $notifiable
        ))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'from' => $this->from,
            'owner' => $this->owner,
            'subject' => $this->subject,
            'message' => $this->message,
            'actionText' => $this->actionText,
            'actionUrl' => $this->actionUrl,
            'type' => $this->type,
        ];
    }
}
