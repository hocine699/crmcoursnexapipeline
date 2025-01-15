<?php

namespace App\Http\Controllers;

use App\Models\ProductTax;
use Illuminate\Http\Request;

class ProductTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage ProductTax'))
        {
            if(\Auth::user()->type == 'owner'){

                $product_taxs = ProductTax::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $product_taxs = ProductTax::where('created_by', \Auth::user()->id)->get();

            }
            return view('producttax.index', compact('product_taxs'));
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
        if(\Auth::user()->can('Create ProductTax'))
        {
            return view('producttax.create');
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
        if(\Auth::user()->can('Create ProductTax'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'tax_name' => 'required|max:120',

                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $name                 = $request['tax_name'];
            $producttax           = new ProductTax();
            $producttax->tax_name = $name;
            $producttax->rate     = $request['rate'];
            $producttax['created_by']  = \Auth::user()->creatorId();
            $producttax->save();

            return redirect()->back()->with('success', 'Product Tax' . $producttax->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductTax $productTax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductTax $productTax)
    {
        if(\Auth::user()->can('Edit ProductTax'))
        {
            return view('producttax.edit', compact('productTax'));
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
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductTax $productTax)
    {
        if(\Auth::user()->can('Edit ProductTax'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'tax_name' => 'required|max:120',

                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $productTax['tax_name'] = $request->tax_name;
            $productTax['rate']     = $request->rate;
            $producttax['created_by']  = \Auth::user()->creatorId();
            $productTax->update();

            return redirect()->back()->with(
                'success', 'Product Tax ' . $productTax->name . ' updated!'
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
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductTax $productTax)
    {
        if(\Auth::user()->can('Delete ProductTax'))
        {
            $productTax->delete();

            return redirect()->back()->with(
                'success', 'Product Tax ' . $productTax->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
