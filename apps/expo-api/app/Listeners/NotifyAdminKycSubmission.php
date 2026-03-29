<?php
namespace App\Listeners;
use App\Events\KycSubmitted;
use App\Models\Notification;
use App\Models\User;

class NotifyAdminKycSubmission
{
    public function handle(KycSubmitted $event): void
    {
        // Notify all admins
        $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'طلب توثيق جديد',
                'title_en' => 'New KYC Submission',
                'body' => "طلب توثيق جديد من {$event->profile->company_name}",
                'type' => 'kyc_submitted',
            ]);
        }
    }
}
