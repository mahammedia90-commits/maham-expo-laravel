<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WaitlistController extends Controller
{
    public function index(): JsonResponse
    {
        $waitlist = [
            [
                'id' => 1,
                'user_id' => 5,
                'user_name' => 'Mohammed Hassan',
                'event_id' => 1,
                'event_name' => 'Maham Expo 2026',
                'space_id' => 10,
                'position' => 1,
                'status' => 'waiting',
                'created_at' => now()->subDays(5)->toIso8601String(),
            ],
            [
                'id' => 2,
                'user_id' => 8,
                'user_name' => 'Fatima Al-Dosari',
                'event_id' => 1,
                'event_name' => 'Maham Expo 2026',
                'space_id' => 10,
                'position' => 2,
                'status' => 'waiting',
                'created_at' => now()->subDays(3)->toIso8601String(),
            ],
        ];
        return response()->json(['data' => $waitlist]);
    }

    public function approve($waitlist): JsonResponse
    {
        return response()->json(['data' => ['id' => $waitlist, 'status' => 'approved']]);
    }

    public function reject($waitlist): JsonResponse
    {
        return response()->json(['data' => ['id' => $waitlist, 'status' => 'rejected']]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $items = $request->get('items', []);
        return response()->json(['data' => ['reordered' => count($items)]]);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => ['total' => 25, 'waiting' => 12, 'approved' => 10, 'rejected' => 3]]);
    }
}
