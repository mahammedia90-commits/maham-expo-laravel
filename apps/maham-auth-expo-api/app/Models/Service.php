<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'token',
        'secret',
        'allowed_ips',
        'allowed_permissions',
        'webhook_url',
        'status',
        'last_used_at',
    ];

    protected $hidden = [
        'token',
        'secret',
    ];

    protected $casts = [
        'allowed_ips' => 'array',
        'allowed_permissions' => 'array',
        'last_used_at' => 'datetime',
    ];

    /* ========================================
     * Boot
     * ======================================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->token)) {
                $service->token = self::generateToken();
            }
            if (empty($service->secret)) {
                $service->secret = self::generateSecret();
            }
        });
    }

    /* ========================================
     * Relationships
     * ======================================== */

    public function logs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'service_roles')
            ->withTimestamps();
    }

    /* ========================================
     * Token Methods
     * ======================================== */

    public static function generateToken(): string
    {
        return 'srv_' . Str::random(40);
    }

    public static function generateSecret(): string
    {
        return hash('sha256', Str::random(64));
    }

    public function regenerateToken(): string
    {
        $this->token = self::generateToken();
        $this->save();
        
        return $this->token;
    }

    public function regenerateSecret(): string
    {
        $this->secret = self::generateSecret();
        $this->save();
        
        return $this->secret;
    }

    /* ========================================
     * Role Methods
     * ======================================== */

    /**
     * إضافة أدوار للخدمة
     */
    public function assignRoles(array $roles): void
    {
        $roleIds = [];
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_string($role)) {
                $roleModel = Role::findByName($role);
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            } else {
                $roleIds[] = $role;
            }
        }
        $this->roles()->syncWithoutDetaching($roleIds);
    }

    /**
     * إزالة أدوار من الخدمة
     */
    public function removeRoles(array $roles): void
    {
        $roleIds = [];
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_string($role)) {
                $roleModel = Role::findByName($role);
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            } else {
                $roleIds[] = $role;
            }
        }
        $this->roles()->detach($roleIds);
    }

    /**
     * مزامنة أدوار الخدمة (استبدال الكل)
     */
    public function syncRoles(array $roles): void
    {
        $roleIds = [];
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_string($role)) {
                $roleModel = Role::findByName($role);
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            } else {
                $roleIds[] = $role;
            }
        }
        $this->roles()->sync($roleIds);
    }

    /**
     * التحقق من أن الخدمة لديها أدوار محددة
     */
    public function hasRoles(): bool
    {
        return $this->roles()->count() > 0;
    }

    /**
     * التحقق من أن المستخدم لديه دور مسموح للخدمة
     */
    public function canUserAccess(User $user): bool
    {
        // إذا الخدمة ما عندها أدوار محددة، الكل يقدر يدخل
        if (!$this->hasRoles()) {
            return true;
        }

        // التحقق من أن المستخدم لديه أحد الأدوار المسموحة
        $serviceRoleIds = $this->roles()->pluck('roles.id')->toArray();
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();

        return !empty(array_intersect($serviceRoleIds, $userRoleIds));
    }

    /* ========================================
     * Validation Methods
     * ======================================== */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isIpAllowed(string $ip): bool
    {
        if (empty($this->allowed_ips)) {
            return true;
        }

        foreach ($this->allowed_ips as $allowedIp) {
            if ($this->ipMatches($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    public function canCheckPermission(string $permission): bool
    {
        if (empty($this->allowed_permissions)) {
            return true;
        }

        foreach ($this->allowed_permissions as $allowed) {
            // دعم wildcards مثل users.*
            if ($allowed === '*' || $permission === $allowed) {
                return true;
            }
            
            if (Str::endsWith($allowed, '.*')) {
                $prefix = Str::beforeLast($allowed, '.*');
                if (Str::startsWith($permission, $prefix . '.')) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function ipMatches(string $ip, string $pattern): bool
    {
        // دعم CIDR notation
        if (Str::contains($pattern, '/')) {
            return $this->ipInCidr($ip, $pattern);
        }
        
        return $ip === $pattern;
    }

    protected function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);
        
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        
        return ($ip & $mask) == $subnet;
    }

    /* ========================================
     * Logging Methods
     * ======================================== */

    public function logUsage(string $action, array $data = []): ServiceLog
    {
        $this->update(['last_used_at' => now()]);

        return $this->logs()->create([
            'action' => $action,
            'user_id' => $data['user_id'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'request_data' => $data['request'] ?? null,
            'response_data' => $data['response'] ?? null,
            'response_time' => $data['response_time'] ?? null,
            'success' => $data['success'] ?? true,
            'error_message' => $data['error'] ?? null,
        ]);
    }

    /* ========================================
     * Scopes
     * ======================================== */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }

    /* ========================================
     * Static Methods
     * ======================================== */

    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)->first();
    }

    public static function validateToken(string $token): ?self
    {
        $service = static::findByToken($token);
        
        if (!$service || !$service->isActive()) {
            return null;
        }
        
        return $service;
    }
}
