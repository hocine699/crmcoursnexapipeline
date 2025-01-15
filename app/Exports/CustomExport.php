<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Opportunities;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomExport implements FromCollection ,WithHeadings
{
     /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $data = Report::all()->where('created_by',Auth::user()->creatorId());
        foreach($data as $c => $custom)
        {
            $user_id = User::find($custom->user_id);
            $user = $user_id->username;
            $opportunitys = Opportunities::find($custom->opportunity);
            $opportunitys =! empty($opportunitys) ? $opportunitys -> name:'';

            $accounts = Account::find($custom->account);
            $accounts =! empty($accounts) ? $accounts ->name:'';

            $created_bys = User::find($custom->created_by);
            $created_by = $created_bys->username;

            $data[$c]["user_id"] = $user;
            $data[$c]["opportunity"] = $opportunitys;
            $data[$c]["account"] = $accounts;
        }
        return $data;
    }

    public function headings():array
    {
        return[
        'id',
        'user_id',
        'name',
        'entity_type',
        'group_by',
        'chart_type',
        'created_by',
        "Created_At",
        "Updated_At",
        ];
    }
}
