<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->header('X-User-Id', 0);
        $tickets = SupportTicket::where('customerId', $userId)
            ->orderBy('createdAt', 'desc')
            ->paginate(15);
        return ApiResponse::paginated($tickets);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate(['subject' => 'required|string', 'description' => 'required|string']);
        $ticket = SupportTicket::create([
            'customerId' => $request->header('X-User-Id', 0),
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->input('category', 'general'),
            'priority' => $request->input('priority', 'medium'),
            'status' => 'open',
        ]);
        return ApiResponse::success($ticket, 'تم إنشاء التذكرة بنجاح', 201);
    }
    
    public function show(int $id): JsonResponse
    {
        $ticket = SupportTicket::with('replies')->findOrFail($id);
        return ApiResponse::success($ticket);
    }
    
    public function reply(Request $request, int $id): JsonResponse
    {
        $request->validate(['message' => 'required|string']);
        $reply = TicketReply::create([
            'smTicketId' => $id,
            'smUserId' => $request->header('X-User-Id', 0),
            'smMessage' => $request->message,
            'isStaff' => false,
        ]);
        return ApiResponse::success($reply, 'تم إرسال الرد');
    }
}
