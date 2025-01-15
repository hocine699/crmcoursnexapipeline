<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormFieldResponse extends Model
{
    protected $fillable = [
        'form_id',
        'name_id',
        'email_id',
        'phone_id',
        'address_id',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'user_id',
        'description_id',
        'type',
    ];
}
