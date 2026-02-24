<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorExposureTracking;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExposureController extends Controller
{
    /**
     * List exposure data for sponsor
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $query = SponsorExposureTracking::whereIn('sponsor_id', $sponsorIds)
            ->with(['event']);

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($channel = $request->input('channel')) {
            $query->forChannel($channel);
        }

        // Date range
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $query->inDateRange($startDate, $endDate);

        $data = $query->orderBy('date', 'desc')->get();

        return ApiResponse::success($data);
    }

    /**
     * Exposure summary with aggregated stats
     */
    public function summary(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // By channel
        $byChannel = SponsorExposureTracking::whereIn('sponsor_id', $sponsorIds)
            ->inDateRange($startDate, $endDate)
            ->selectRaw('channel, SUM(impressions_count) as total_impressions, SUM(clicks_count) as total_clicks')
            ->groupBy('channel')
            ->get()
            ->map(function ($item) {
                return [
                    'channel' => $item->channel,
                    'channel_label' => $item->channel_label,
                    'total_impressions' => (int) $item->total_impressions,
                    'total_clicks' => (int) $item->total_clicks,
                    'ctr' => $item->total_impressions > 0
                        ? round(($item->total_clicks / $item->total_impressions) * 100, 2)
                        : 0,
                ];
            });

        // By date (daily trend)
        $byDate = SponsorExposureTracking::whereIn('sponsor_id', $sponsorIds)
            ->inDateRange($startDate, $endDate)
            ->selectRaw('date, SUM(impressions_count) as total_impressions, SUM(clicks_count) as total_clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // By event
        $byEvent = SponsorExposureTracking::whereIn('sponsor_id', $sponsorIds)
            ->inDateRange($startDate, $endDate)
            ->with('event:id,name,name_ar')
            ->selectRaw('event_id, SUM(impressions_count) as total_impressions, SUM(clicks_count) as total_clicks')
            ->groupBy('event_id')
            ->get();

        // Totals
        $totals = [
            'total_impressions' => $byChannel->sum('total_impressions'),
            'total_clicks' => $byChannel->sum('total_clicks'),
            'ctr' => $byChannel->sum('total_impressions') > 0
                ? round(($byChannel->sum('total_clicks') / $byChannel->sum('total_impressions')) * 100, 2)
                : 0,
        ];

        return ApiResponse::success([
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'totals' => $totals,
            'by_channel' => $byChannel,
            'by_date' => $byDate,
            'by_event' => $byEvent,
        ]);
    }
}
