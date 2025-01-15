<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'name',
        'email',
        'phone',
        'website',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postalcode',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postalcode',
        'type',
        'industry',
        'description',
    ];

    protected $appends = [
        'type_name',
        'industry_name',
    ];

    public function accountType()
    {
        return $this->hasOne('App\Models\AccountType', 'id', 'type');
    }

    public function accountIndustry()
    {
        return $this->hasOne('App\Models\AccountIndustry', 'id', 'industry');
    }

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function document_id()
    {
        return $this->hasOne('App\Models\Document', 'id', 'name');
    }

    public function getTypeNameAttribute()
    {
        $type = Account::find($this->type);

        return $this->attributes['type_name'] = !empty($type) ? $type->name : '';
    }

    public function getIndustryNameAttribute()
    {
        $type = Account::find($this->industry);

        return $this->attributes['industry_name'] = !empty($type) ? $type->name : '';
    }

    public function stages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }
}
