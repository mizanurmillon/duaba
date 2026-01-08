<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PaymentNotification extends Notification
{
    use Queueable;

    protected $subject;
    public string $message;
    public Payment $payment;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $subject, string $message, Payment $payment, string $type)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->payment = $payment;
        $this->type = $type;
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
            'payment' => $this->payment->id,
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
            'created_at' => $this->payment->created_at,
        ]);
    }
}
