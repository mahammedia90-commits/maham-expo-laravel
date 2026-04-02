<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OpportunityController extends Controller
{
    public function index(): JsonResponse
    {
        $opportunities = [
            [
                'id' => 1,
                'title' => 'Investment Round A',
                'description' => 'Series A funding opportunity',
                'type' => 'investment',
                'status' => 'open',
                'value' => 500000,
                'event_id' => 1,
                'assigned_to' => 'Ahmed Admin',
                'deadline' => now()->addMonths(2)->toIso8601String(),
                'created_at' => now()->subDays(5)->toIso8601String(),
            ],
            [
                'id' => 2,
                'title' => 'Premium Sponsorship Package',
                'description' => 'Exclusive sponsor partnership',
                'type' => 'sponsorship',
                'status' => 'in_review',
                'value' => 250000,
                'event_id' => 1,
                'assigned_to' => 'Sarah Admin',
                'deadline' => now()->addMonths(1)->toIso8601String(),
                'created_at' => now()->subDays(3)->toIso8601String(),
            ],
        ];
        return response()->json(['data' => $opportunities]);
    }

    public function store(Request $request): JsonResponse
    {
        $opp = $request->all();
        $opp['id'] = rand(100, 999);
        $opp['created_at'] = now()->toIso8601String();
        return response()->json(['data' => $opp], 201);
    }

    public function show($opportunity): JsonResponse
    {
        return response()->json(['data' => ['id' => $opportunity, 'status' => 'open']]);
    }

    public function update(Request $request, $opportunity): JsonResponse
    {
        return response()->json(['data' => ['id' => $opportunity, 'updated' => true]]);
    }

    public function destroy($opportunity): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => ['total' => 35, 'open' => 15, 'in_review' => 12, 'closed' => 8, 'total_value' => 5000000]]);
    }
}
