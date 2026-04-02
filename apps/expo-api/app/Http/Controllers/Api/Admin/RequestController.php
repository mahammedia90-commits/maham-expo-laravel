<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = [
            [
                'id' => 1,
                'ref' => 'VR-2026-001',
                'type' => 'visit',
                'requester' => 'Ahmed Al-Rashid',
                'company' => 'Tech Solutions LLC',
                'status' => 'pending',
                'aiScore' => 85,
                'created_at' => now()->subDays(1)->toIso8601String(),
            ],
        ];
        return response()->json(['data' => $requests, 'meta' => ['current_page' => 1, 'per_page' => 20, 'total' => count($requests)]]);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'status' => 'pending']]);
    }

    public function approve($id): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'status' => 'approved']]);
    }

    public function reject(Request $request, $id): JsonResponse
    {
        return response()->json(['data' => ['id' => $id, 'status' => 'rejected']]);
    }
}
