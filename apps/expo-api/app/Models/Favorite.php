<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favorite extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeEvents($query)
    {
        return $query->where('favoritable_type', Event::class);
    }

    public function scopeSpaces($query)
    {
        return $query->where('favoritable_type', Space::class);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function toggle(string $userId, string $type, string $id): array
    {
        $modelClass = match($type) {
            'event' => Event::class,
            'space' => Space::class,
            default => null,
        };

        if (!$modelClass) {
            return ['added' => false, 'error' => 'Invalid type'];
        }

        $favorite = self::where('user_id', $userId)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return ['added' => false, 'message' => 'Removed from favorites'];
        }

        self::create([
            'user_id' => $userId,
            'favoritable_type' => $modelClass,
            'favoritable_id' => $id,
        ]);

        return ['added' => true, 'message' => 'Added to favorites'];
    }

    public static function isFavorited(string $userId, string $type, string $id): bool
    {
        $modelClass = match($type) {
            'event' => Event::class,
            'space' => Space::class,
            default => null,
        };

        if (!$modelClass) {
            return false;
        }

        return self::where('user_id', $userId)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $id)
            ->exists();
    }
}
