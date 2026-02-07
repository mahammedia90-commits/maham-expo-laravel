<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'group',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->withPivot(['is_granted', 'assigned_by', 'expires_at'])
            ->withTimestamps();
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    public static function findOrCreate(string $name, array $attributes = []): self
    {
        // استخراج المجموعة من الاسم
        $group = $attributes['group'] ?? self::extractGroup($name);
        
        return static::firstOrCreate(
            ['name' => $name],
            array_merge([
                'display_name' => self::generateDisplayName($name),
                'group' => $group,
            ], $attributes)
        );
    }

    public static function createForResource(string $resource, array $actions = ['view', 'create', 'update', 'delete']): array
    {
        $permissions = [];
        
        foreach ($actions as $action) {
            $permissions[] = static::findOrCreate("{$resource}.{$action}", [
                'group' => $resource,
            ]);
        }
        
        return $permissions;
    }

    /* ========================================
     * Helper Methods
     * ======================================== */

    protected static function extractGroup(string $name): string
    {
        $parts = explode('.', $name);
        return $parts[0] ?? 'general';
    }

    protected static function generateDisplayName(string $name): string
    {
        // users.create -> Create Users
        $parts = explode('.', $name);
        
        if (count($parts) === 2) {
            return ucfirst($parts[1]) . ' ' . ucfirst($parts[0]);
        }
        
        return ucfirst(str_replace(['.', '-', '_'], ' ', $name));
    }

    /* ========================================
     * Accessors
     * ======================================== */

    public function getActionAttribute(): ?string
    {
        $parts = explode('.', $this->name);
        return $parts[1] ?? null;
    }

    public function getResourceAttribute(): ?string
    {
        $parts = explode('.', $this->name);
        return $parts[0] ?? null;
    }
}
