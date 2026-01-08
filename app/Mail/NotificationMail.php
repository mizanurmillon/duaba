<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $fromAddress;

    public $owner;

    public $subjectText;

    public $messageContent;

    public $actionText;

    public $actionUrl;

    public $notifiable;

    /**
     * Create a new message instance.
     */
    public function __construct($from, $owner, $subject, $message, $actionText, $actionUrl, $notifiable)
    {
        $this->fromAddress = $from;
        $this->owner = $owner;
        $this->subjectText = $subject;
        $this->messageContent = $message;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->notifiable = $notifiable;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from($this->fromAddress, $this->owner)
            ->subject($this->subjectText)
            ->view('emails.notification')
            ->with([
                'messageContent' => $this->messageContent,
                'actionText' => $this->actionText,
                'actionUrl' => $this->actionUrl,
                'notifiable' => $this->notifiable,
            ]);
    }
}
