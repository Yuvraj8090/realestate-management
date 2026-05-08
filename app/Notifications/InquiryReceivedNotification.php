<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Inquiry $inquiry,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New property inquiry received')
            ->greeting('A new inquiry has arrived')
            ->line("Property: {$this->inquiry->property->title}")
            ->line("From: {$this->inquiry->name} ({$this->inquiry->email})")
            ->line("Preferred contact: {$this->inquiry->preferred_contact_method->value}")
            ->line($this->inquiry->message)
            ->action('View lead dashboard', route('leads.index'))
            ->line('Respond quickly to improve conversion chances.');
    }
}
