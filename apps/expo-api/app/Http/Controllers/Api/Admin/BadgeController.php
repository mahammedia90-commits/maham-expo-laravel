<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BadgeController extends Controller
{
    public function index(): JsonResponse
    {
        $badges = [
            [
                'id' => 1,
                'name' => 'بطل الأداء',
                'name_en' => 'Performance Champion',
                'description' => 'Awarded to top performers',
                'icon' => 'trophy',
                'tier' => 'gold',
                'criteria' => 'Top 5% performance score',
                'holders_count' => 12,
                'auto_grant' => false,
                'active' => true,
                'created_at' => now()->subMonths(2)->toIso8601String(),
            ],
            [
                'id' => 2,
                'name' => 'الملك',
                'name_en' => 'Royal Member',
                'description' => 'VIP member status',
                'icon' => 'crown',
                'tier' => 'platinum',
                'criteria' => 'Spend over 500k',
                'holders_count' => 8,
                'auto_grant' => true,
                'active' => true,
                'created_at' => now()->subMonths(3)->toIso8601String(),
            ],
        ];
        return response()->json(['data' => $badges]);
    }

    public function store(Request $request): JsonResponse
    {
        $badge = $request->all();
        $badge['id'] = rand(100, 999);
        $badge['holders_count'] = 0;
        $badge['created_at'] = now()->toIso8601String();
        return response()->json(['data' => $badge], 201);
    }

    public function show($badge): JsonResponse
    {
        return response()->json(['data' => ['id' => $badge, 'name' => 'Badge Name']]);
    }

    public function update(Request $request, $badge): JsonResponse
    {
        return response()->json(['data' => ['id' => $badge, 'updated' => true]]);
    }

    public function destroy($badge): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function grant(Request $request): JsonResponse
    {
        return response()->json(['data' => ['granted' => true]]);
    }

    public function holders($badge): JsonResponse
    {
        $holders = [
            ['id' => 1, 'name' => 'Ahmed Al-Rashid', 'email' => 'ahmed@example.sa'],
            ['id' => 2, 'name' => 'Sara Mohammed', 'email' => 'sara@example.sa'],
        ];
        return response()->json(['data' => $holders]);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => ['total' => 15, 'active' => 12, 'holders' => 45]]);
    }
}
