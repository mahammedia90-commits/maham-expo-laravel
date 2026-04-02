<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'file_path',
        'file_size',
        'mime_type',
        'status',
        'uploaded_by',
        'related_id',
        'related_type',
    ];
}
