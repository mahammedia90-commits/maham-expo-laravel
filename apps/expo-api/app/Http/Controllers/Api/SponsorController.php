<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SponsorPackage;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SponsorController extends Controller {
    public function index(Request $request, int $eventId): JsonResponse {
        $sponsors = DB::table('sponsorships')
            ->join('sponsor_packages', 'sponsorships.packageId', '=', 'sponsor_packages.id')
            ->where('sponsor_packages.eventId', $eventId)
            ->select('sponsorships.*', 'sponsor_packages.name as packageName')
            ->get();
        return ApiResponse::success($sponsors);
    }
    
    public function packages(Request $request, int $eventId): JsonResponse {
        $packages = SponsorPackage::where('eventId', $eventId)->get();
        return ApiResponse::success($packages);
    }
}
