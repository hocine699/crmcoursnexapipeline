<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpportunitiesStage extends Model
{
    protected     $fillable = [
        'name',
        'created_by',
        ];
//    public function opportunity()
//    {
//
//        return $this->hasMany('App\Opportunities', 'stage', 'id');
//    }

    public function opportunity($id)
    {
        return Opportunities::where('stage', '=', $id)->get();
    }
}
