<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorLead;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorLeadController extends Controller
{
    use SafeOrderBy;

    /**
     * قائمة جميع العملاء المحتملين
     */
    public function index(Request $request): JsonResponse
    {
        $query = SponsorLead::with(['sponsor', 'event']);

        if ($sponsorId = $request->input('sponsor_id')) {
            $query->forSponsor($sponsorId);
        }

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($interest = $request->input('interest_level')) {
            $query->ofInterest($interest);
        }

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->search($search);
        }

        $this->applySafeOrder($query, $request, [
            'company_name', 'contact_name', 'interest_level', 'status', 'captured_at', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    /**
     * إنشاء عميل محتمل
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sponsor_id' => ['required', 'uuid', 'exists:sponsors,id'],
            'event_id' => ['nullable', 'uuid', 'exists:events,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_name_ar' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_name_ar' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'industry' => ['nullable', 'string', 'max:255'],
            'industry_ar' => ['nullable', 'string', 'max:255'],
            'interest_level' => ['sometimes', 'in:high,medium,low'],
            'status' => ['sometimes', 'in:new,contacted,qualified,converted,lost'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'notes_ar' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:255'],
            'captured_at' => ['nullable', 'date'],
        ]);

        $lead = SponsorLead::create($validated);
        $lead->load(['sponsor', 'event']);

        return ApiResponse::created($lead, __('messages.sponsor_lead.created'));
    }

    /**
     * تفاصيل عميل محتمل
     */
    public function show(SponsorLead $sponsorLead): JsonResponse
    {
        $sponsorLead->load(['sponsor', 'event']);
        return ApiResponse::success($sponsorLead);
    }

    /**
     * تحديث عميل محتمل
     */
    public function update(Request $request, SponsorLead $sponsorLead): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => ['sometimes', 'string', 'max:255'],
            'company_name_ar' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['sometimes', 'string', 'max:255'],
            'contact_name_ar' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'industry' => ['nullable', 'string', 'max:255'],
            'industry_ar' => ['nullable', 'string', 'max:255'],
            'interest_level' => ['sometimes', 'in:high,medium,low'],
            'status' => ['sometimes', 'in:new,contacted,qualified,converted,lost'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'notes_ar' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:255'],
            'captured_at' => ['nullable', 'date'],
        ]);

        $sponsorLead->update($validated);
        $sponsorLead->load(['sponsor', 'event']);

        return ApiResponse::success($sponsorLead, __('messages.sponsor_lead.updated'));
    }

    /**
     * حذف عميل محتمل
     */
    public function destroy(SponsorLead $sponsorLead): JsonResponse
    {
        $sponsorLead->delete();
        return ApiResponse::success(null, __('messages.sponsor_lead.deleted'));
    }
}
