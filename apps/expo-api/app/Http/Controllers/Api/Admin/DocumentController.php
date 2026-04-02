<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = [
            [
                'id' => 1,
                'name' => 'Contract Agreement',
                'type' => 'contract',
                'file_path' => '/uploads/contract_001.pdf',
                'file_size' => 2048,
                'mime_type' => 'application/pdf',
                'status' => 'approved',
                'uploaded_by' => 'Ahmed Admin',
                'related_id' => 5,
                'related_type' => 'sponsor',
                'created_at' => now()->subDays(2)->toIso8601String(),
            ],
            [
                'id' => 2,
                'name' => 'KYC Document',
                'type' => 'kyc',
                'file_path' => '/uploads/kyc_002.pdf',
                'file_size' => 1512,
                'mime_type' => 'application/pdf',
                'status' => 'pending',
                'uploaded_by' => 'Sarah Admin',
                'related_id' => 10,
                'related_type' => 'business_profile',
                'created_at' => now()->subDays(1)->toIso8601String(),
            ],
        ];
        return response()->json(['data' => $documents]);
    }

    public function store(Request $request): JsonResponse
    {
        $doc = [
            'id' => rand(100, 999),
            'name' => $request->get('name', 'Document'),
            'type' => $request->get('type'),
            'file_path' => '/uploads/doc_' . time() . '.pdf',
            'file_size' => 0,
            'mime_type' => 'application/pdf',
            'status' => 'pending',
            'uploaded_by' => 'Current User',
            'created_at' => now()->toIso8601String(),
        ];
        return response()->json(['data' => $doc], 201);
    }

    public function show($document): JsonResponse
    {
        return response()->json(['data' => ['id' => $document]]);
    }

    public function updateStatus(Request $request, $document): JsonResponse
    {
        $status = $request->get('status');
        return response()->json(['data' => ['id' => $document, 'status' => $status]]);
    }

    public function destroy($document): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function export(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'download_url' => 'https://api.mahamexpo.sa/exports/documents_' . date('Y-m-d') . '.csv',
            'file_name' => 'documents_' . date('Y-m-d') . '.csv',
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => ['total' => 45, 'pending' => 12, 'approved' => 30, 'rejected' => 3]]);
    }
}
