<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        $spacesCount = 0;
        $availableSpaces = 0;
        try { $spacesCount = DB::table('spaces')->count(); $availableSpaces = DB::table('spaces')->where('status','available')->count(); } catch (\Throwable $e) {}
        try { $spacesCount += DB::table('units')->count(); $availableSpaces += DB::table('units')->where('status','available')->count(); } catch (\Throwable $e) {}

        return ApiResponse::success([
            'total_events' => DB::table('events')->count(),
            'active_events' => DB::table('events')->where('status', 'active')->count(),
            'upcoming_events' => DB::table('events')->where('status', 'upcoming')->count(),
            'total_spaces' => $spacesCount,
            'available_spaces' => $availableSpaces,
            'total_users' => DB::table('users')->count(),
            'total_sponsors' => DB::table('sponsor_packages')->count(),
        ]);
    }
}
