<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $fillable = [
        'is_referral_enabled',
        'commission_per',
        'minimum_amount',
        'guidelines',        
        'created_by',
    ];
}
