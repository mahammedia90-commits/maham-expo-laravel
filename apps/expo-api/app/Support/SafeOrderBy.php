<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

/**
 * Provides safe orderBy functionality to prevent SQL injection
 * through user-supplied sort_by and sort_order parameters.
 */
trait SafeOrderBy
{
    /**
     * Apply safe ordering to query from request parameters.
     * Only allows whitelisted column names and validates sort direction.
     * Accepts both Builder and Relation (HasMany, BelongsToMany, etc.)
     */
    protected function applySafeOrder(
        Builder|Relation $query,
        Request $request,
        array $allowedColumns,
        string $defaultColumn = 'created_at',
        string $defaultOrder = 'desc'
    ): Builder|Relation {
        $sortBy = $request->input('sort_by', $defaultColumn);
        $sortOrder = strtolower($request->input('sort_order', $defaultOrder));

        // Whitelist columns
        if (!in_array($sortBy, $allowedColumns, true)) {
            $sortBy = $defaultColumn;
        }

        // Validate sort direction
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = $defaultOrder;
        }

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Sanitize search input - limit length and remove dangerous characters.
     */
    protected function sanitizeSearch(?string $search, int $maxLength = 100): ?string
    {
        if ($search === null || $search === '') {
            return null;
        }

        // Limit length
        $search = mb_substr($search, 0, $maxLength);

        // Remove wildcard characters that could affect LIKE performance
        $search = str_replace(['%', '_'], ['\\%', '\\_'], $search);

        return $search;
    }
}
