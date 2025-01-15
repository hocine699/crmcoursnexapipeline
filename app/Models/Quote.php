<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable   = [
        'user_id',
        'name',
        'Opportunity',
        'status',
        'account',
        'amount',
        'date_quoted',
        'quote_number',
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
        'billing_contact',
        'shipping_contact',
        'tax',
        'shipping _provider',
        'description',
        'created_by',
        'converted_salesorder_id',
    ];
    public static $status   = [
        'Open',
        // 'Not Paid',
        // 'Partialy Paid',
        // 'Paid',
        'Cancelled',
    ];
    protected $appends    = [
        'opportunity_name',
        'account_name',

    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function accounts()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

    public function taxs()
    {
        return $this->hasOne('App\Models\ProductTax', 'id', 'tax');
    }

    public function opportunitys()
    {
        return $this->hasOne('App\Models\Opportunities', 'id', 'opportunity');
    }

    public function contacts()
    {
        return $this->hasOne('App\Models\Contact', 'id', 'billing_contact');
    }

    public function shipping_providers()
    {
        return $this->hasOne('App\Models\ShippingProvider', 'id', 'shipping_provider');
    }

    public function getaccount($type, $id)
    {
        $parent = Account::find($id)->name;

        return $parent;
    }

    public function itemsdata()
    {
        return $this->hasMany('App\Models\QuoteItem', 'quote_id', 'id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->itemsdata as $product)
        {
            $subTotal += ($product->price * $product->quantity);
        }

        return $subTotal;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach($this->itemsdata as $product)
        {
            $taxes = Utility::totalTaxRate($product->tax);

            $totalTax += ($taxes / 100) * ($product->price * $product->quantity);
        }

        return $totalTax;
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->itemsdata as $product)
        {
            $totalDiscount += $product->discount;
        }

        return $totalDiscount;
    }

    public function getTotal()
    {
        return ($this->getSubTotal() + $this->getTotalTax()) - $this->getTotalDiscount();
    }


    public function getOpportunityNameAttribute()
    {
        $opportunity = Quote::find($this->opportunity);

        return $this->attributes['opportunity_name'] = !empty($opportunity) ? $opportunity->name : '';
    }

    public function getAccountNameAttribute()
    {
        $account = Quote::find($this->account);

        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = Quote::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public static function Tax($tax)
    {

       $taxArr = explode(',', $tax);

        $tax = 0;
        foreach($taxArr as $tax)
        {
            $taxs = ProductTax::find($tax);
            $taxname= !empty($taxs) ? $taxs->tax_name: 0;
        }


        return $taxname;


    }


}
