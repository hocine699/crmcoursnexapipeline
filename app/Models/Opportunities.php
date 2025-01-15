<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunities extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'account_name',
        'stage',
        'amount',
        'probability',
        'close_date',
        'contacts',
        'lead_source',
        'created_by',
        'description',
    ];
    protected $appends  = [
        'contact_name',
        'account_name',
        'campaign_name',
        'stage_name',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function stages()
    {
        return $this->hasOne('App\Models\OpportunitiesStage', 'id', 'stage');
    }

    public function accounts()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

    public function campaign_ids()
    {
        return $this->hasOne('App\Models\Campaign', 'id', 'campaign_id');
    }

    public function leadsource()
    {
        return $this->hasOne('App\Models\LeadSource', 'id', 'lead_source');
    }

    public function contacts()
    {
        return $this->hasOne('App\Models\Contact', 'id', 'contact');
    }

    public function opportunities()
    {
        return $this->hasOne('App\Models\Document', 'id', 'opportunities');
    }

    public function getAccountNameAttribute()
    {
        $account = Opportunities::find($this->account);


        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function getContactNameAttribute()
    {
        $contact = Opportunities::find($this->contact);

        return $this->attributes['contact_name'] = !empty($contact) ? $contact->name : '';
    }

    public function getCampaignNameAttribute()
    {
        $campaign = Opportunities::find($this->campaign);

        return $this->attributes['campaign_name'] = !empty($campaign) ? $campaign->name : '';
    }

    public function getStageNameAttribute()
    {
        $stage = OpportunitiesStage::find($this->stage);

        return $this->attributes['stage_name'] = !empty($stage) ? $stage->name : '';
    }

    public function taskstages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }
}

