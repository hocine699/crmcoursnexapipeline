<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id',
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
        'is_converted',
        'created_by',
        'description',
    ];

    protected $appends = [
        'status_name',
        'account_name',
        'source_name',
        'campaign_name',
    ];

    public static $status = [
        'New',
        'Assigned',
        'In Process',
        'Converted',
        'Recycled',
        'Dead',
    ];
    private static $account_name = null;
    private static $campaign_name = null;

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function accountIndustry()
    {
        return $this->hasOne('App\Models\AccountIndustry', 'id', 'industry');
    }

    public function LeadSource()
    {
        return $this->hasOne('App\Models\LeadSource', 'id', 'source');
    }

    public function campaigns()
    {
        return $this->hasOne('App\Models\Campaign', 'id', 'campaign');
    }

    public function accounts()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

    public static function leads($id)
    {
        return Lead::where('status', '=', $id)->get();
    }

    public function getStatusNameAttribute()
    {
        $status = Lead::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

        public  function getAccountNameAttribute()
        {
            if (self::$account_name === null) {
                self::$account_name = self::fetchgetAccountNameAttribute();
            }

            return self::$account_name;
        }
        public  function fetchgetAccountNameAttribute()
        {
            $account = Lead::find($this->account);
            return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
        }

        public  function getCampaignNameAttribute()
        {
            if (self::$campaign_name === null) {
                self::$campaign_name = self::fetchgetCampaignNameAttribute();
            }

            return self::$campaign_name;
        }
        public function fetchgetCampaignNameAttribute()
        {
            $campaign = Lead::find($this->campaign);

            return $this->attributes['campaign_name'] = !empty($campaign) ? $campaign->name : '';
        }

    public function getSourceNameAttribute()
    {
        $lead_source = Lead::find($this->source);

        return $this->attributes['source_name'] = !empty($lead_source) ? $lead_source->name : '';
    }

    public function stages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }


}
