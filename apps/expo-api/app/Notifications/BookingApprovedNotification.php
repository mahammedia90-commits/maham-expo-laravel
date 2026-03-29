<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(private int $requestId) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_approved',
            'rental_request_id' => $this->requestId,
            'message_ar' => 'تمت الموافقة على طلب الحجز',
            'message_en' => 'Booking request approved',
        ];
    }
}
