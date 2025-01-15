<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'status',
        'budget',
        'start_date',
        'end_date',
        'target_lists',
        'excludingtarget_lists ',
        'description',
    ];

    protected $appends = [
        'type_name',
        'status_name',
    ];

    public static $status = [
        'Planning',
        'Active',
        'Inactive',
        'Complete',
    ];

    public function types()
    {
        return $this->hasOne('App\Models\CampaignType', 'id', 'type');
    }

    public function target_lists()
    {
        return $this->hasOne('App\Models\TargetList', 'id', 'target_list');
    }

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


    public function getStatusNameAttribute()
    {
        $status = Campaign::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getTypeNameAttribute()
    {
        $type = Campaign::find($this->type);

        return $this->attributes['type_name'] = !empty($type) ? $type->name : '';
    }


}
