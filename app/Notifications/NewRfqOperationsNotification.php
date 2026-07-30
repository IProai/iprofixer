<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NewRfqOperationsNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly FormSubmission $submission) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New RFQ {$this->submission->reference}")
            ->line("Contact: {$this->submission->contact_name}")
            ->line('Organisation: '.($this->submission->organization_name ?: 'Not provided'))
            ->line('Service: '.($this->submission->service_code ?: 'Not selected'))
            ->line('Urgency: '.($this->submission->urgency ?: 'Not selected'))
            ->line("Attachments: {$this->submission->attachments()->count()}")
            ->action('Review RFQ', route('admin.rfqs.show', $this->submission));
    }
}
