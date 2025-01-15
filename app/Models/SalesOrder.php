<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SalesOrder extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'quote',
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
    ];

    protected $appends = [
        'user_id_name',
        'quote_name',
        'opportunity_name',

    ];

    public static $status   = [
        'Open',
        // 'Not Paid',
        // 'Partialy Paid',
        // 'Paid',
        'Cancelled',
    ];


    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }




    public function accounts()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

    public function quotes()
    {

        return $this->hasOne('App\Models\Quote', 'id', 'quote');
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
        return $this->hasMany('App\Models\SalesOrderItem', 'salesorder_id', 'id');
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

    public function getUserIdNameAttribute()
    {
        $user_id = SalesOrder::find($this->user_id);

        return $this->attributes['user_id_name'] = !empty($user_id) ? $user_id->name : '';
    }

    public function getQuoteNameAttribute()
    {
        $quote = SalesOrder::find($this->quote_id);

        return $this->attributes['quote_name'] = !empty($quote) ? $quote->name : '';
    }

    public function getOpportunityNameAttribute()
    {
        $opportunity = SalesOrder::find($this->opportunity);

        return $this->attributes['opportunity_name'] = !empty($opportunity) ? $opportunity->name : '';
    }

    public static function statuss($status)
    {
        if($status==0)
        {
            $status='Open';
        }
        elseif($status==1)
        {
            $status='Not Paid';
        }
         elseif($status==2)
        {
            $status='Partialy Paid';
        }
         elseif($status==3)
        {
            $status='Paid';
        }
         elseif($status==4)
        {
            $status='Cancelled';
        }


        return $status;

    }

    public function getStatusNameAttribute()
    {

        $status = SalesOrder::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public static function Users($user_id)
    {

        $userArr = explode(',', $user_id);

        $user = 0;
        foreach($userArr as $user)
        {
            $users = User::find($user);

            // $username=$users->name;
            $username= !empty($users) ? $users->name: 0;
        }


        return $username;

    }

     public static function opportunity($opportunity)
    {
        $opportunityArr = explode(',', $opportunity);

        $opportunity = 0;
        foreach($opportunityArr as $opportunity)
        {
            $opportunitys = Opportunities::find($opportunity);
            $opportunityname=$opportunitys->name;
        }


        return $opportunityname;

    }

     public static function account($account)
    {
       $accountArr = explode(',', $account);

        $account = 0;
        foreach($accountArr as $account)
        {
            $accounts = Opportunities::find($account);

            $accountname=$accounts->name;
        }


        return $accountname;


    }
     public static function contact($contact)
    {
       $contactArr = explode(',', $contact);

        $contact = 0;
        foreach($contactArr as $contact)
        {
            $contacts = Contact::find($contact);


            $contactname=$contacts->name;
        }


        return $contactname;


    }
    public static function Tax($tax)
    {

       $taxArr = explode(',', $tax);

        $tax = 0;
        foreach($taxArr as $tax)
        {
            $taxs = ProductTax::find($tax);


            $taxname=$taxs->tax_name;
        }


        return $taxname;


    }

    public static function shippingprovider($shippingprovider)
    {

       $shippingproviderArr = explode(',', $shippingprovider);

        $shippingprovider = 0;
        foreach($shippingproviderArr as $shippingprovider)
        {
            $shippingproviders = ShippingProvider::find($shippingprovider);



            $shippingprovidername=$shippingproviders->name;
        }


        return $shippingprovidername;


    }



}
