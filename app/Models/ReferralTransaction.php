<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralTransaction extends Model
{
    protected $fillable = [
        'comapny_id',
        'plan_id',
        'plan_price',
        'commission',        
        'amount',
        'referral_code',
        'ref_user_id'
    ];

    public function company_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'comapny_id');
    }
    public function plan_name()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }
    public function ref_company_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'ref_user_id');
    }
}
