<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function inbox(Request $request)
    {
        $messages = DB::table('notifications')
            ->where('type', 'message')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|integer',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $id = DB::table('notifications')->insertGetId([
            'user_id' => $request->to_user_id,
            'title' => $request->subject,
            'body' => $request->body,
            'type' => 'message',
            'channel' => 'internal',
            'status' => 'unread',
            'data' => json_encode(['from' => $request->user()?->id ?? 1, 'type' => 'admin_message']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $id]], 201);
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'target' => 'required|in:all,merchants,investors,sponsors,staff',
        ]);

        $targetQuery = DB::table('users');
        if ($request->target !== 'all') {
            $roleMap = ['merchants' => 'merchant', 'investors' => 'investor', 'sponsors' => 'sponsor', 'staff' => 'staff'];
            $targetQuery->where('role', $roleMap[$request->target] ?? $request->target);
        }

        $users = $targetQuery->pluck('id');
        $now = now();
        $inserts = $users->map(fn($uid) => [
            'user_id' => $uid,
            'title' => $request->subject,
            'body' => $request->body,
            'type' => 'broadcast',
            'channel' => 'internal',
            'status' => 'unread',
            'data' => json_encode(['from' => 'admin', 'target' => $request->target]),
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        if (!empty($inserts)) {
            DB::table('notifications')->insert($inserts);
        }

        return response()->json(['success' => true, 'data' => ['sent_to' => count($inserts)]]);
    }

    public function templates()
    {
        $templates = DB::table('notification_templates')->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $templates]);
    }
}
