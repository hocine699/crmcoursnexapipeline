<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetList extends Model
{
    protected     $fillable = [
        'name',
        'created_by',
    ];
}
