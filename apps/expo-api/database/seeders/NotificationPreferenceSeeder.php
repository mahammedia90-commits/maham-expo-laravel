<?php

namespace Database\Seeders;

use App\Models\NotificationPreference;
use Illuminate\Database\Seeder;

class NotificationPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $preferences = [
            // User 10 (TechVentures - active investor): All notifications enabled
            [
                'user_id' => '00000000-0000-0000-0000-000000000010',
                'email_enabled' => true,
                'push_enabled' => true,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => true,
                'notify_support_updates' => true,
                'notify_ratings' => true,
            ],
            // User 11 (Al-Salam Trading - merchant): Disabled promotions and ratings
            [
                'user_id' => '00000000-0000-0000-0000-000000000011',
                'email_enabled' => true,
                'push_enabled' => true,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => false,
                'notify_support_updates' => true,
                'notify_ratings' => false,
            ],
            // User 12 (Fresh Foods - pending): Default settings
            [
                'user_id' => '00000000-0000-0000-0000-000000000012',
                'email_enabled' => true,
                'push_enabled' => true,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => true,
                'notify_support_updates' => true,
                'notify_ratings' => true,
            ],
            // User 13 (Quick Invest - rejected): Email only, minimal notifications
            [
                'user_id' => '00000000-0000-0000-0000-000000000013',
                'email_enabled' => true,
                'push_enabled' => false,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => false,
                'notify_contract_milestones' => false,
                'notify_promotions' => false,
                'notify_support_updates' => true,
                'notify_ratings' => false,
            ],
            // User 14 (Beauty World - merchant): Push disabled
            [
                'user_id' => '00000000-0000-0000-0000-000000000014',
                'email_enabled' => true,
                'push_enabled' => false,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => true,
                'notify_support_updates' => true,
                'notify_ratings' => true,
            ],
            // User 15 (Gulf Properties - pending investor): All enabled
            [
                'user_id' => '00000000-0000-0000-0000-000000000015',
                'email_enabled' => true,
                'push_enabled' => true,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => true,
                'notify_support_updates' => true,
                'notify_ratings' => true,
            ],
            // Admin/Reviewer user
            [
                'user_id' => '00000000-0000-0000-0000-000000000099',
                'email_enabled' => true,
                'push_enabled' => true,
                'in_app_enabled' => true,
                'notify_request_updates' => true,
                'notify_payment_reminders' => true,
                'notify_event_updates' => true,
                'notify_contract_milestones' => true,
                'notify_promotions' => false,
                'notify_support_updates' => true,
                'notify_ratings' => true,
            ],
        ];

        if (NotificationPreference::count() > 0) {
            $this->command->info('Notification preferences already seeded, skipping.');
            return;
        }

        foreach ($preferences as $preference) {
            NotificationPreference::create($preference);
        }

        $this->command->info('Created ' . count($preferences) . ' notification preferences.');
    }
}
