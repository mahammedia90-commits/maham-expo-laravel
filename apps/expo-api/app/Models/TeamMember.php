<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'team_members';
    public $timestamps = false;
}
