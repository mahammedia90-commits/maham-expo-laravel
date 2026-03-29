<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Section;
use App\Models\Space;
use Illuminate\Support\Facades\Cache;

class EventService
{
    public function listEvents(array $filters = [])
    {
        $query = Event::with(['venue', 'sections'])
            ->withCount(['spaces', 'spaces as available_spaces_count' => function ($q) {
                $q->where('status', 'available');
            }]);

        if (!empty($filters['category'])) $query->where('category', $filters['category']);
        if (!empty($filters['city_id'])) $query->where('city_id', $filters['city_id']);
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['featured'])) $query->where('is_featured', true);
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('name_ar', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('start_date', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    public function getFeatured(): \Illuminate\Support\Collection
    {
        return Cache::remember('featured_events', 3600, function () {
            return Event::where('is_featured', true)
                ->where('status', 'active')
                ->with('venue')
                ->orderBy('start_date')
                ->limit(10)
                ->get();
        });
    }

    public function getEventWithDetails(int $id): Event
    {
        return Event::with(['venue', 'sections.spaces', 'sponsorPackages'])
            ->withCount(['spaces', 'spaces as available_spaces_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->findOrFail($id);
    }
}
