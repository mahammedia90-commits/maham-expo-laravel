<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExhibitionMapController extends Controller
{
    public function show($event): JsonResponse
    {
        $map = [
            'event_id' => $event,
            'name' => 'Exhibition Map',
            'layout_data' => ['grid_size' => '10x15', 'total_spaces' => 150],
            'spaces' => [
                ['id' => 1, 'code' => 'A1', 'status' => 'occupied'],
            ],
        ];
        return response()->json(['data' => $map]);
    }

    public function updateSpace(Request $request, $space): JsonResponse
    {
        $status = $request->get('status');
        return response()->json(['data' => ['space_id' => $space, 'status' => $status]]);
    }

    public function stats($event): JsonResponse
    {
        return response()->json(['data' => ['event_id' => $event, 'total_spaces' => 150, 'occupied' => 87]]);
    }
}
