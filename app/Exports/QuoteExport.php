<?php

namespace App\Exports;

use App\Models\Quote;
use App\Models\User;
use App\Models\Opportunities;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ProductTax;
use App\Models\ShippingProvider;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuoteExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       $data=Quote::all()->except(['status_name'])->where('created_by', \Auth::user()->creatorId());


         foreach($data as $k => $quote)
        {
  			$user_id=User::find($quote->user_id);
  			$user=$user_id->username;

  			$opportunitys=Opportunities::find($quote->opportunity);
            $opportunity=!empty($opportunitys)?$opportunitys->name:'';

  			$accounts=Account::find($quote->account);
  			$account=!empty($accounts)?$accounts->name:'';

  			if($quote->billing_contacts!=0)
  			{
  				$billing_contacts=Contact::find($quote->billing_contact);
  				$billing_contact=$billing_contacts->name;
  			}
  			else
  			{
  				$billing_contact=0;
  			}

  			if($quote->shipping_contact!=0)
  			{
  				$shipping_contacts=Contact::find($quote->shipping_contact);
  				$shipping_contact=$shipping_contacts->name;
  			}
  			else
  			{
  				$shipping_contact=0;
  			}

  			$tax=Quote::Tax($quote->tax);

  			$shipping_providers=ShippingProvider::find($quote->shipping_provider);
  			$shipping_provider=$shipping_providers->name;

  			$created_bys=User::find($quote->created_by);
  			$created_by=$created_bys->username;


  			$data[$k]["status"]=Quote::$status[$quote->status];
  			$data[$k]["user_id"]=$user;
  			$data[$k]["opportunity"]=$opportunity;
  			$data[$k]["account"]=$account;
  			$data[$k]["billing_contact"]=$billing_contact;
  			$data[$k]["shipping_contact"]=$shipping_contact;
  			$data[$k]["tax"]=$tax;
  			$data[$k]["shipping_provider"]=$shipping_provider;
  		}
  		return $data;
    }


     public function headings(): array
    {
        return [
            "Quote ID",
            "User",
            "Name",
            "Quote",
            "Opportunity",
            "Status",
            "Account",
            "Amount",
            "Date_Quoted",
            "Quote_Number",
            "Billing_Address",
             "Billing_City",
            "Billing_State",
            "Billing_Country",
            "Billing_Postalcode",
            "Shipping_Address",
            "Shipping_City",
            "Shipping_State",
            "Shipping_Country",
             "Shipping_Postalcode",
             "billing_contact",
            "Shipping_Contact",
            "Tax",
            "Shipping_Provider",
            "Description",
            "converted_salesorder_id",
            "Created_By",
            "Created_At",
            "Updated_At",
        ];
    }
}
