<?php
namespace App\Listeners;
use App\Events\BookingApproved;
use App\Models\Notification;

class SendBookingApprovalNotification
{
    public function handle(BookingApproved $event): void
    {
        Notification::create([
            'user_id' => $event->request->user_id,
            'title' => 'تمت الموافقة على طلب الحجز',
            'title_en' => 'Booking Request Approved',
            'body' => 'تمت الموافقة على طلبك. يرجى مراجعة العقد والفاتورة.',
            'type' => 'booking_approved',
            'data' => json_encode(['rental_request_id' => $event->request->id]),
        ]);
    }
}
