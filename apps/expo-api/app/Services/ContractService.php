<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractParty;
use App\Models\ContractVersion;
use App\Models\ContractPaymentPlan;
use App\Models\ContractStatusLog;
use App\Models\ContractReminder;
use App\Models\Invoice;
use App\Enums\UnifiedContractStatus;
use App\Enums\ContractType;
use App\Enums\PaymentPlanStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

class ContractService
{
    // =========================================================================
    // CONTRACT CRUD
    // =========================================================================

    /**
     * Create a new contract with parties, version 1, and status log.
     * Auto-calculates financials. If payment_method=installments, generates plan.
     */
    public function create(array $data, string $userId): Contract
    {
        return DB::transaction(function () use ($data, $userId) {
            // Calculate financials
            $financials = $this->calculateFinancials($data);

            $contract = Contract::create([
                'contract_number'      => $this->generateContractNumber(),
                'title'                => $data['title'] ?? null,
                'title_ar'             => $data['title_ar'] ?? null,
                'description'          => $data['description'] ?? null,
                'description_ar'       => $data['description_ar'] ?? null,
                'type'                 => $data['type'] ?? ContractType::RENTAL->value,
                'status'               => UnifiedContractStatus::DRAFT->value,
                'priority'             => $data['priority'] ?? 'medium',

                // References
                'event_id'             => $data['event_id'] ?? null,
                'space_id'             => $data['space_id'] ?? null,
                'rental_request_id'    => $data['rental_request_id'] ?? null,
                'sponsor_id'           => $data['sponsor_id'] ?? null,

                // Dates
                'start_date'           => $data['start_date'] ?? null,
                'end_date'             => $data['end_date'] ?? null,
                'signing_deadline'     => $data['signing_deadline'] ?? null,

                // Financials
                'currency'             => $data['currency'] ?? 'SAR',
                'subtotal'             => $financials['subtotal'],
                'discount_amount'      => $data['discount_amount'] ?? 0,
                'discount_percentage'  => $data['discount_percentage'] ?? 0,
                'discount_amount'      => $financials['discount_amount'],
                'taxable_amount'       => $financials['taxable_amount'],
                'vat_rate'             => $data['vat_rate'] ?? 15,
                'vat_amount'           => $financials['vat_amount'],
                'total_amount'         => $financials['total_amount'],
                'paid_amount'          => 0,
                'payment_status'       => 'unpaid',

                // Payment terms
                'payment_method'       => $data['payment_method'] ?? 'full',
                'payment_terms_days'   => $data['payment_terms_days'] ?? 30,
                'installments_count'   => $data['installments_count'] ?? 1,

                // Content
                'terms_and_conditions' => $data['terms_and_conditions'] ?? null,
                'special_conditions'   => $data['special_conditions'] ?? null,
                'notes'                => $data['notes'] ?? null,
                'notes_ar'             => $data['notes_ar'] ?? null,
                'attachments'          => $data['attachments'] ?? null,
                'template_id'          => $data['template_id'] ?? null,

                // Metadata
                'created_by'           => $userId,
                'auto_renew'           => $data['auto_renew'] ?? false,
                'renewal_terms'        => $data['renewal_terms'] ?? null,
            ]);

            // Create parties
            if (!empty($data['parties'])) {
                foreach ($data['parties'] as $party) {
                    ContractParty::create([
                        'contract_id'   => $contract->id,
                        'party_type'    => $party['party_type'],           // e.g. landlord, tenant, sponsor
                        'party_role'    => $party['party_role'] ?? null,
                        'user_id'       => $party['user_id'] ?? null,
                        'business_id'   => $party['business_id'] ?? null,
                        'name'          => $party['name'] ?? null,
                        'name_ar'       => $party['name_ar'] ?? null,
                        'email'         => $party['email'] ?? null,
                        'phone'         => $party['phone'] ?? null,
                        'id_number'     => $party['id_number'] ?? null,
                        'is_primary'    => $party['is_primary'] ?? false,
                        'signing_order' => $party['signing_order'] ?? 0,
                    ]);
                }
            }

            // Generate version 1
            $this->createVersion($contract, $userId, 'Contract created');

            // Log creation
            $this->logStatusChange(
                $contract,
                'created',
                null,
                UnifiedContractStatus::DRAFT->value,
                $userId
            );

            // Auto-generate payment plan for installments
            if (($data['payment_method'] ?? 'full') === 'installments') {
                $this->generatePaymentPlan($contract);
            }

            return $contract->fresh(['parties', 'versions', 'paymentPlan']);
        });
    }

