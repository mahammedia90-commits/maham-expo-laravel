<?php

namespace App\Http\Controllers\Api;

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
     * قائمة تذاكر الدعم الخاصة بالمستخدم
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::forUser($request->input('auth_user_id'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category, fn ($q) => $q->ofCategory($request->category))
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return ApiResponse::paginated($tickets, __('messages.support_ticket.list_fetched'));
    }

    /**
     * إنشاء تذكرة دعم جديدة
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'subject_ar'  => 'nullable|string|max:255',
            'description' => 'required|string|max:5000',
            'description_ar' => 'nullable|string|max:5000',
            'category'    => 'required|string',
            'priority'    => 'nullable|string',
            'attachments' => 'nullable|array|max:' . config('expo-api.support.max_attachments', 5),
            'attachments.*' => 'string',
        ]);

        $ticket = SupportTicket::create([
            'user_id'        => $request->input('auth_user_id'),
            'subject'        => $request->subject,
            'subject_ar'     => $request->subject_ar ?? $request->subject,
            'description'    => $request->description,
            'description_ar' => $request->description_ar ?? $request->description,
            'category'       => $request->category,
            'priority'       => $request->priority ?? 'medium',
            'status'         => 'open',
            'attachments'    => $request->attachments ?? [],
        ]);

        return ApiResponse::created($ticket, __('messages.support_ticket.created'));
    }

    /**
     * عرض تفاصيل تذكرة
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::with('replies.user:id,name')
            ->forUser($request->input('auth_user_id'))
            ->find($id);

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
     * إضافة رد على تذكرة
     */
    public function reply(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::forUser($request->input('auth_user_id'))->find($id);

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
            'attachments.*' => 'string',
        ]);

        $reply = TicketReply::create([
            'ticket_id'     => $ticket->id,
            'user_id'       => $request->input('auth_user_id'),
            'message'       => $request->message,
            'message_ar'    => $request->message_ar ?? $request->message,
            'is_staff_reply' => false,
            'attachments'   => $request->attachments ?? [],
        ]);

        // إعادة فتح التذكرة إذا كانت منتظرة الرد
        if ($ticket->status === 'waiting_reply') {
            $ticket->update(['status' => 'in_progress']);
        }

        return ApiResponse::created($reply, __('messages.support_ticket.reply_added'));
    }

    /**
     * إغلاق تذكرة من قبل المستخدم
     */
    public function close(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::forUser($request->input('auth_user_id'))->find($id);

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

        $ticket->close();

        return ApiResponse::success($ticket, __('messages.support_ticket.closed'));
    }

    /**
     * إعادة فتح تذكرة
     */
    public function reopen(Request $request, string $id): JsonResponse
    {
        $ticket = SupportTicket::forUser($request->input('auth_user_id'))->find($id);

        if (! $ticket) {
            return ApiResponse::error(
                __('messages.support_ticket.not_found'),
                ApiErrorCode::TICKET_NOT_FOUND,
                404
            );
        }

        $ticket->reopen();

        return ApiResponse::success($ticket, __('messages.support_ticket.reopened'));
    }
}
