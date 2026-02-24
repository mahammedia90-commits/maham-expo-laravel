<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    /**
     * جلب تفضيلات الإشعارات الخاصة بالمستخدم
     * GET /notification-preferences
     */
    public function show(Request $request): JsonResponse
    {
        $preferences = NotificationPreference::getOrCreateForUser($request->user()->id);

        return ApiResponse::success($preferences, __('messages.notification.preferences_updated'));
    }

    /**
     * تحديث تفضيلات الإشعارات
     * PUT /notification-preferences
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'email_enabled'               => 'boolean',
            'push_enabled'                => 'boolean',
            'in_app_enabled'              => 'boolean',
            'notify_request_updates'      => 'boolean',
            'notify_payment_reminders'    => 'boolean',
            'notify_event_updates'        => 'boolean',
            'notify_contract_milestones'  => 'boolean',
            'notify_promotions'           => 'boolean',
            'notify_support_updates'      => 'boolean',
            'notify_ratings'              => 'boolean',
        ]);

        $preferences = NotificationPreference::getOrCreateForUser($request->user()->id);

        $preferences->update($request->only([
            'email_enabled', 'push_enabled', 'in_app_enabled',
            'notify_request_updates', 'notify_payment_reminders',
            'notify_event_updates', 'notify_contract_milestones',
            'notify_promotions', 'notify_support_updates', 'notify_ratings',
        ]));

        return ApiResponse::success($preferences, __('messages.notification.preferences_updated'));
    }
}
