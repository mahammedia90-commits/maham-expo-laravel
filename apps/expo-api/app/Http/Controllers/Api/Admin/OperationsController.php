<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationsController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'active_tasks' => DB::table('operations_tasks')->where('status', '!=', 'completed')->count(),
                'total_suppliers' => DB::table('suppliers')->count(),
                'active_gates' => DB::table('gates')->where('status', 'active')->count(),
                'pending_permits' => DB::table('permits')->where('status', 'pending')->count(),
                'active_shifts' => DB::table('shifts')->whereDate('date', now()->toDateString())->count(),
                'field_requests' => DB::table('field_service_requests')->where('status', 'pending')->count(),
            ],
        ]);
    }

    // Suppliers
    public function suppliers(Request $request)
    {
        $query = DB::table('suppliers');
        if ($request->search) $query->where('name', 'like', '%'.$request->search.'%');
        return response()->json(['success' => true, 'data' => $query->orderBy('created_at', 'desc')->paginate(20)]);
    }

    public function createSupplier(Request $request)
    {
        $id = DB::table('suppliers')->insertGetId([
            'name' => $request->name,
            'category' => $request->category,
            'contact_name' => $request->contact_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'data' => ['id' => $id]], 201);
    }

    // Gates
    public function gates()
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('gates')->orderBy('name')->get(),
        ]);
    }

    public function gatePasses(Request $request)
    {
        $query = DB::table('gate_passes')
            ->leftJoin('gates', 'gate_passes.gate_id', '=', 'gates.id')
            ->select('gate_passes.*', 'gates.name as gate_name');
        if ($request->gate_id) $query->where('gate_passes.gate_id', $request->gate_id);
        return response()->json(['success' => true, 'data' => $query->orderBy('gate_passes.created_at', 'desc')->paginate(50)]);
    }

    public function gateLogs(Request $request)
    {
        $query = DB::table('gate_logs')
            ->leftJoin('gates', 'gate_logs.gate_id', '=', 'gates.id')
            ->select('gate_logs.*', 'gates.name as gate_name');
        if ($request->date) $query->whereDate('gate_logs.scanned_at', $request->date);
        return response()->json(['success' => true, 'data' => $query->orderBy('gate_logs.scanned_at', 'desc')->paginate(100)]);
    }

    // Permits
    public function permits(Request $request)
    {
        $query = DB::table('permits');
        if ($request->status) $query->where('status', $request->status);
        return response()->json(['success' => true, 'data' => $query->orderBy('created_at', 'desc')->paginate(20)]);
    }

    public function approvePermit($id)
    {
        DB::table('permits')->where('id', $id)->update(['status' => 'approved', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    // Field Services
    public function fieldServices()
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('field_services')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function fieldServiceRequests(Request $request)
    {
        $query = DB::table('field_service_requests');
        if ($request->status) $query->where('status', $request->status);
        return response()->json(['success' => true, 'data' => $query->orderBy('created_at', 'desc')->paginate(20)]);
    }

    // Inventory
    public function inventory()
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('inventory')->orderBy('name')->paginate(50),
        ]);
    }

    public function inventoryMovements(Request $request)
    {
        $query = DB::table('inventory_movements')
            ->leftJoin('inventory', 'inventory_movements.item_id', '=', 'inventory.id')
            ->select('inventory_movements.*', 'inventory.name as item_name');
        return response()->json(['success' => true, 'data' => $query->orderBy('created_at', 'desc')->paginate(50)]);
    }
}
