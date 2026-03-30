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
        'openId', 'name', 'email', 'loginMethod', 'role',
        'phone', 'passwordHash', 'status', 'avatar', 'company',
        'commercialRegister', 'vatNumber', 'kycStatus',
        'twoFactorEnabled', 'twoFactorSecret', 'language',
        'department', 'allowedSections', 'departmentId', 'lastSignedIn',
    ];
    protected $hidden = ['passwordHash', 'twoFactorSecret'];
    protected $casts = ['twoFactorEnabled' => 'boolean'];

    public function getAuthPassword() { return $this->passwordHash; }
    public function notifications(): HasMany { return $this->hasMany(Notification::class, 'userId'); }
    public function investorProfile(): HasOne { return $this->hasOne(InvestorProfile::class, 'userId'); }
    public function wallet(): HasOne { return $this->hasOne(Wallet::class, 'userId'); }
}
