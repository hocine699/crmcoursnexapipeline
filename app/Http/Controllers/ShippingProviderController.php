<?php

namespace App\Http\Controllers;

use App\Models\ShippingProvider;
use Illuminate\Http\Request;

class ShippingProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage ShippingProvider'))
        {
            if(\Auth::user()->type == 'owner'){

                $shipping_providers = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $shipping_providers = ShippingProvider::where('created_by', \Auth::user()->id)->get();

            }

            return view('shipping_provider.index', compact('shipping_providers'));
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
        if(\Auth::user()->can('Create ShippingProvider'))
        {
            return view('shipping_provider.create');
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
        if(\Auth::user()->can('Create ShippingProvider'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $shippingprovider               = new ShippingProvider();
            $shippingprovider->name         = $request['name'];
            $shippingprovider->website      = $request['website'];
            $shippingprovider['created_by'] = \Auth::user()->creatorId();
            $shippingprovider->save();

            return redirect()->route('shipping_provider.index')->with('success', 'Shipping Provider' . $shippingprovider->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\ShippingProvider $shippingProvider
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShippingProvider $shippingProvider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ShippingProvider $shippingProvider
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ShippingProvider $shippingProvider)
    {
        if(\Auth::user()->can('Edit ShippingProvider'))
        {
            return view('shipping_provider.edit', compact('shippingProvider'));
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
     * @param \App\ShippingProvider $shippingProvider
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShippingProvider $shippingProvider)
    {
        if(\Auth::user()->can('Edit ShippingProvider'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $name                           = $request['name'];
            $shippingProvider->name         = $name;
            $shippingProvider->website      = $request['website'];
            $shippingProvider['created_by'] = \Auth::user()->creatorId();
            $shippingProvider->update();

            return redirect()->route('shipping_provider.index')->with('success', 'Shipping Provider' . $shippingProvider->name . ' Updated!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ShippingProvider $shippingProvider
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingProvider $shippingProvider)
    {
        if(\Auth::user()->can('Delete ShippingProvider'))
        {
            $shippingProvider->delete();

            return redirect()->route('shipping_provider.index')->with(
                'success', 'Shipping Provider ' . $shippingProvider->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
