<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\HasRolesAndPermissions;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRolesAndPermissions;

    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'status', 'avatar', 'metadata'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() {
        return ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone];
    }

    public function isActive(): bool { return $this->status === 'active'; }

    public function updateLastLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function getFullInfoAttribute()
    {
        $role = 'user';
        $roleCode = 0;
        $permissions = [];

        // Get role from HasRolesAndPermissions trait if available
        try {
            if (method_exists($this, 'roles') && $this->roles?->count() > 0) {
                $role = $this->roles->first()->name ?? 'user';
            }
            if (method_exists($this, 'permissions') && $this->permissions?->count() > 0) {
                $permissions = $this->permissions->pluck('name')->toArray();
            }
        } catch (\Exception $e) {
            // If eager loading failed, ignore
        }

        // Map role name to role code
        $roleCode = match($role) {
            'super_admin' => 1989,
            'department_manager' => 6060,
            'staff' => 5050,
            'admin' => 1989,
            default => 0,
        };

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'role' => $role,
            'role_code' => $roleCode,
            'permissions' => $permissions,
            'status' => $this->status,
        ];
    }
}
