<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->header('X-User-Id', 0);
        $invoices = Invoice::where('userId', $userId)
            ->orderBy('createdAt', 'desc')
            ->paginate(15);
        return ApiResponse::paginated($invoices);
    }
    
    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(Invoice::findOrFail($id));
    }
}
