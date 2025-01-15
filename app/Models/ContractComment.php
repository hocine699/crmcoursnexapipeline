<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_id',
        'user_id',
        'comment',
        'created_by',
    ];

    public function client()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
