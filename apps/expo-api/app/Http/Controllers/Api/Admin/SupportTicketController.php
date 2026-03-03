<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * قائمة جميع التذاكر مع فلترة
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::query()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category, fn ($q) => $q->ofCategory($request->category))
            ->when($request->priority, fn ($q) => $q->ofPriority($request->priority))
            ->when($request->assigned_to, fn ($q) => $q->assignedTo($request->assigned_to))
            ->when($request->unassigned, fn ($q) => $q->unassigned())
            ->when($request->user_id, fn ($q) => $q->forUser($request->user_id))
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return ApiResponse::paginated($tickets, __('messages.support_ticket.list_fetched'));
    }

    /**
     * عرض تفاصيل تذكرة
     */
    public function show(string $id): JsonResponse
    {
        $ticket = SupportTicket::with([
            'replies',
        ])->find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        return ApiResponse::success($ticket, __('messages.support_ticket.fetched'));
    }

    /**
     * تعيين تذكرة لموظف دعم
     */
    public function assign(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        $request->validate([
            'assigned_to' => 'required|uuid',
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status'      => 'in_progress',
        ]);

        return ApiResponse::success($ticket, __('messages.support_ticket.assigned'));
    }

    /**
     * الرد على تذكرة من قبل الإدارة
     */
    public function reply(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        if ($ticket->status === 'closed') {
            return ApiResponse::error(
                __('messages.support_ticket.ticket_closed'),
                ApiErrorCode::TICKET_CLOSED,
                422
            );
        }

        $request->validate([
            'message'     => 'required|string|max:5000',
            'message_ar'  => 'nullable|string|max:5000',
            'attachments' => 'nullable|array',
        ]);

        $reply = TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => $request->input('auth_user_id'),
            'message'        => $request->message,
            'message_ar'     => $request->message_ar ?? $request->message,
            'is_staff_reply' => true,
            'attachments'    => $request->attachments ?? [],
        ]);

        // تحديث حالة التذكرة إلى "في انتظار رد العميل"
        $ticket->markWaitingReply();

        return ApiResponse::created($reply, __('messages.support_ticket.reply_added'));
    }

    /**
     * إغلاق تذكرة
     */
    public function close(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        $ticket->close();

        return ApiResponse::success($ticket, __('messages.support_ticket.closed'));
    }

    /**
     * حل تذكرة
     */
    public function resolve(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        $ticket->resolve($request->input('auth_user_id'));

        return ApiResponse::success($ticket, __('messages.support_ticket.resolved'));
    }

    /**
     * حذف تذكرة
     */
    public function destroy(string $id): JsonResponse
    {
        $ticket = SupportTicket::find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        $ticket->delete();

        return ApiResponse::success(null, __('messages.support_ticket.deleted'));
    }
}
