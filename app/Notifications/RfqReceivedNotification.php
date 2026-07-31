<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class RfqReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $reference,
        private readonly string $contactName,
        private readonly string $requestedLocale,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->requestedLocale === 'ar') {
            return (new MailMessage)
                ->subject("تم استلام طلب التقييم {$this->reference}")
                ->greeting("مرحباً {$this->contactName}")
                ->line('تم استلام طلب التقييم الخاص بكم بنجاح.')
                ->line("الرقم المرجعي: {$this->reference}")
                ->line('سيقوم فريقنا بمراجعة المعلومات والتواصل معكم بشأن الخطوة التالية.');
        }

        return (new MailMessage)
            ->subject("Assessment request received {$this->reference}")
            ->greeting("Hello {$this->contactName}")
            ->line('Your assessment request has been received successfully.')
            ->line("Reference: {$this->reference}")
            ->line('Our team will review the information and contact you about the next step.');
    }
}
