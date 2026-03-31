<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmController extends Controller
{
    public function dashboard()
    {
        $leads = DB::table('crm_leads')->count();
        $activities = DB::table('crm_activities')->count();
        $tasks = DB::table('crm_tasks')->count();
        $stages = DB::table('crm_leads')
            ->select('stage', DB::raw('COUNT(*) as count'))
            ->groupBy('stage')
            ->pluck('count', 'stage');

        return response()->json([
            'success' => true,
            'data' => [
                'total_leads' => $leads,
                'total_activities' => $activities,
                'total_tasks' => $tasks,
                'pipeline' => $stages,
                'conversion_rate' => $leads > 0 ? round(($stages['contract_sent'] ?? 0) / $leads * 100, 1) : 0,
            ],
        ]);
    }

    public function leads(Request $request)
    {
        $query = DB::table('crm_leads');

        if ($request->stage) $query->where('stage', $request->stage);
        if ($request->source) $query->where('source', $request->source);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('companyName', 'like', '%'.$request->search.'%')
                   ->orWhere('contactName', 'like', '%'.$request->search.'%')
                   ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('createdAt', 'desc')->paginate($request->per_page ?? 20),
        ]);
    }

    public function createLead(Request $request)
    {
        $request->validate([
            'companyName' => 'required|string|max:255',
            'contactName' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'source' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        $id = DB::table('crm_leads')->insertGetId([
            'companyName' => $request->companyName,
            'contactName' => $request->contactName,
            'email' => $request->email,
            'phone' => $request->phone,
            'source' => $request->source ?? 'manual',
            'type' => $request->type ?? 'merchant',
            'stage' => 'new_lead',
            'score' => 0,
            'createdAt' => now()->getTimestamp() * 1000,
            'updatedAt' => now()->getTimestamp() * 1000,
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $id]], 201);
    }

    public function updateLead(Request $request, $id)
    {
        $updates = array_filter([
            'stage' => $request->stage,
            'score' => $request->score,
            'assignedTo' => $request->assignedTo,
            'notes' => $request->notes,
            'updatedAt' => now()->getTimestamp() * 1000,
        ], fn($v) => $v !== null);

        DB::table('crm_leads')->where('id', $id)->update($updates);
        return response()->json(['success' => true]);
    }

    public function advanceStage($id)
    {
        $stages = ['new_lead','attempted_contact','contacted','interested','qualified','highly_qualified','meeting_scheduled','proposal_prepared','negotiation','legal_review','contract_sent','awaiting_approval'];
        $lead = DB::table('crm_leads')->find($id);
        if (!$lead) return response()->json(['success' => false, 'message' => 'Lead not found'], 404);

        $idx = array_search($lead->stage, $stages);
        if ($idx !== false && $idx < count($stages) - 1) {
            DB::table('crm_leads')->where('id', $id)->update([
                'stage' => $stages[$idx + 1],
                'updatedAt' => now()->getTimestamp() * 1000,
            ]);
        }

        return response()->json(['success' => true, 'data' => ['new_stage' => $stages[$idx + 1] ?? $lead->stage]]);
    }

    public function activities(Request $request)
    {
        $query = DB::table('crm_activities')
            ->leftJoin('crm_leads', 'crm_activities.leadId', '=', 'crm_leads.id')
            ->select('crm_activities.*', 'crm_leads.companyName as lead_name');

        if ($request->lead_id) $query->where('crm_activities.leadId', $request->lead_id);

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('crm_activities.createdAt', 'desc')->paginate(20),
        ]);
    }

    public function logActivity(Request $request)
    {
        $request->validate([
            'leadId' => 'required|integer',
            'type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $id = DB::table('crm_activities')->insertGetId([
            'leadId' => $request->leadId,
            'type' => $request->type,
            'notes' => $request->notes,
            'performedBy' => $request->user()?->id ?? 1,
            'createdAt' => now()->getTimestamp() * 1000,
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $id]], 201);
    }

    public function tasks(Request $request)
    {
        $query = DB::table('crm_tasks');
        if ($request->status) $query->where('status', $request->status);
        if ($request->lead_id) $query->where('leadId', $request->lead_id);

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('createdAt', 'desc')->paginate(20),
        ]);
    }
}
