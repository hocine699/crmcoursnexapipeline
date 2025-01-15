<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonCase extends Model
{
    // protected $table = 'common_cases';


    protected $fillable = [
        'user_id',
        'name',
        'number',
        'status',
        'account',
        'priority',
        'contact',
        'type',
        'description',
        'attachments',
        'created_by',
    ];
    protected $appends  = [
        'account_name',
        'status_name',
        'priority_name',
        'contact_name',

    ];

    public static $status   = [
        'New',
        'Assigned',
        'Pending',
        'Closed',
        'Rejected',
        'Duplicate',
    ];
    public static $priority = [
        0 => 'Low',
        1 => 'Normal',
        2 => 'High',
        3 => 'Urgent',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function accounts()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

    public function contacts()
    {
        return $this->hasOne('App\Models\Contact', 'id', 'contact');
    }

    public function types()
    {
        return $this->hasOne('App\Models\CaseType', 'id', 'type');
    }

    public function getAccountNameAttribute()
    {
        $account = CommonCase::find($this->account);

        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = CommonCase::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getPriorityNameAttribute()
    {
        $priority = CommonCase::$priority[$this->priority];

        return $this->attributes['priority_name'] = $priority;
    }

    public function getContactNameAttribute()
    {
        $contact = CommonCase::find($this->contact);

        return $this->attributes['contact_name'] = !empty($contact) ? $contact->name : '';
    }

    public function stages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }


}
