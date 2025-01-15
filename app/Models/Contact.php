<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Account;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'account_id',
        'email',
        'phone',
        'contact_address',
        'contact_city',
        'contact_state',
        'contact_country',
        'contact_postalcode',
        'description',
        'customer_signature',
    ];

    protected $appends = ['account_name'];
    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function assign_account()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

    public function getAccountNameAttribute()
    {
        $account = Contact::find($this->account);

        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function stages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }
}
