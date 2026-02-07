<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
        'level',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'level' => 'integer',
    ];

    /* ========================================
     * Relationships
     * ======================================== */

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot(['assigned_by', 'expires_at'])
            ->withTimestamps();
    }

    /* ========================================
     * Permission Methods
     * ======================================== */

    public function givePermissionTo(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        $this->permissions()->syncWithoutDetaching($permissionIds);
        $this->clearPermissionCache();
        
        return $this;
    }

    public function revokePermissionTo(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        $this->permissions()->detach($permissionIds);
        $this->clearPermissionCache();
        
        return $this;
    }

    public function syncPermissions(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        $this->permissions()->sync($permissionIds);
        $this->clearPermissionCache();
        
        return $this;
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->permissions()
            ->where('name', $permission)
            ->exists();
    }

    /* ========================================
     * Helper Methods
     * ======================================== */

    protected function resolvePermissions(array $permissions): array
    {
        $flatPermissions = collect($permissions)->flatten();
        
        return $flatPermissions->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }
            
            $found = Permission::where('name', $permission)->first();
            return $found ? $found->id : null;
        })->filter()->toArray();
    }

    protected function clearPermissionCache(): void
    {
        $cacheKey = config('auth-service.cache.prefix') . 'role_permissions_' . $this->id;
        Cache::forget($cacheKey);
        
        // مسح كاش المستخدمين المرتبطين بهذا الدور
        $this->users->each(function ($user) {
            $userCacheKey = config('auth-service.cache.prefix') . 'user_permissions_' . $user->id;
            Cache::forget($userCacheKey);
        });
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeByLevel($query, int $maxLevel)
    {
        return $query->where('level', '<=', $maxLevel);
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
        return static::firstOrCreate(
            ['name' => $name],
            array_merge([
                'display_name' => ucfirst(str_replace(['-', '_'], ' ', $name)),
            ], $attributes)
        );
    }
}
