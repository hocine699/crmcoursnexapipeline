<?php

namespace App\Exports;

use App\Models\SalesOrder;
use App\Models\Quote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;


class SalesOrderExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=SalesOrder::all()->except(['status_name'])->where('created_by', \Auth::user()->creatorId());

        foreach($data as $k => $salesorder)
        {

        	unset($salesorder->id);


        	$user=SalesOrder::Users($salesorder->user_id);
            $quotes=Quote::find($salesorder->quote);
        	$quote=$quotes->name;
            $opportunity=SalesOrder::opportunity($salesorder->opportunity);

            $account=SalesOrder::account($salesorder->account);
            if($salesorder->billing_contact!=0)
            {
                $billing_contact=SalesOrder::contact($salesorder->billing_contact);
            }
            else
            {
                $billing_contact=0;
            }
             if($salesorder->shipping_contact!=0)
            {
                 $shipping_contact=SalesOrder::contact($salesorder->shipping_contact);
            }
            else
            {
                $shipping_contact=0;
            }


            // $tax=SalesOrder::Tax($salesorder->tax);
            $shippingprovider=SalesOrder::shippingprovider($salesorder->shipping_provider);
            $created_by=SalesOrder::Users($salesorder->created_by);

            $data[$k]["user_id"]=$user;
            $data[$k]["quote"]=$quote;
            $data[$k]["opportunity"]=$opportunity;

            $data[$k]["status"]=SalesOrder::$status[$salesorder->status];
            $data[$k]["Amount"]= Auth::user()->priceFormat($salesorder->getTotal());
            $data[$k]["account"]=$account;
            $data[$k]["billing_contact"]=$billing_contact;
            $data[$k]["shipping_contact"]=$shipping_contact;
            // $data[$k]["tax"]= $tax;
            $data[$k]["shipping_provider"]=$shippingprovider;
            $data[$k]["created_by"]=$created_by;

        }

        return $data;

    }

    public function headings(): array
    {
        return [
            "SalesOrder ID",
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
            // "Tax",
            "Shipping_Provider",
            "Description",
            "Created_By",
            "Created_At",
            "Updated_At",
        ];
    }

}
