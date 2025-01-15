<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    private static $emailTemplate = null;
    protected $fillable = [
        'name',
        'from',
        'slug',
        'created_by',
    ];

    public function template()
    {
        return $this->hasOne('App\Models\UserEmailTemplate', 'template_id', 'id')->where('user_id', '=', \Auth::user()->id);
    }

    public static function getemailtemplate()
    {   
        if (self::$emailTemplate === null) {

            $emailTemplate     = EmailTemplate::first();
            self::$emailTemplate = $emailTemplate;
        }

        return self::$emailTemplate;
    }
}
