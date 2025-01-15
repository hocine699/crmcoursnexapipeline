<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    protected $fillable = [
        'user_id',
        'log_type',
        'file_upload',
        'remark',
        'created_by',
    ];
}
