<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name', 'nameAr', 'description', 'isSystem',
    ];

    protected $casts = [
        'isSystem' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot(['assigned_by', 'expires_at']);
    }

    public function givePermissionTo(...$permissions): self
    {
        $permissionIds = $this->resolvePermissions($permissions);
        $this->permissions()->syncWithoutDetaching($permissionIds);
        return $this;
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    protected function resolvePermissions(array $permissions): array
    {
        return collect($permissions)->flatten()->map(function ($p) {
            if ($p instanceof Permission) return $p->id;
            $found = Permission::where('name', $p)->first();
            return $found ? $found->id : null;
        })->filter()->toArray();
    }

    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}
