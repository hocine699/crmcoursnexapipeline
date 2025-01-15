<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage ProductCategory'))
        {
            if(\Auth::user()->type == 'owner'){

                $product_categorys = ProductCategory::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $product_categorys = ProductCategory::where('created_by', \Auth::user()->id)->get();

            }
            return view('product_category.index', compact('product_categorys'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create ProductCategory'))
        {
            return view('product_category.create');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('Create ProductCategory'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $name                  = $request['name'];
            $productcategory       = new ProductCategory();
            $productcategory->name = $name;
            $productcategory['created_by']  = \Auth::user()->creatorId();
            $productcategory->save();

            return redirect()->route('product_category.index')->with('success', 'Product Category' . $productcategory->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProductCategory $productCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ProductCategory $productCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategory $productCategory)
    {
        if(\Auth::user()->can('Edit ProductCategory'))
        {
            return view('product_category.edit', compact('productCategory'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ProductCategory $productCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        if(\Auth::user()->can('Edit ProductCategory'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $productCategory['name'] = $request->name;
            $productCategory['created_by']  = \Auth::user()->creatorId();
            $productCategory->update();

            return redirect()->route('product_category.index')->with(
                'success', 'Product Category ' . $productCategory->name . ' updated!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ProductCategory $productCategory
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCategory $productCategory)
    {
        if(\Auth::user()->can('Delete ProductCategory'))
        {
            $productCategory->delete();

            return redirect()->route('product_category.index')->with(
                'success', 'Product Category ' . $productCategory->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
