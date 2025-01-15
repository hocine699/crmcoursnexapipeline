<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'category',
        'brand',
        'price',
        'tax',
        'part_number',
        'weight',
        'URL',
        'sku',
        'created_by',
        'description',
    ];

    protected $appends = [
        'brand_name',
        'category_name',
    ];

    public static $status = [
        'Available',
        'Unavailable',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function brands()
    {
        return $this->hasOne('App\Models\ProductBrand', 'id', 'brand');
    }

    public function taxs()
    {
        return $this->hasOne('App\Models\ProductTax', 'id', 'tax');
    }

    public function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes  = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = ProductTax::find($tax);
        }

        return $taxes;
    }

    public function categorys()
    {
        return $this->hasOne('App\Models\ProductCategory', 'id', 'category');
    }

    public function getBrandNameAttribute()
    {
        $brand = ProductBrand::find($this->brand);

        return $this->attributes['brand_name'] = !empty($brand) ? $brand->name : '';
    }

    public function getCategoryNameAttribute()
    {
        $category = ProductCategory::find($this->category);

        return $this->attributes['category_name'] = !empty($category) ? $category->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = Product::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public static function Taxdata($tax)
    {
        if(!empty($taxArr)){ 
            $taxArr = explode(',', $tax);
             $tax = 0;
               foreach($taxArr as $tax)
            {
                $taxs = ProductTax::find($tax);
                $taxname=$taxs->tax_name;
            }
            return $taxname;
        }
    }

}
