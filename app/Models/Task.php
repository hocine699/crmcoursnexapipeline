<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'stage',
        'priority',
        'start_date',
        'due_date',
        'parent',
        'parent_id',
        'account',
        'description',
        'attachments',
    ];

    protected $appends = [
        'user_id_name',

    ];

    public static $priority = [
        0 => 'Low',
        1 => 'Normal',
        2 => 'High',
        3 => 'Urgent',
    ];
    public static $parent   = [
        '' => '--',
        'Account' => 'Account',
        'Lead' => 'Lead',
        'Contact' => 'Contact',
        'Opportunities' => 'Opportunities',
        'Case' => 'Case',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function stages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }

    public function attendees_contacts()
    {
        return $this->hasOne('App\Models\Contact', 'id', 'attendees_contact');
    }

    public function attendees_leads()
    {
        return $this->hasOne('App\Models\Lead', 'id', 'attendees_lead');
    }

    public function getparent($type, $id)
    {
        if($type == 'Account')
        {
            $parent = Account::find($id)->name;
        }
        elseif($type == 'Lead')
        {
            $parent = Lead::find($id)->name;
        }
        elseif($type == 'Contact')
        {
            $parent = Contact::find($id)->name;
        }
        elseif($type == 'Opportunities')
        {
            $parent = Opportunities::find($id)->name;
        }
        elseif($type == 'Case')
        {
            $parent = CommonCase::find($id)->name;
        }
        else
        {
            $parent = '';
        }

        return $parent;
    }

    public function getUserIdNameAttribute()
    {
        $user_id = Task::find($this->user_id);

        return $this->attributes['user_id_name'] = !empty($user_id) ? $user_id->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = Task::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getPriorityNameAttribute()
    {
        $priority = Task::$priority[$this->priority];

        return $this->attributes['priority_name'] = $priority;
    }

    public function getParentNameAttribute()
    {
        $parent = Task::$parent[$this->parent];

        return $this->attributes['Parent_name'] = $parent;
    }

     public static function parents($parent)
    {

        if($parent == 'Account')
        {
            $parent = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($parent == 'Lead')
        {
            $parent = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($parent == 'Contact')
        {
            $parent = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($parent == 'Opportunities')
        {
            $parent = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($parent == 'Case')
        {
            $parent = CommonCase::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        else
        {
            $parent = '';
        }

        return $parent;
    }
}
