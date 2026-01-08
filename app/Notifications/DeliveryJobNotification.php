<?php

namespace App\Notifications;

use App\Models\DeliveryJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class DeliveryJobNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subject;
    public string $message;
    public DeliveryJob $deliveryJob;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $message, string $subject, string $type, DeliveryJob $delivery)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->type = $type;
        $this->deliveryJob = $delivery;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'deliveryJob' => $this->deliveryJob->id,
            'subject' => $this->subject,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'subject' => $this->subject,
            'message' => $this->message,
            'type' => $this->type,
            'created_at' => $this->deliveryJob->created_at,
        ]);
    }
}
