<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'name', 'email', 'phone', 'passwordHash', 'loginMethod', 'role',
        'status', 'avatar', 'company', 'commercialRegister', 'vatNumber',
        'kycStatus', 'language', 'department', 'departmentId', 'isActive',
        'activityType', 'region',
    ];
    protected $hidden = ['passwordHash', 'twoFactorSecret'];
    protected $casts = ['isActive' => 'boolean', 'twoFactorEnabled' => 'boolean'];

    public function getAuthPassword() { return $this->passwordHash; }
    public function notifications(): HasMany { return $this->hasMany(Notification::class, 'userId'); }
    public function investorProfile(): HasOne { return $this->hasOne(InvestorProfile::class, 'userId'); }
    public function wallet(): HasOne { return $this->hasOne(Wallet::class, 'userId'); }
}
