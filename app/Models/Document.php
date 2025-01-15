<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected     $fillable = [
        'user_id',
        'name',
        'Folder',
        'type',
        'status',
        'publish_date',
        'expiration_date',
        'description',
        'attachments',
    ];
    public static $status = [
        'Active',
        'Draft',
        'Expired',
        'Canceled',
    ];
    public function types()
    {
        return $this->hasOne('App\Models\DocumentType', 'id', 'type');
    }
    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function opportunitys()
    {
        return $this->hasOne('App\Models\Opportunities', 'id', 'opportunities');
    }
    public function accounts()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account');
    }

}
