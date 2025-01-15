<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected     $fillable = [
        'user_id',
        'name',
        'status',
        'direction',
        'start_date',
        'end_date',
        'parent',
        'parent_id',
        'account',
        'description',
        'attendees_user',
        'attendees_contact',
        'attendees_lead',
    ];

    protected $appends = [
        'direction_name',
        'status_name',
        'parent_name',
    ];

    public static $status   = [
        'Planned',
        'Held',
        'Not Held',
    ];
    public static $direction = [
        'Outbound',
        'Inbound',
    ];
    public static $parent   = [
        '' => '--',
        'account' => 'Account',
        'lead' => 'Lead',
        'contact' => 'Contact',
        'opportunities' => 'Opportunities',
        'case' => 'Case',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function attendees_contacts()
    {
        return $this->hasOne('App\Models\Contact', 'id', 'attendees_contact');
    }
    public function attendees_users()
    {
        return $this->hasOne('App\Models\User', 'id', 'attendees_user');
    }

    public function attendees_leads()
    {
        return $this->hasOne('App\Models\Lead', 'id', 'attendees_lead');
    }

    public function getparent($type, $id)
    {
        if($type == 'account')
        {
            $parent = Account::find($id)->name;
        }
        elseif($type == 'lead')
        {
            $parent = Lead::find($id)->name;
        }
        elseif($type == 'contact')
        {
            $parent = Contact::find($id)->name;
        }
        elseif($type == 'opportunities')
        {
            $parent = Opportunities::find($id)->name;
        }
        elseif($type == 'case')
        {
            $parent = CommonCase::find($id)->name;
        }else{
            $parent = '';
        }

        return $parent;
    }

    public function getStatusNameAttribute()
    {
        $status = Call::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getParentNameAttribute()
    {
        $parent = Call::$parent[$this->parent];

        return $this->attributes['Parent_name'] = $parent;
    }

    public function getDirectionNameAttribute()
    {
        $direction = Call::$direction[$this->direction];

        return $this->attributes['Parent_name'] = $direction;
    }

}
