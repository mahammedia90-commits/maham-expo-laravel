<?php

namespace App\Jobs;

use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $leadData,
        private int $leadId
    ) {}

    public function handle(AiService $aiService): void
    {
        $score = $aiService->scoreLead($this->leadData);
        Log::info("Lead #{$this->leadId} scored: {$score}");
        // Update lead score in database
    }
}
