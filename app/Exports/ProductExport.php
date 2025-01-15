<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\ProductBrand	;
use App\Models\ProductTax	;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=Product::all()->where('created_by', \Auth::user()->creatorId());

        foreach($data as $k => $product)
        {
        	$user_id=User::find($product->user_id);
        	$user=!empty($user_id)?$user_id->name:'';

        	$categorys=ProductCategory::find($product->category);
        	$category=$categorys->name??'';

        	$brands=ProductBrand::find($product->brand);
        	$brand=$brands->name??'';

        	$tax=Product::Taxdata($product->tax);

        	$created_bys=User::find($product->created_by);
        	$created_by=$created_bys->username;

        	$data[$k]["status"]=Product::$status[$product->status];
        	$data[$k]["user_id"]=$user;
        	$data[$k]["category"]=$category;
        	$data[$k]["brand"]=$brand;
        	$data[$k]["tax"]=$tax;
        	$data[$k]["created_by"]=$created_by;
        }

        return $data;
    }

     public function headings(): array
    {
        return [
            "Quote ID",
            "User",
            "Name",
            "Status",
            "Category",
            "Brand",
            "Price",
            "Tax",
            "Part_Number",
            "Weight",
            "URL",
             "SKU",
            "Description",
            "Created_By",
            "Created_At",
            "Updated_At",
        ];
    }
}
