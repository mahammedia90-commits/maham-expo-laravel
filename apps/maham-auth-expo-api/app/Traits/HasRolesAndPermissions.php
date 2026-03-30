<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait HasRolesAndPermissions
{
    /* ========================================
     * Relationships
     * ======================================== */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['assigned_by', 'expires_at'])
            ;
    }

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot(['is_granted', 'assigned_by', 'expires_at'])
            ;
    }

    /* ========================================
     * Role Methods
     * ======================================== */

    public function assignRole(...$roles): self
    {
        $roleIds = $this->resolveRoles($roles);
        
        foreach ($roleIds as $roleId) {
            $this->roles()->syncWithoutDetaching([
                $roleId => ['assigned_by' => auth()->id()]
            ]);
        }
        
        $this->clearPermissionCache();
        
        return $this;
    }

    public function removeRole(...$roles): self
    {
        $roleIds = $this->resolveRoles($roles);
        $this->roles()->detach($roleIds);
        $this->clearPermissionCache();
        
        return $this;
    }

    public function syncRoles(...$roles): self
    {
        $roleIds = $this->resolveRoles($roles);
        
        $syncData = [];
        foreach ($roleIds as $roleId) {
            $syncData[$roleId] = ['assigned_by' => auth()->id()];
        }
        
        $this->roles()->sync($syncData);
        $this->clearPermissionCache();
        
        return $this;
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        
        if (is_array($role)) {
            return $this->roles->whereIn('name', $role)->isNotEmpty();
        }
        
        return $this->roles->contains($role);
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles->pluck('name');
        
        foreach ($roles as $role) {
            if (!$userRoles->contains($role)) {
                return false;
            }
        }
        
        return true;
    }

    /* ========================================
     * Permission Methods
     * ======================================== */

    public function givePermissionTo(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        
        foreach ($permissionIds as $permissionId) {
            $this->directPermissions()->syncWithoutDetaching([
                $permissionId => [
                    'is_granted' => true,
                    'assigned_by' => auth()->id()
                ]
            ]);
        }
        
        $this->clearPermissionCache();
        
        return $this;
    }

    public function revokePermissionTo(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        $this->directPermissions()->detach($permissionIds);
        $this->clearPermissionCache();
        
        return $this;
    }

    public function denyPermissionTo(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        
        foreach ($permissionIds as $permissionId) {
            $this->directPermissions()->syncWithoutDetaching([
                $permissionId => [
                    'is_granted' => false,
                    'assigned_by' => auth()->id()
                ]
            ]);
        }
        
        $this->clearPermissionCache();
        
        return $this;
    }

    public function hasPermissionTo(string $permission): bool
    {
        // فحص super-admin أولاً
        if ($this->hasRole('super-admin')) {
            return true;
        }

        // فحص الصلاحيات المرفوضة مباشرة
        $deniedPermission = $this->directPermissions()
            ->where('name', $permission)
            ->wherePivot('is_granted', false)
            ->first();
        
        if ($deniedPermission) {
            return false;
        }

        // فحص الصلاحيات المباشرة
        $directPermission = $this->directPermissions()
            ->where('name', $permission)
            ->wherePivot('is_granted', true)
            ->first();
        
        if ($directPermission) {
            return true;
        }

        // فحص صلاحيات الأدوار
        return $this->hasPermissionViaRole($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }
        
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermissionTo($permission)) {
                return false;
            }
        }
        
        return true;
    }

    protected function hasPermissionViaRole(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    /* ========================================
     * Get All Permissions
     * ======================================== */

    public function getAllPermissions(): Collection
    {
        $cacheKey = config('auth-service.cache.prefix', 'auth_') . 'user_permissions_' . $this->id;
        $ttl = config('auth-service.cache.ttl.user_permissions', 1800);

        if (!config('auth-service.cache.enabled', true)) {
            return $this->computeAllPermissions();
        }

        try {
            return Cache::remember($cacheKey, $ttl, function () {
                return $this->computeAllPermissions();
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Cache unavailable for permissions, computing directly', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return $this->computeAllPermissions();
        }
    }

    protected function computeAllPermissions(): Collection
    {
        // صلاحيات الأدوار
        $rolePermissions = $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');

        // الصلاحيات المباشرة (الممنوحة)
        $grantedPermissions = $this->directPermissions()
            ->wherePivot('is_granted', true)
            ->get();

        // الصلاحيات المرفوضة
        $deniedPermissions = $this->directPermissions()
            ->wherePivot('is_granted', false)
            ->pluck('id');

        // دمج الصلاحيات واستبعاد المرفوضة
        return $rolePermissions
            ->merge($grantedPermissions)
            ->unique('id')
            ->reject(function ($permission) use ($deniedPermissions) {
                return $deniedPermissions->contains($permission->id);
            })
            ->pluck('name')
            ->values();
    }

    public function getPermissionsGrouped(): array
    {
        return $this->getAllPermissions()
            ->groupBy(function ($permission) {
                return explode('.', $permission)[0] ?? 'general';
            })
            ->toArray();
    }

    /* ========================================
     * Helper Methods
     * ======================================== */

    protected function resolveRoles(array $roles): array
    {
        $flatRoles = collect($roles)->flatten();
        
        return $flatRoles->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            
            $found = Role::where('name', $role)->first();
            return $found ? $found->id : null;
        })->filter()->toArray();
    }

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
        try {
            $cacheKey = config('auth-service.cache.prefix', 'auth_') . 'user_permissions_' . $this->id;
            Cache::forget($cacheKey);
        } catch (\Throwable $e) {
            // Cache unavailable — permission cache will expire naturally
        }
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    public function scopePermission($query, string $permission)
    {
        return $query->where(function ($q) use ($permission) {
            // صلاحية مباشرة
            $q->whereHas('directPermissions', function ($subQ) use ($permission) {
                $subQ->where('name', $permission)
                    ->wherePivot('is_granted', true);
            })
            // أو عبر دور
            ->orWhereHas('roles.permissions', function ($subQ) use ($permission) {
                $subQ->where('name', $permission);
            });
        });
    }
}
