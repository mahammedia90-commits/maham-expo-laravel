<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorLead extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'leads';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['sponsorId','eventId','companyName','contactName','email','phone','industry','interestLevel','source','status','score'];
}
