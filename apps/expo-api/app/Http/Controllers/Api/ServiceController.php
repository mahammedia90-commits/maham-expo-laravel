<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
class ServiceController extends Controller {
    public function index(): JsonResponse { return ApiResponse::success(DB::table('exhibitor_services')->get()); }
}
