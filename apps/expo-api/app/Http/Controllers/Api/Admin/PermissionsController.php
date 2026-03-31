<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionsController extends Controller
{
    public function roles()
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('roles')->orderBy('name')->get(),
        ]);
    }

    public function createRole(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name', 'permissions' => 'array']);
        $id = DB::table('roles')->insertGetId([
            'name' => $request->name,
            'display_name' => $request->display_name ?? $request->name,
            'permissions' => json_encode($request->permissions ?? []),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'data' => ['id' => $id]], 201);
    }

    public function userRoles()
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('user_roles')
                ->leftJoin('users', 'user_roles.user_id', '=', 'users.id')
                ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
                ->select('user_roles.*', 'users.name as user_name', 'users.email', 'roles.name as role_name')
                ->orderBy('user_roles.created_at', 'desc')
                ->paginate(50),
        ]);
    }

    public function assignRole(Request $request)
    {
        $request->validate(['user_id' => 'required|integer', 'role_id' => 'required|integer']);
        DB::table('user_roles')->updateOrInsert(
            ['user_id' => $request->user_id],
            ['role_id' => $request->role_id, 'created_at' => now(), 'updated_at' => now()]
        );
        return response()->json(['success' => true]);
    }

    public function auditLogs(Request $request)
    {
        $query = DB::table('audit_logs');
        if ($request->user_id) $query->where('user_id', $request->user_id);
        if ($request->action) $query->where('action', $request->action);
        if ($request->date) $query->whereDate('created_at', $request->date);

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 50),
        ]);
    }

    public function departmentAccess()
    {
        return response()->json([
            'success' => true,
            'data' => DB::table('user_department_access')
                ->leftJoin('users', 'user_department_access.user_id', '=', 'users.id')
                ->select('user_department_access.*', 'users.name as user_name')
                ->get(),
        ]);
    }
}
