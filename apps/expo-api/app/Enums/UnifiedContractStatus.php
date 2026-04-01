<?php

namespace App\Enums;

enum UnifiedContractStatus: string
{
    case DRAFT = 'draft';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case SENT_FOR_SIGNATURE = 'sent_for_signature';
    case SIGNED = 'signed';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';
    case TERMINATED = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'مسودة',
            self::UNDER_REVIEW => 'قيد المراجعة',
            self::APPROVED => 'معتمد',
            self::SENT_FOR_SIGNATURE => 'مرسل للتوقيع',
            self::SIGNED => 'موقّع',
            self::ACTIVE => 'نشط',
            self::SUSPENDED => 'معلّق',
            self::COMPLETED => 'مكتمل',
            self::CANCELLED => 'ملغي',
            self::REJECTED => 'مرفوض',
            self::TERMINATED => 'مُنهى',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::UNDER_REVIEW => 'Under Review',
            self::APPROVED => 'Approved',
            self::SENT_FOR_SIGNATURE => 'Sent for Signature',
            self::SIGNED => 'Signed',
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REJECTED => 'Rejected',
            self::TERMINATED => 'Terminated',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::UNDER_REVIEW => 'yellow',
            self::APPROVED => 'blue',
            self::SENT_FOR_SIGNATURE => 'indigo',
            self::SIGNED => 'purple',
            self::ACTIVE => 'green',
            self::SUSPENDED => 'orange',
            self::COMPLETED => 'teal',
            self::CANCELLED => 'red',
            self::REJECTED => 'rose',
            self::TERMINATED => 'slate',
        };
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::DRAFT]);
    }

    public function canBeReviewed(): bool
    {
        return $this === self::UNDER_REVIEW;
    }

    public function canBeSentForSignature(): bool
    {
        return $this === self::APPROVED;
    }

    public function canBeSigned(): bool
    {
        return $this === self::SENT_FOR_SIGNATURE;
    }

    public function canBeActivated(): bool
    {
        return $this === self::SIGNED;
    }

    public function canBeSuspended(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this, [self::ACTIVE, self::SUSPENDED]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::DRAFT, self::UNDER_REVIEW, self::APPROVED]);
    }

    public function canBeTerminated(): bool
    {
        return in_array($this, [self::ACTIVE, self::SUSPENDED]);
    }

    /**
     * Get the list of statuses this status can transition to.
     */
    public function getAllowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::UNDER_REVIEW, self::CANCELLED],
            self::UNDER_REVIEW => [self::APPROVED, self::REJECTED, self::CANCELLED],
            self::APPROVED => [self::SENT_FOR_SIGNATURE, self::CANCELLED],
            self::SENT_FOR_SIGNATURE => [self::SIGNED, self::CANCELLED],
            self::SIGNED => [self::ACTIVE],
            self::ACTIVE => [self::SUSPENDED, self::COMPLETED, self::TERMINATED],
            self::SUSPENDED => [self::ACTIVE, self::COMPLETED, self::TERMINATED],
            self::COMPLETED => [],
            self::CANCELLED => [],
            self::REJECTED => [self::DRAFT],
            self::TERMINATED => [],
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
