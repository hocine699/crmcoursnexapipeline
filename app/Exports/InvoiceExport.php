<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
         $data=Invoice::all()->except(['status_name'])->where('created_by', \Auth::user()->creatorId());

         foreach($data as $k => $invoice)
        {
             unset($invoice->quote_id,$invoice->invoice_id);



        	$user=User::where('id',$invoice->user_id)->first();
            $users=!empty($user)?$user->name:'';



            if($invoice->quote!=0)
            {
                $quotes=Invoice::quote($invoice->quote);
            }
            else
            {
                $quotes=0;
            }

        	if($invoice->salesorder!=0)
        	{

        		$salesorder=Invoice::salesorder($invoice->salesorder);
        	}
        	else
        	{

        		$salesorder=0;
        	}

            if($invoice->opportunity!=0)
            {

                $opportunity=Invoice::opportunity($invoice->opportunity);
            }
            else
            {

                $opportunity=0;
            }


            if($invoice->account!=0)
            {
                $account=Invoice::account($invoice->account);
            }
            else
            {
                $account=0;
            }
             $created_bys=User::find($invoice->created_by);
             $created_by=$created_bys->username;

             if($invoice->billing_contact!=0)
            {
                $billing_contact=Invoice::contact($invoice->billing_contact);
            }
            else
            {
                $billing_contact=0;
            }
             if($invoice->shipping_contact!=0)
            {
                 $shipping_contact=Invoice::contact($invoice->shipping_contact);
            }
            else
            {
                $shipping_contact=0;
            }
            if($invoice->shipping_provider!=0)
            {
                 $shippingprovider=Invoice::shippingprovider($invoice->shipping_provider);
            }
            else
            {
                $shippingprovider=0;
            }
            $tax=Invoice::Tax($invoice->tax);




             $data[$k]["user_id"]=$users;
             $data[$k]["quote"]=$quotes;
             $data[$k]["salesorder"]=$salesorder;
             $data[$k]["status"]=Invoice::$status[$invoice->status];
             $data[$k]["account"]=$account;
             $data[$k]["opportunity"]=$opportunity;
             $data[$k]["billing_contact"]=$billing_contact;
             $data[$k]["shipping_provider"]=$shippingprovider;
             $data[$k]["tax"]=$tax;
             $data[$k]["created_by"]=$created_by;
        }

        return $data;
    }


    public function headings(): array
    {
        return [
            "Invoice ID",
            "User",
            "Name",
            "Salesorder",
            "Quote",
            "Opportunity",
            "Status",
            "Account",
            "Amount",
            "date_quoted",
            "quote_number",
             "billing_address",
            "billing_city",
            "billing_state",
            "billing_country",
            "billing_postalcode",
            "shipping_address",
            "shipping_city",
            "Shipping_State",
             "Shipping_Country",
             "Shipping_Postalcode",
             "Billing_Contact",
             "Shipping_Contact",
            "tax",
            "Shipping_Provider",

            "Description",
            "Created_By",
            "Created_At",
            "Updated_At",
        ];
    }
}
