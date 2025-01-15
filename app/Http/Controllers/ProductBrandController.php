<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage ProductBrand'))
        {
            if(\Auth::user()->type == 'owner'){

                $product_brands = ProductBrand::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $product_brands = ProductBrand::where('created_by', \Auth::user()->id)->get();

            }
            return view('product_brand.index', compact('product_brands'));
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
        if(\Auth::user()->can('Create ProductBrand'))
        {
            return view('product_brand.create');
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
        if(\Auth::user()->can('Create ProductBrand'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $name               = $request['name'];
            $productbrand       = new ProductBrand();
            $productbrand->name = $name;
            $productbrand['created_by']  = \Auth::user()->creatorId();
            $productbrand->save();

            return redirect()->route('product_brand.index')->with('success', 'Product Brand' . $productbrand->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProductBrand $productBrand
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductBrand $productBrand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ProductBrand $productBrand
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductBrand $productBrand)
    {
        if(\Auth::user()->can('Edit ProductBrand'))
        {
            return view('product_brand.edit', compact('productBrand'));
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
     * @param \App\ProductBrand $productBrand
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductBrand $productBrand)
    {
        if(\Auth::user()->can('Edit ProductBrand'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $productBrand['name'] = $request->name;
            $productBrand['created_by']  = \Auth::user()->creatorId();
            $productBrand->update();

            return redirect()->route('product_brand.index')->with(
                'success', 'Product Brand ' . $productBrand->name . ' updated!'
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
     * @param \App\ProductBrand $productBrand
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductBrand $productBrand)
    {
        if(\Auth::user()->can('Delete ProductBrand'))
        {
            $productBrand->delete();

            return redirect()->route('product_brand.index')->with(
                'success', 'Product Brand ' . $productBrand->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
