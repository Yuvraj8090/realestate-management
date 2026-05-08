<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyModerationStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Property $property,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Property verification status updated')
            ->greeting("Status update for {$this->property->title}")
            ->line('New moderation status: '.str($this->property->moderation_status->value)->headline());

        if ($this->property->rejection_reason) {
            $message->line("Reason: {$this->property->rejection_reason}");
        }

        if ($this->property->moderation_notes) {
            $message->line("Notes: {$this->property->moderation_notes}");
        }

        return $message
            ->action('View property', route('properties.show', $this->property))
            ->line('You can review the listing and make any requested updates from your dashboard.');
    }
}
