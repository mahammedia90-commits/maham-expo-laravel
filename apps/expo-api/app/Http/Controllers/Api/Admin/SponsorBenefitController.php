<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorBenefit;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SponsorBenefitController extends Controller
{
    /**
     * List benefits (optionally filtered by contract)
     */
    public function index(Request $request): JsonResponse
    {
        $query = SponsorBenefit::with(['contract.sponsor']);

        if ($contractId = $request->input('sponsor_contract_id')) {
            $query->forContract($contractId);
        }

        if ($type = $request->input('benefit_type')) {
            $query->ofType($type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $benefits = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($benefits);
    }

    /**
     * Add a benefit to a contract
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sponsor_contract_id' => ['required', 'uuid', 'exists:sponsor_contracts,id'],
            'benefit_type' => ['required', Rule::enum(\App\Enums\SponsorBenefitType::class)],
            'description' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);

        $benefit = SponsorBenefit::create($validated);
        $benefit->load('contract.sponsor');

        return ApiResponse::created($benefit, __('messages.sponsor_benefit.created'));
    }

    /**
     * Show a benefit
     */
    public function show(SponsorBenefit $sponsorBenefit): JsonResponse
    {
        $sponsorBenefit->load('contract.sponsor');

        return ApiResponse::success($sponsorBenefit);
    }

    /**
     * Update a benefit
     */
    public function update(Request $request, SponsorBenefit $sponsorBenefit): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'delivery_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $sponsorBenefit->update($validated);

        return ApiResponse::success($sponsorBenefit, __('messages.sponsor_benefit.updated'));
    }

    /**
     * Mark benefit as delivered (partially or fully)
     */
    public function markDelivered(Request $request, SponsorBenefit $sponsorBenefit): JsonResponse
    {
        $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'delivery_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $sponsorBenefit->markDelivered(
            $request->input('quantity', 1),
            $request->input('delivery_notes')
        );

        return ApiResponse::success($sponsorBenefit, __('messages.sponsor_benefit.delivered'));
    }
}
