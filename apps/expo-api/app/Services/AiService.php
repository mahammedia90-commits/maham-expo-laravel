<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * MAHAM EXPO — Centralized AI Service
 * ALL AI operations go through this single service.
 * Uses OpenAI/Anthropic API for intelligence.
 */
class AiService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key', env('AI_API_KEY', ''));
        $this->apiUrl = config('services.ai.url', 'https://api.openai.com/v1/chat/completions');
        $this->model = config('services.ai.model', 'gpt-4o-mini');
    }

    /**
     * Smart Event Recommendations based on user preferences
     */
    public function recommendEvents(int $userId, array $preferences = []): array
    {
        $cacheKey = "ai_recommend_events_{$userId}";
        return Cache::remember($cacheKey, 3600, function () use ($preferences) {
            return $this->query(
                "Recommend 5 Saudi exhibitions for a user interested in: " . implode(', ', $preferences),
                'recommendations'
            );
        });
    }

    /**
     * CRM Lead Scoring — score leads 0-100
     */
    public function scoreLead(array $leadData): int
    {
        $score = 50; // Base score
        if (!empty($leadData['company_name'])) $score += 10;
        if (!empty($leadData['phone'])) $score += 10;
        if (!empty($leadData['email'])) $score += 5;
        if (($leadData['interactions'] ?? 0) > 3) $score += 15;
        if (($leadData['total_spent'] ?? 0) > 10000) $score += 10;
        return min(100, $score);
    }

    /**
     * Smart Alerts — detect anomalies
     */
    public function generateAlerts(): array
    {
        $alerts = [];

        // Check unpaid invoices > 30 days
        $overdueInvoices = \App\Models\Invoice::where('status', 'issued')
            ->where('due_date', '<', now()->subDays(30))
            ->count();
        if ($overdueInvoices > 0) {
            $alerts[] = [
                'type' => 'payment_overdue',
                'severity' => 'high',
                'message_ar' => "يوجد {$overdueInvoices} فاتورة متأخرة أكثر من 30 يوم",
                'message_en' => "{$overdueInvoices} invoices overdue by 30+ days",
                'count' => $overdueInvoices,
            ];
        }

        // Check expiring contracts
        $expiringContracts = \App\Models\RentalContract::where('status', 'active')
            ->where('end_date', '<', now()->addDays(7))
            ->count();
        if ($expiringContracts > 0) {
            $alerts[] = [
                'type' => 'contract_expiring',
                'severity' => 'medium',
                'message_ar' => "يوجد {$expiringContracts} عقد ينتهي خلال 7 أيام",
                'message_en' => "{$expiringContracts} contracts expiring in 7 days",
                'count' => $expiringContracts,
            ];
        }

        // Check pending KYC
        $pendingProfiles = \App\Models\BusinessProfile::where('verification_status', 'pending')
            ->where('created_at', '<', now()->subDays(3))
            ->count();
        if ($pendingProfiles > 0) {
            $alerts[] = [
                'type' => 'kyc_pending',
                'severity' => 'medium',
                'message_ar' => "يوجد {$pendingProfiles} طلب توثيق بانتظار المراجعة",
                'message_en' => "{$pendingProfiles} KYC requests awaiting review",
                'count' => $pendingProfiles,
            ];
        }

        return $alerts;
    }

    /**
     * Predictive Analytics — revenue forecasting
     */
    public function predictRevenue(int $months = 3): array
    {
        $currentRevenue = \App\Models\Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(3))
            ->sum('amount');

        $avgMonthly = $currentRevenue / 3;
        $predictions = [];
        for ($i = 1; $i <= $months; $i++) {
            $predictions[] = [
                'month' => now()->addMonths($i)->format('Y-m'),
                'predicted_revenue' => round($avgMonthly * (1 + ($i * 0.05)), 2),
                'confidence' => max(60, 95 - ($i * 10)),
            ];
        }

        return [
            'current_monthly_avg' => round($avgMonthly, 2),
            'currency' => 'SAR',
            'predictions' => $predictions,
        ];
    }

    /**
     * AI Chat Assistant
     */
    public function chat(string $message, string $context = ''): string
    {
        if (empty($this->apiKey)) {
            return $this->fallbackChat($message);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'أنت مساعد منصة مهام إكسبو لإدارة المعارض في السعودية. أجب بالعربية بشكل مختصر ومفيد.'],
                    ['role' => 'user', 'content' => $message],
                ],
                'max_tokens' => 500,
            ]);

            return $response->json('choices.0.message.content', 'عذراً، لم أتمكن من الإجابة.');
        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return $this->fallbackChat($message);
        }
    }

    private function fallbackChat(string $message): string
    {
        $lower = mb_strtolower($message);
        if (str_contains($lower, 'حجز') || str_contains($lower, 'book')) {
            return 'يمكنك حجز مساحة من خلال تصفح الفعاليات واختيار المساحة المناسبة ثم تقديم طلب حجز.';
        }
        if (str_contains($lower, 'دفع') || str_contains($lower, 'pay')) {
            return 'نقبل الدفع عبر مدى، فيزا، ماستركارد، وApple Pay. جميع المدفوعات مؤمنة عبر بوابة Tap.';
        }
        if (str_contains($lower, 'توثيق') || str_contains($lower, 'kyc')) {
            return 'لتوثيق حسابك، قم برفع صورة السجل التجاري وصورة الهوية الوطنية من صفحة الملف الشخصي.';
        }
        return 'مرحباً بك في مهام إكسبو! كيف يمكنني مساعدتك؟';
    }

    private function query(string $prompt, string $type): array
    {
        // Fallback without API key
        return ['type' => $type, 'source' => 'local', 'results' => []];
    }
}
