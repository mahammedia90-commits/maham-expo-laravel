<?php

namespace App\Jobs;

use App\Services\AiService;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSmartAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AiService $aiService): void
    {
        $alerts = $aiService->generateAlerts();
        foreach ($alerts as $alert) {
            // Notify all admins
            $adminIds = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))
                ->pluck('id');
            foreach ($adminIds as $adminId) {
                Notification::create([
                    'user_id' => $adminId,
                    'title' => $alert['message_ar'],
                    'title_en' => $alert['message_en'],
                    'body' => $alert['message_ar'],
                    'type' => 'smart_alert_' . $alert['type'],
                    'data' => json_encode($alert),
                ]);
            }
        }
    }
}
