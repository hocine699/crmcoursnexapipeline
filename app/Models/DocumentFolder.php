<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentFolder extends Model
{
    protected $fillable = [
        'name',
        'parent',
        'description',
        'created_by',
    ];

    public function children()
	{   
		return $this->hasMany(DocumentFolder::class,'parent','id');
	}
	public function parent()
	{   
		return $this->belongsTo(DocumentFolder::class,'parent','id');
 	}

}
