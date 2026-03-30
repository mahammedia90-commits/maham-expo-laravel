<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'sys_dict_data';
    public $timestamps = false;
    protected $fillable = ['dictType','dictLabel','dictValue','dictSort','status'];
}
