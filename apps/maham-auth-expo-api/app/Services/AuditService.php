<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Str;

class AuditService
{
    /**
     * تسجيل حدث في سجل التدقيق
     */
    public function log(
        string $action,
        ?User $user = null,
        array $data = []
    ): ?AuditLog {
        // التحقق من تفعيل التدقيق
        if (!config('auth-service.audit.enabled', true)) {
            return null;
        }

        // التحقق من أن الحدث مسموح بتسجيله
        $allowedEvents = config('auth-service.audit.events', []);
        if (!empty($allowedEvents) && !in_array($action, $allowedEvents)) {
            return null;
        }

        return AuditLog::create([
            'id' => Str::uuid(),
            'user_id' => $user?->id,
            'target_user_id' => $data['target_user_id'] ?? null,
            'action' => $action,
            'ip_address' => $data['ip'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * الحصول على سجلات مستخدم
     */
    public function getUserLogs(
        string $userId,
        int $limit = 50,
        ?string $action = null
    ) {
        $query = AuditLog::where('user_id', $userId)
            ->orWhere('target_user_id', $userId);

        if ($action) {
            $query->where('action', $action);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على آخر السجلات
     */
    public function getRecentLogs(int $limit = 100, ?string $action = null)
    {
        $query = AuditLog::query();

        if ($action) {
            $query->where('action', $action);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * مسح السجلات القديمة
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        return AuditLog::where('created_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}
