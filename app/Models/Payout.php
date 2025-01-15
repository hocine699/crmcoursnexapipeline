<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'company_id',
        'request_date',
        'status',
        'requested_amount',
        'created_by',        
    ];

     public static $statuesColor = [
        'In Progress' => 'warning',
        'Rejected' => 'danger',
        'Approved' => 'success',
    ];

    public static $status = [
        'In Progress',
        'Rejected',
        'Approved',
    ];

    public function company_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'company_id');
    }
}
