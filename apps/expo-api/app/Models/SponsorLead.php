<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorLead extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'bp_leads';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['leadType','brandName','companyName','ownerName','ownerPhone','ownerEmail','assignedTo','salesAssignedTo','status','qualificationNotes','rejectionNotes','internalNotes','leadSource','budgetRange','priority','expectedRevenue'];
}
