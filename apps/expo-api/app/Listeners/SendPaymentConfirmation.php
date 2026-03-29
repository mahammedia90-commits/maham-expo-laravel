<?php
namespace App\Listeners;
use App\Events\PaymentCompleted;
use App\Models\Notification;

class SendPaymentConfirmation
{
    public function handle(PaymentCompleted $event): void
    {
        Notification::create([
            'user_id' => $event->payment->user_id,
            'title' => 'تم استلام الدفعة',
            'title_en' => 'Payment Received',
            'body' => "تم استلام مبلغ {$event->payment->amount} ر.س بنجاح",
            'type' => 'payment_completed',
            'data' => json_encode(['payment_id' => $event->payment->id]),
        ]);
    }
}
