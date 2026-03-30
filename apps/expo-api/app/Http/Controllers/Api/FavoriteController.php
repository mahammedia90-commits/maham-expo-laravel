<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->header('X-User-Id', 0);
        $favorites = DB::table('favorites')->where('userId', $userId)->get();
        return ApiResponse::success($favorites);
    }
    
    public function store(Request $request): JsonResponse
    {
        DB::table('favorites')->insertOrIgnore([
            'userId' => $request->header('X-User-Id', 0),
            'eventId' => $request->input('event_id'),
        ]);
        return ApiResponse::success(null, 'تمت الإضافة للمفضلة');
    }
    
    public function destroy(int $id): JsonResponse
    {
        DB::table('favorites')->where('id', $id)->delete();
        return ApiResponse::success(null, 'تمت الإزالة من المفضلة');
    }
}
