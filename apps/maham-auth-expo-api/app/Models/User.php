<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\HasRolesAndPermissions;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRolesAndPermissions;

    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'name', 'email', 'phone', 'role', 'loginMethod', 'openId',
        'lastSignedIn', 'isActive', 'status',
    ];

    protected $casts = ['isActive' => 'boolean'];

    public function isActive(): bool { return (bool) ($this->isActive ?? true); }
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() {
        return ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'role' => $this->role];
    }

    public function updateLastLogin(?string $ip = null): void
    {
        $this->lastSignedIn = now();
        $this->save();
    }
}