    /**
     * Update contract (only if in draft status).
     * Creates a new version, recalculates financials, and logs changes.
     */
    public function update(Contract $contract, array $data, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::DRAFT]);

        return DB::transaction(function () use ($contract, $data, $userId) {
            // Snapshot original fields for diff
            $original = $contract->only([
                'title', 'title_ar', 'description', 'start_date', 'end_date',
                'subtotal', 'discount_amount', 'discount_percentage', 'vat_rate',
                'payment_method', 'installments_count', 'terms_and_conditions',
                'special_conditions', 'notes',
            ]);

            // Recalculate financials if any financial field changed
            $financialKeys = ['subtotal', 'discount_amount', 'discount_percentage', 'vat_rate'];
            $hasFinancialChange = count(array_intersect_key($data, array_flip($financialKeys))) > 0;

            if ($hasFinancialChange) {
                $calcData = array_merge($contract->toArray(), $data);
                $financials = $this->calculateFinancials($calcData);
                $data = array_merge($data, [
                    'discount_amount' => $financials['discount_amount'],
                    'taxable_amount'  => $financials['taxable_amount'],
                    'vat_amount'      => $financials['vat_amount'],
                    'total_amount'    => $financials['total_amount'],
                ]);
            }

            $contract->update($data);

            // Update parties if provided
            if (isset($data['parties'])) {
                $contract->parties()->delete();
                foreach ($data['parties'] as $party) {
                    ContractParty::create(array_merge($party, [
                        'contract_id' => $contract->id,
                    ]));
                }
            }

            // Determine changed fields
            $changedFields = [];
            foreach ($original as $key => $oldValue) {
                if (isset($data[$key]) && $data[$key] != $oldValue) {
                    $changedFields[$key] = [
                        'old' => $oldValue,
                        'new' => $data[$key],
                    ];
                }
            }

            // New version
            $this->createVersion(
                $contract,
                $userId,
                'Contract updated',
                $changedFields ?: null
            );

            // Regenerate payment plan if payment method or financials changed
            if ($hasFinancialChange || isset($data['payment_method']) || isset($data['installments_count'])) {
                $contract->paymentPlan()->delete();
                $this->generatePaymentPlan($contract->fresh());
            }

            $this->logStatusChange($contract, 'updated', $contract->status, $contract->status, $userId, [
                'changed_fields' => array_keys($changedFields),
            ]);

            return $contract->fresh(['parties', 'versions', 'paymentPlan']);
        });
    }

    // =========================================================================
    // LIFECYCLE TRANSITIONS
    // =========================================================================

    /**
     * Submit a draft contract for review.
     * Validates that all required fields are filled.
     */
    public function submitForReview(Contract $contract, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::DRAFT]);
        $this->validateRequiredFields($contract);

        return DB::transaction(function () use ($contract, $userId) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'       => UnifiedContractStatus::UNDER_REVIEW->value,
            ]);

            $this->logStatusChange(
                $contract, 'submitted_for_review',
                $fromStatus, UnifiedContractStatus::UNDER_REVIEW->value,
                $userId
            );

            return $contract->fresh();
        });
    }

    /**
     * Legal department approval.
     */
    public function approveLegal(Contract $contract, string $userId, ?string $notes = null): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::UNDER_REVIEW]);

        return DB::transaction(function () use ($contract, $userId, $notes) {
            $contract->update([
                'legal_approved'    => true,
                'legal_approved_by' => $userId,
                'legal_approved_at' => now(),
                'legal_notes'       => $notes,
            ]);

            $this->logStatusChange(
                $contract, 'legal_approved',
                $contract->status, $contract->status,
                $userId,
                ['description' => $notes]
            );

            return $contract->fresh();
        });
    }

    /**
     * Finance department approval. Requires prior legal approval.
     */
    public function approveFinance(Contract $contract, string $userId, ?string $notes = null): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::UNDER_REVIEW]);

        if (!$contract->legal_approved) {
            throw new LogicException('Finance approval requires legal approval first.');
        }

        return DB::transaction(function () use ($contract, $userId, $notes) {
            $contract->update([
                'finance_approved'    => true,
                'finance_approved_by' => $userId,
                'finance_approved_at' => now(),
                'finance_notes'       => $notes,
            ]);

            $this->logStatusChange(
                $contract, 'finance_approved',
                $contract->status, $contract->status,
                $userId,
                ['description' => $notes]
            );

            return $contract->fresh();
        });
    }

    /**
     * Final approval. Requires both legal and finance approval.
     * Transitions: under_review -> approved.
     */
    public function approveFinal(Contract $contract, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::UNDER_REVIEW]);

        if (!$contract->legal_approved) {
            throw new LogicException('Final approval requires legal approval.');
        }
        if (!$contract->finance_approved) {
            throw new LogicException('Final approval requires finance approval.');
        }

        return DB::transaction(function () use ($contract, $userId) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'              => UnifiedContractStatus::APPROVED->value,
                'final_approved_by'   => $userId,
                'final_approved_at'   => now(),
            ]);

            $this->logStatusChange(
                $contract, 'approved',
                $fromStatus, UnifiedContractStatus::APPROVED->value,
                $userId
            );

            return $contract->fresh();
        });
    }

    /**
     * Reject a contract under review.
     */
    public function reject(Contract $contract, string $userId, string $reason): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::UNDER_REVIEW]);

        return DB::transaction(function () use ($contract, $userId, $reason) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'           => UnifiedContractStatus::REJECTED->value,
                'rejection_reason' => $reason,
                'rejected_by'      => $userId,
                'rejected_at'      => now(),
            ]);

            $this->logStatusChange(
                $contract, 'rejected',
                $fromStatus, UnifiedContractStatus::REJECTED->value,
                $userId,
                ['reason' => $reason]
            );

            return $contract->fresh();
        });
    }

    /**
     * Request changes — sends contract back to draft for editing.
     */
    public function requestChanges(Contract $contract, string $userId, string $notes): Contract
    {
        $this->assertStatus($contract, [
            UnifiedContractStatus::UNDER_REVIEW,
            UnifiedContractStatus::APPROVED,
        ]);

        return DB::transaction(function () use ($contract, $userId, $notes) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'               => UnifiedContractStatus::DRAFT->value,
                'admin_notes'         => $notes,
                // Reset approvals when sent back
                'legal_approved'       => false,
                'legal_approved_by'    => null,
                'legal_approved_at'    => null,
                'finance_approved'     => false,
                'finance_approved_by'  => null,
                'finance_approved_at'  => null,
                'final_approved_by'    => null,
                'final_approved_at'    => null,
            ]);

            $this->logStatusChange(
                $contract, 'changes_requested',
                $fromStatus, UnifiedContractStatus::DRAFT->value,
                $userId,
                ['description' => $notes]
            );

            return $contract->fresh();
        });
    }

    /**
     * Send approved contract for signature.
     * Generates signing tokens for each party, sets signing_deadline.
     */
    public function sendForSignature(Contract $contract, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::APPROVED]);

        $parties = $contract->parties;
        if ($parties->isEmpty()) {
            throw new LogicException('Contract must have at least one party before sending for signature.');
        }

        return DB::transaction(function () use ($contract, $userId, $parties) {
            $fromStatus = $contract->status;

            // Generate signing tokens for each party
            foreach ($parties as $party) {
                $party->update([
                    'signing_token'            => Str::random(64),
                    'signing_token_expires_at' => now()->addDays(14),
                ]);
            }

            $contract->update([
                'status'           => UnifiedContractStatus::SENT_FOR_SIGNATURE->value,
                'signing_deadline' => $contract->signing_deadline ?? now()->addDays(14),
            ]);

            $this->logStatusChange(
                $contract, 'sent_for_signature',
                $fromStatus, UnifiedContractStatus::SENT_FOR_SIGNATURE->value,
                $userId,
                ['parties_count' => $parties->count()]
            );

            return $contract->fresh(['parties']);
        });
    }

    /**
     * Record a signature for a specific party.
     * When all parties have signed, marks the contract as fully signed.
     */
    public function sign(Contract $contract, string $partyId, array $signatureData): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::SENT_FOR_SIGNATURE]);

        $party = $contract->parties()->where('id', $partyId)->first();
        if (!$party) {
            throw new InvalidArgumentException("Party [{$partyId}] not found on this contract.");
        }

        if ($party->signed_at) {
            throw new LogicException('This party has already signed the contract.');
        }

        return DB::transaction(function () use ($contract, $party, $signatureData) {
            $party->update([
                'signed_at'        => now(),
                'signature_data'   => $signatureData['signature'] ?? null,
                'signature_ip'     => $signatureData['ip'] ?? null,
                'signature_device' => $signatureData['user_agent'] ?? null,
            ]);

            // Check if all parties have signed
            $unsignedCount = $contract->parties()
                ->whereNull('signed_at')
                ->count();

            if ($unsignedCount === 0) {
                $fromStatus = $contract->status;
                $contract->update([
                    'status'          => UnifiedContractStatus::SIGNED->value,
                    'is_fully_signed' => true,
                    'signed_at'       => now(),
                ]);

                $this->logStatusChange(
                    $contract, 'fully_signed',
                    $fromStatus, UnifiedContractStatus::SIGNED->value,
                    $party->user_id ?? 'system',
                    ['last_signer_party_id' => $party->id]
                );
            } else {
                $this->logStatusChange(
                    $contract, 'party_signed',
                    $contract->status, $contract->status,
                    $party->user_id ?? 'system',
                    [
                        'party_id'  => $party->id,
                        'remaining' => $unsignedCount,
                    ]
                );
            }

            return $contract->fresh(['parties']);
        });
    }

    /**
     * Activate a signed contract. Triggers invoice generation.
     */
    public function activate(Contract $contract, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::SIGNED]);

        return DB::transaction(function () use ($contract, $userId) {
            $fromStatus = $contract->status;
            $contract->update([
                'status' => UnifiedContractStatus::ACTIVE->value,
            ]);

            // Generate invoices from payment plan
            $this->generateInvoices($contract);

            $this->logStatusChange(
                $contract, 'activated',
                $fromStatus, UnifiedContractStatus::ACTIVE->value,
                $userId
            );

            return $contract->fresh();
        });
    }

    /**
     * Suspend an active contract.
     */
    public function suspend(Contract $contract, string $userId, string $reason): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::ACTIVE]);

        return DB::transaction(function () use ($contract, $userId, $reason) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'            => UnifiedContractStatus::SUSPENDED->value,
                'suspension_reason' => $reason,
                'suspended_at'      => now(),
                'suspended_by'      => $userId,
            ]);

            $this->logStatusChange(
                $contract, 'suspended',
                $fromStatus, UnifiedContractStatus::SUSPENDED->value,
                $userId,
                ['reason' => $reason]
            );

            return $contract->fresh();
        });
    }

    /**
     * Reactivate a suspended contract.
     */
    public function reactivate(Contract $contract, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::SUSPENDED]);

        return DB::transaction(function () use ($contract, $userId) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'            => UnifiedContractStatus::ACTIVE->value,
                'suspension_reason' => null,
                'suspended_at'      => null,
                'suspended_by'      => null,
            ]);

            $this->logStatusChange(
                $contract, 'reactivated',
                $fromStatus, UnifiedContractStatus::ACTIVE->value,
                $userId
            );

            return $contract->fresh();
        });
    }

    /**
     * Complete an active contract.
     */
    public function complete(Contract $contract, string $userId): Contract
    {
        $this->assertStatus($contract, [UnifiedContractStatus::ACTIVE]);

        return DB::transaction(function () use ($contract, $userId) {
            $fromStatus = $contract->status;
            $contract->update([
                'status' => UnifiedContractStatus::COMPLETED->value,
            ]);

            $this->logStatusChange(
                $contract, 'completed',
                $fromStatus, UnifiedContractStatus::COMPLETED->value,
                $userId
            );

            return $contract->fresh();
        });
    }

    /**
     * Cancel a contract (only from early lifecycle stages).
     */
    public function cancel(Contract $contract, string $userId, string $reason): Contract
    {
        $this->assertStatus($contract, [
            UnifiedContractStatus::DRAFT,
            UnifiedContractStatus::UNDER_REVIEW,
            UnifiedContractStatus::APPROVED,
        ]);

        return DB::transaction(function () use ($contract, $userId, $reason) {
            $fromStatus = $contract->status;
            $contract->update([
                'status'              => UnifiedContractStatus::CANCELLED->value,
                'cancellation_reason' => $reason,
                'cancelled_at'        => now(),
                'cancelled_by'        => $userId,
            ]);

            $this->logStatusChange(
                $contract, 'cancelled',
                $fromStatus, UnifiedContractStatus::CANCELLED->value,
                $userId,
                ['reason' => $reason]
            );

            return $contract->fresh();
        });
    }

    /**
     * Terminate an active or suspended contract early.
     * Calculates and records early termination penalty.
     */
    public function terminate(Contract $contract, string $userId, string $reason): Contract
    {
        $this->assertStatus($contract, [
            UnifiedContractStatus::ACTIVE,
            UnifiedContractStatus::SUSPENDED,
        ]);

        return DB::transaction(function () use ($contract, $userId, $reason) {
            $fromStatus = $contract->status;

            // Calculate early termination penalty
            $penaltyAmount = $this->calculateTerminationPenalty($contract);

            $contract->update([
                'status'              => UnifiedContractStatus::TERMINATED->value,
                'termination_reason'  => $reason,
                'terminated_at'       => now(),
                'terminated_by'       => $userId,
                'penalty_amount'      => $penaltyAmount,
            ]);

            $this->logStatusChange(
                $contract, 'terminated',
                $fromStatus, UnifiedContractStatus::TERMINATED->value,
                $userId,
                [
                    'reason'  => $reason,
                    'penalty' => $penaltyAmount,
                ]
            );

            return $contract->fresh();
        });
    }

    // =========================================================================
    // FINANCIAL
    // =========================================================================

    /**
     * Calculate all financial fields from input data.
     *
     * @return array{subtotal: float, discount_amount: float, taxable_amount: float, vat_amount: float, total_amount: float}
     */
    public function calculateFinancials(array $data): array
    {
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $vatRate  = (float) ($data['vat_rate'] ?? 15);

        // Calculate discount
        $discount = 0;
        if (!empty($data['discount_percentage']) && $data['discount_percentage'] > 0) {
            $discount = $subtotal * ($data['discount_percentage'] / 100);
        } elseif (!empty($data['discount_amount']) && $data['discount_amount'] > 0) {
            $discount = $data['discount_amount'];
        }
        $discountAmount = round($discount, 2);

        $taxableAmount = round($subtotal - $discountAmount, 2);
        $vatAmount     = round($taxableAmount * $vatRate / 100, 2);
        $totalAmount   = round($taxableAmount + $vatAmount, 2);

        return [
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmount,
            'taxable_amount'  => $taxableAmount,
            'vat_amount'      => $vatAmount,
            'total_amount'    => $totalAmount,
        ];
    }

    /**
     * Generate a payment plan based on the contract's payment method.
     *
     * - full: single installment due at start_date + payment_terms_days
     * - installments: split equally across installments_count periods
     */
    public function generatePaymentPlan(Contract $contract): void
    {
        $totalAmount   = (float) $contract->total_amount;
        $paymentMethod = $contract->payment_method ?? 'full';
        $termsDays     = (int) ($contract->payment_terms_days ?? 30);
        $startDate     = $contract->start_date
            ? \Carbon\Carbon::parse($contract->start_date)
            : now();

        if ($paymentMethod === 'full' || ($contract->installments_count ?? 1) <= 1) {
            // Single payment
            ContractPaymentPlan::create([
                'contract_id'        => $contract->id,
                'installment_number' => 1,
                'amount'             => $totalAmount,
                'due_date'           => $startDate->copy()->addDays($termsDays),
                'status'             => PaymentPlanStatus::UPCOMING->value,
            ]);
        } else {
            // Multiple installments — split equally
            $count          = (int) $contract->installments_count;
            $perInstallment = round($totalAmount / $count, 2);
            $remainder      = round($totalAmount - ($perInstallment * $count), 2);

            for ($i = 1; $i <= $count; $i++) {
                $amount = $perInstallment;

                // Add any rounding remainder to the last installment
                if ($i === $count && $remainder != 0) {
                    $amount = round($amount + $remainder, 2);
                }

                // Each installment due date spaced evenly (30-day intervals after initial terms)
                $intervalDays = $termsDays + (($i - 1) * 30);

                ContractPaymentPlan::create([
                    'contract_id'        => $contract->id,
                    'installment_number' => $i,
                    'amount'             => $amount,
                    'due_date'           => $startDate->copy()->addDays($intervalDays),
                    'status'             => PaymentPlanStatus::UPCOMING->value,
                ]);
            }
        }
    }

    /**
     * Generate invoices from the contract's payment plan entries.
     * Each installment becomes one invoice linked via morph.
     */
    public function generateInvoices(Contract $contract): void
    {
        $installments = $contract->paymentPlan()
            ->orderBy('installment_number')
            ->get();

        if ($installments->isEmpty()) {
            // If no payment plan exists, generate one first
            $this->generatePaymentPlan($contract);
            $installments = $contract->paymentPlan()
                ->orderBy('installment_number')
                ->get();
        }

        $primaryParty = $contract->parties()->where('is_primary', true)->first()
            ?? $contract->parties()->first();

        foreach ($installments as $installment) {
            // Skip if invoice already exists for this installment
            if ($installment->invoice_id) {
                continue;
            }

            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad(
                (string) (Invoice::count() + 1), 5, '0', STR_PAD_LEFT
            );

            // Calculate tax proportionally
            $vatRate   = (float) ($contract->vat_rate ?? 15);
            $subtotal  = round($installment->amount / (1 + $vatRate / 100), 2);
            $taxAmount = round($installment->amount - $subtotal, 2);

            $invoice = Invoice::create([
                'invoice_number'   => $invoiceNumber,
                'user_id'          => $primaryParty->user_id ?? $contract->created_by,
                'invoiceable_type' => Contract::class,
                'invoiceable_id'   => $contract->id,
                'title'            => "Invoice for {$contract->contract_number} — Installment {$installment->installment_number}",
                'title_ar'         => "فاتورة عقد {$contract->contract_number} — القسط {$installment->installment_number}",
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'discount_amount'  => 0,
                'total_amount'     => $installment->amount,
                'paid_amount'      => 0,
                'status'           => 'issued',
                'issue_date'       => now()->toDateString(),
                'due_date'         => $installment->due_date,
                'items'            => [
                    [
                        'description'    => $contract->title ?? "Contract {$contract->contract_number}",
                        'description_ar' => $contract->title_ar ?? "عقد {$contract->contract_number}",
                        'quantity'       => 1,
                        'unit_price'     => $installment->amount,
                        'total'          => $installment->amount,
                    ],
                ],
                'notes'      => "Auto-generated from contract {$contract->contract_number}, installment #{$installment->installment_number}",
                'created_by' => $contract->created_by,
            ]);

            // Link invoice to installment
            $installment->update(['invoice_id' => $invoice->id]);
        }
    }

    /**
     * Record a payment against a specific installment.
     * Updates installment status, contract paid_amount, and payment_status.
     */
    public function recordPayment(Contract $contract, int $installmentNumber, float $amount): void
    {
        DB::transaction(function () use ($contract, $installmentNumber, $amount) {
            $installment = $contract->paymentPlan()
                ->where('installment_number', $installmentNumber)
                ->firstOrFail();

            $newPaidAmount     = round((float) $installment->paid_amount + $amount, 2);
            $installmentAmount = (float) $installment->amount;

            if ($newPaidAmount > $installmentAmount) {
                throw new LogicException(
                    "Payment of {$amount} would exceed installment amount. " .
                    "Installment total: {$installmentAmount}, already paid: {$installment->paid_amount}."
                );
            }

            // Determine installment status
            if ($newPaidAmount >= $installmentAmount) {
                $installmentStatus = PaymentPlanStatus::PAID->value;
            } elseif ($newPaidAmount > 0) {
                $installmentStatus = PaymentPlanStatus::PARTIAL->value;
            } else {
                $installmentStatus = PaymentPlanStatus::UPCOMING->value;
            }

            $installment->update([
                'paid_amount' => $newPaidAmount,
                'paid_at'     => $newPaidAmount >= $installmentAmount ? now() : $installment->paid_at,
                'status'      => $installmentStatus,
            ]);

            // Update contract totals
            $totalPaid           = (float) $contract->paymentPlan()->sum('paid_amount');
            $totalContractAmount = (float) $contract->total_amount;

            if ($totalPaid >= $totalContractAmount) {
                $paymentStatus = 'paid';
            } elseif ($totalPaid > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'unpaid';
            }

            $contract->update([
                'paid_amount'    => $totalPaid,
                'payment_status' => $paymentStatus,
            ]);
        });
    }

    /**
     * Calculate late-payment penalty for an overdue installment.
     * Formula: penalty_rate% * amount * overdue_days
     */
    public function calculatePenalty(ContractPaymentPlan $installment): float
    {
        $contract    = $installment->contract;
        $penaltyRate = (float) ($contract->penalty_rate ?? 0);

        if ($penaltyRate <= 0) {
            return 0;
        }

        $dueDate = \Carbon\Carbon::parse($installment->due_date);
        if ($dueDate->isFuture()) {
            return 0; // Not overdue
        }

        $overdueDays = $dueDate->diffInDays(now());
        $amount      = (float) $installment->amount;

        // penalty_rate is a daily percentage
        return round($penaltyRate / 100 * $amount * $overdueDays, 2);
    }

    // =========================================================================
    // VERSIONING
    // =========================================================================

    /**
     * Create a new version snapshot of the contract.
     */
    private function createVersion(
        Contract $contract,
        string $userId,
        ?string $changeSummary = null,
        ?array $changedFields = null
    ): ContractVersion {
        $latestVersion = $contract->versions()
            ->orderByDesc('version_number')
            ->first();

        $nextVersion = $latestVersion ? $latestVersion->version_number + 1 : 1;

        return ContractVersion::create([
            'contract_id'    => $contract->id,
            'version_number' => $nextVersion,
            'content_snapshot' => $contract->toArray(),
            'change_summary' => $changeSummary,
            'changed_fields' => $changedFields,
            'created_by'     => $userId,
        ]);
    }

    // =========================================================================
    // LOGGING
    // =========================================================================

    /**
     * Record a status change / lifecycle event in the contract status log.
     */
    private function logStatusChange(
        Contract $contract,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        string $userId,
        ?array $metadata = null
    ): void {
        ContractStatusLog::create([
            'contract_id'  => $contract->id,
            'action'       => $action,
            'from_status'  => $fromStatus,
            'to_status'    => $toStatus,
            'performed_by' => $userId,
            'metadata'     => $metadata,
            'ip_address'   => request()?->ip(),
            'user_agent'   => request()?->userAgent(),
        ]);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Generate a unique contract number: CT-YYYY-00001
     */
    private function generateContractNumber(): string
    {
        $year   = now()->format('Y');
        $prefix = "CT-{$year}-";

        $lastContract = Contract::where('contract_number', 'like', "{$prefix}%")
            ->orderByDesc('contract_number')
            ->first();

        if ($lastContract) {
            $lastSeq = (int) Str::afterLast($lastContract->contract_number, '-');
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Assert the contract is in one of the allowed statuses.
     * Throws LogicException if not.
     *
     * @param Contract $contract
     * @param UnifiedContractStatus[] $allowedStatuses
     */
    private function assertStatus(Contract $contract, array $allowedStatuses): void
    {
        $currentStatus = $contract->status instanceof UnifiedContractStatus
            ? $contract->status
            : UnifiedContractStatus::tryFrom($contract->status);

        $allowed = false;
        foreach ($allowedStatuses as $status) {
            if ($currentStatus === $status) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            $allowedNames = implode(', ', array_map(fn ($s) => $s->value, $allowedStatuses));
            $current      = $currentStatus?->value ?? $contract->status;
            throw new LogicException(
                "Cannot perform this action. Contract status is [{$current}], " .
                "but must be one of [{$allowedNames}]."
            );
        }
    }

    /**
     * Validate transition is allowed based on the defined state machine.
     * Throws LogicException for invalid transitions.
     */
    private function validateTransition(Contract $contract, UnifiedContractStatus $targetStatus): void
    {
        $transitions = [
            UnifiedContractStatus::DRAFT->value => [
                UnifiedContractStatus::UNDER_REVIEW->value,
                UnifiedContractStatus::CANCELLED->value,
            ],
            UnifiedContractStatus::UNDER_REVIEW->value => [
                UnifiedContractStatus::APPROVED->value,
                UnifiedContractStatus::REJECTED->value,
                UnifiedContractStatus::DRAFT->value,       // changes requested
                UnifiedContractStatus::CANCELLED->value,
            ],
            UnifiedContractStatus::APPROVED->value => [
                UnifiedContractStatus::SENT_FOR_SIGNATURE->value,
                UnifiedContractStatus::DRAFT->value,       // changes requested
                UnifiedContractStatus::CANCELLED->value,
            ],
            UnifiedContractStatus::SENT_FOR_SIGNATURE->value => [
                UnifiedContractStatus::SIGNED->value,
            ],
            UnifiedContractStatus::SIGNED->value => [
                UnifiedContractStatus::ACTIVE->value,
            ],
            UnifiedContractStatus::ACTIVE->value => [
                UnifiedContractStatus::SUSPENDED->value,
                UnifiedContractStatus::COMPLETED->value,
                UnifiedContractStatus::TERMINATED->value,
            ],
            UnifiedContractStatus::SUSPENDED->value => [
                UnifiedContractStatus::ACTIVE->value,
                UnifiedContractStatus::TERMINATED->value,
            ],
            // Terminal states — no outgoing transitions
            UnifiedContractStatus::COMPLETED->value  => [],
            UnifiedContractStatus::CANCELLED->value  => [],
            UnifiedContractStatus::TERMINATED->value => [],
            UnifiedContractStatus::REJECTED->value   => [],
        ];

        $currentValue = $contract->status instanceof UnifiedContractStatus
            ? $contract->status->value
            : $contract->status;

        $allowed = $transitions[$currentValue] ?? [];

        if (!in_array($targetStatus->value, $allowed, true)) {
            throw new LogicException(
                "Invalid status transition from [{$currentValue}] to [{$targetStatus->value}]. " .
                'Allowed targets: [' . implode(', ', $allowed) . '].'
            );
        }
    }

    /**
     * Validate that all required fields are filled before submission.
     */
    private function validateRequiredFields(Contract $contract): void
    {
        $errors = [];

        if (empty($contract->title) && empty($contract->title_ar)) {
            $errors[] = 'Contract must have a title (English or Arabic).';
        }
        if (empty($contract->type)) {
            $errors[] = 'Contract type is required.';
        }
        if (empty($contract->start_date)) {
            $errors[] = 'Start date is required.';
        }
        if (empty($contract->end_date)) {
            $errors[] = 'End date is required.';
        }
        if ($contract->start_date && $contract->end_date && $contract->start_date > $contract->end_date) {
            $errors[] = 'End date must be after start date.';
        }
        if ((float) $contract->total_amount <= 0) {
            $errors[] = 'Total amount must be greater than zero.';
        }
        if ($contract->parties()->count() === 0) {
            $errors[] = 'Contract must have at least one party.';
        }

        if (!empty($errors)) {
            throw new InvalidArgumentException(
                'Contract cannot be submitted for review: ' . implode(' ', $errors)
            );
        }
    }

    /**
     * Calculate early termination penalty.
     * Based on remaining contract value and penalty_rate.
     */
    private function calculateTerminationPenalty(Contract $contract): float
    {
        $penaltyRate = (float) ($contract->penalty_rate ?? 0);
        if ($penaltyRate <= 0) {
            return 0;
        }

        $totalAmount = (float) $contract->total_amount;
        $paidAmount  = (float) $contract->paid_amount;
        $remaining   = max($totalAmount - $paidAmount, 0);

        // Penalty is penalty_rate% of the remaining unpaid amount
        return round($remaining * $penaltyRate / 100, 2);
    }

    // =========================================================================
    // REPORTING / QUERIES
    // =========================================================================

    /**
     * Get aggregate statistics about contracts.
     *
     * @return array{by_status: array, total_value: float, total_paid: float, active_count: int, total_count: int}
     */
    public function getStats(): array
    {
        $byStatus = Contract::selectRaw('status, COUNT(*) as count, SUM(total_amount) as total_value')
            ->groupBy('status')
            ->get()
            ->keyBy('status')
            ->map(fn ($row) => [
                'count'       => (int) $row->count,
                'total_value' => (float) $row->total_value,
            ])
            ->toArray();

        $totals = Contract::selectRaw(
            'COUNT(*) as total_count, ' .
            'COALESCE(SUM(total_amount), 0) as total_value, ' .
            'COALESCE(SUM(paid_amount), 0) as total_paid'
        )->first();

        $activeCount = Contract::where('status', UnifiedContractStatus::ACTIVE->value)->count();

        return [
            'by_status'    => $byStatus,
            'total_value'  => (float) $totals->total_value,
            'total_paid'   => (float) $totals->total_paid,
            'active_count' => $activeCount,
            'total_count'  => (int) $totals->total_count,
        ];
    }

    /**
     * Get contracts grouped by status for a pipeline/kanban view.
     *
     * @return array<string, array{contracts: \Illuminate\Support\Collection, count: int, total_value: float}>
     */
    public function getPipeline(): array
    {
        $pipeline = [];

        $statuses  = UnifiedContractStatus::cases();
        $contracts = Contract::with(['parties'])
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy(fn ($c) => $c->status instanceof UnifiedContractStatus ? $c->status->value : $c->status);

        foreach ($statuses as $status) {
            $group = $contracts->get($status->value, collect());
            $pipeline[$status->value] = [
                'label'       => $status->label(),
                'contracts'   => $group->values(),
                'count'       => $group->count(),
                'total_value' => (float) $group->sum('total_amount'),
            ];
        }

        return $pipeline;
    }

    /**
     * Get contracts expiring within the given number of days.
     */
    public function getExpiringContracts(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Contract::where('status', UnifiedContractStatus::ACTIVE->value)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString(),
            ])
            ->orderBy('end_date')
            ->with(['parties'])
            ->get();
    }
}
