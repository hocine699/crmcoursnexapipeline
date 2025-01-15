<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Lead;
use App\Models\User;
use App\Models\Account;
use App\Models\Opportunities;

class LeadExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=Lead::all()->except(['status_name'])->where('created_by', \Auth::user()->creatorId());

        foreach($data as $k => $lead)
        {

             $user_id=User::find($lead->user_id);
             $user=!empty($user_id->username) ?$user_id->username : '';

             $accounts=Account::find($lead->account);
             $account=!empty($accounts)?$accounts->name:'';

             $created_bys=User::find($lead->created_by);
             $created_by=!empty($created_bys->username) ?  $created_bys->username : '';


             $data[$k]["user_id"]=$user;
             $data[$k]["account"]=$account;

         }
         return $data;
    }

    public function headings(): array
    {
        return [
            "Lead ID",
            "User_name",
            "Name",
            "Account",
            "Email",
            "Phone",
            "Title",
            "Website",
            "lead_address",
            "lead_city",
            "lead_state",
            "lead_country",
            "lead_postalcode",
            "Status",
            "Source",
            "Opportunity_amount",
            "Campaign",
            "Industry",
            "is_converted",
            "Description",
            "Created_by",
            "Created_at",
            "Updated_at"
        ];
    }
}
