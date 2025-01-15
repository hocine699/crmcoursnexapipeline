<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\In;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'name',
        'salesorder',
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
        'opportunity_name',
        'account_name',
        'salesorder_name',
        'quote_name',

    ];



    public static $statuesColor = [
        'Open' => 'primary',
        'Not Paid' => 'danger',
        'Partialy Paid' => 'warning',
        'Paid' => 'success',
        'Cancelled' => 'info',
    ];



    public static $status = [
        'Open',
        'Not Paid',
        'Partialy Paid',
        'Paid',
        'Cancelled',
    ];
    private static $account_name = null;

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
        return $this->hasMany('App\Models\InvoiceItem', 'invoice_id', 'id');
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
        $opportunity = Invoice::find($this->opportunity);

        return $this->attributes['opportunity_name'] = !empty($opportunity) ? $opportunity->name : '';
    }
    public  function getAccountNameAttribute()
    {
        if (self::$account_name === null) {
            self::$account_name = self::fetchgetAccountNameAttribute();
        }

        return self::$account_name;
    }
    public function fetchgetAccountNameAttribute()
    {
        $account = Invoice::find($this->account);

        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function getQuoteNameAttribute()
    {
        $quote = Invoice::find($this->quote);

        return $this->attributes['account_name'] = !empty($quote) ? $quote->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = Invoice::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getSalesorderNameAttribute()
    {
        $salesorder = Invoice::find($this->salesorder);

        return $this->attributes['account_name'] = !empty($salesorder) ? $salesorder->name : '';
    }

    public function getDue()
    {
        $due = 0;
        foreach($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return ($this->getTotal() - $due);
    }

    public function payments()
    {
        return $this->hasMany('App\Models\InvoicePayment', 'invoice_id', 'id');
    }

    public static function change_status($invoice_id, $status)
    {

        $invoice         = Invoice::find($invoice_id);
        $invoice->status = $status;
        $invoice->update();
    }

   public static function Users($user_id)
    {


            $users = User::find($user_id);

            $username=$users->name;

            return $username;


    }

     public static function quote($quote)
    {


        $quoteArr = explode(',', $quote);

        $quote = 0;
        foreach($quoteArr as $quote)
        {

            $quotes = Quote::find($quote);


            $quotename=$quotes->name;
        }


        return $quotename;

    }

    public static function salesorder($salesorder)
    {


        $salesorderArr = explode(',', $salesorder);

        $SalesOrder = 0;
        foreach($salesorderArr as $salesorder)
        {

            $salesorders = SalesOrder::find($salesorder);


            $salesordername=$salesorders->name;
        }


        return $salesordername;

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

    public static function Tax($tax)
    {

       $taxArr = explode(',', $tax);

        $tax = 0;
        foreach($taxArr as $tax)
        {
            $taxs = ProductTax::find($tax);
            $taxname=$taxs->tax_name ?? 0;
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
