<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Space;
use App\Models\RentalRequest;
use App\Models\RentalContract;
use App\Models\Service;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class MerchantPortalController extends Controller
{
    /**
     * Merchant portal dashboard with real metrics
     */
    public function dashboard(): JsonResponse
    {
        try {
            $merchants = BusinessProfile::where('type', 'merchant')->count();
            $booths = Space::count();
            $occupiedBooths = Space::where('status', 'occupied')->count();
            $occupancyRate = $booths > 0 ? round(($occupiedBooths / $booths) * 100, 1) : 0;

            $totalRevenue = RentalContract::sum('amount') ?? 0;
            $activeContracts = RentalContract::where('status', 'active')->count();
            $pendingRequests = RentalRequest::where('status', 'pending')->count();

            return response()->json([
                'data' => [
                    'total_merchants' => $merchants,
                    'total_booths' => $booths,
                    'occupied_booths' => $occupiedBooths,
                    'available_booths' => $booths - $occupiedBooths,
                    'occupancy_rate' => $occupancyRate,
                    'total_revenue' => (float)$totalRevenue,
                    'active_contracts' => $activeContracts,
                    'pending_requests' => $pendingRequests,
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if tables don't exist
            return response()->json([
                'data' => [
                    'total_merchants' => 0,
                    'total_booths' => 0,
                    'occupied_booths' => 0,
                    'available_booths' => 0,
                    'occupancy_rate' => 0,
                    'total_revenue' => 0,
                    'active_contracts' => 0,
                    'pending_requests' => 0,
                ]
            ]);
        }
    }

    /**
     * List all merchants from database
     */
    public function merchants(): JsonResponse
    {
        $merchants = BusinessProfile::where('type', 'merchant')
            ->select('id', 'name', 'name_en', 'email', 'phone', 'sector', 'location', 'status')
            ->paginate(20);

        return response()->json([
            'data' => $merchants->items(),
            'meta' => [
                'current_page' => $merchants->currentPage(),
                'last_page' => $merchants->lastPage(),
                'per_page' => $merchants->perPage(),
                'total' => $merchants->total(),
            ]
        ]);
    }

    /**
     * List all booths/spaces from database
     */
    public function booths(): JsonResponse
    {
        $booths = Space::select('id', 'name', 'zone', 'type', 'size', 'price', 'status', 'description')
            ->paginate(20);

        return response()->json([
            'data' => $booths->items(),
            'meta' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
            ]
        ]);
    }

    /**
     * Get booth map data for an event
     */
    public function boothMap($eventId): JsonResponse
    {
        try {
            $spaces = Space::where('eventId', $eventId)
                ->select('id', 'name', 'zone', 'status', 'price', 'type', 'size')
                ->get();

            $totalSpaces = $spaces->count();
            $occupiedSpaces = $spaces->where('status', 'occupied')->count();
            $availableSpaces = $totalSpaces - $occupiedSpaces;

            return response()->json([
                'data' => [
                    'event_id' => $eventId,
                    'total_spaces' => $totalSpaces,
                    'occupied_spaces' => $occupiedSpaces,
                    'available_spaces' => $availableSpaces,
                    'occupancy_rate' => $totalSpaces > 0 ? round(($occupiedSpaces / $totalSpaces) * 100, 1) : 0,
                    'spaces' => $spaces,
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [
                    'event_id' => $eventId,
                    'total_spaces' => 0,
                    'occupied_spaces' => 0,
                    'available_spaces' => 0,
                    'occupancy_rate' => 0,
                    'spaces' => [],
                ]
            ]);
        }
    }

    /**
     * Get rental/booth requests from database
     */
    public function boothRequests(): JsonResponse
    {
        $requests = RentalRequest::select('id', 'merchant_id', 'space_id', 'status', 'amount', 'created_at')
            ->paginate(20);

        return response()->json([
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ]
        ]);
    }

    /**
     * Get merchant contracts from database
     */
    public function contracts(): JsonResponse
    {
        try {
            $contracts = RentalContract::select('id', 'contract_number', 'amount', 'currency', 'status', 'payment_status', 'start_date', 'end_date')
                ->paginate(20);

            return response()->json([
                'data' => $contracts->items(),
                'meta' => [
                    'current_page' => $contracts->currentPage(),
                    'last_page' => $contracts->lastPage(),
                    'per_page' => $contracts->perPage(),
                    'total' => $contracts->total(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 0,
                    'per_page' => 20,
                    'total' => 0,
                ]
            ]);
        }
    }

    /**
     * Get booth/exhibition services from database
     */
    public function services(): JsonResponse
    {
        try {
            $services = Service::select('id', 'name', 'description', 'price', 'status')
                ->paginate(20);

            return response()->json([
                'data' => $services->items(),
                'meta' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 0,
                    'per_page' => 20,
                    'total' => 0,
                ]
            ]);
        }
    }

    /**
     * Get merchant payments from database
     */
    public function payments(): JsonResponse
    {
        try {
            $payments = Payment::select('id', 'amount', 'method', 'status', 'created_at')
                ->paginate(20);

            return response()->json([
                'data' => $payments->items(),
                'meta' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 0,
                    'per_page' => 20,
                    'total' => 0,
                ]
            ]);
        }
    }

    /**
     * Get merchant messages/notifications
     */
    public function messages(): JsonResponse
    {
        // Get notifications if available
        $notifications = [];

        return response()->json([
            'data' => $notifications,
            'meta' => [
                'total' => 0,
            ]
        ]);
    }
}
