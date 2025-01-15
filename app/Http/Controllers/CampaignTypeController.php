<?php

namespace App\Http\Controllers;

use App\Models\CampaignType;
use Illuminate\Http\Request;

class CampaignTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage CampaignType'))
        {
            if(\Auth::user()->type == 'owner'){
            $types = CampaignType::where('created_by', \Auth::user()->creatorId())->get();
        }
        else{
            $types = CampaignType::where('created_by', \Auth::user()->id)->get();

        }
            return view('campaign_type.index', compact('types'));
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
        if(\Auth::user()->can('Create CampaignType'))
        {
            return view('campaign_type.create');
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
        if(\Auth::user()->can('Create CampaignType'))
        {
            $this->validate(
                $request, ['name' => 'required',]
            );

            $name                  = $request['name'];
            $campaigntype          = new CampaignType();
            $campaigntype->name    = $name;
            $campaigntype['created_by'] = \Auth::user()->creatorId();
            $campaigntype->save();

            return redirect()->back()->with('success', 'Campaign Type ' . $campaigntype->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CampaignType $campaignType
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CampaignType $campaignType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CampaignType $campaignType
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CampaignType $campaignType)
    {
        if(\Auth::user()->can('Edit CampaignType'))
        {
            return view('campaign_type.edit', compact('campaignType'));
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
     * @param \App\CampaignType $campaignType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampaignType $campaignType)
    {
        if(\Auth::user()->can('Edit CampaignType'))
        {
            $this->validate(
                $request, ['name' => 'required',]
            );
            $campaignType['name']       = $request->name;
            $campaignType['created_by'] = \Auth::user()->creatorId();
            $campaignType->update();

            return redirect()->route('campaign_type.index')->with(
                'success', 'Campaign Type ' . $campaignType->name . ' updated!'
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
     * @param \App\CampaignType $campaignType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampaignType $campaignType)
    {
        if(\Auth::user()->can('Delete CampaignType'))
        {
            $campaignType->delete();

            return redirect()->route('campaign_type.index')->with(
                'success', 'Campaign Type ' . $campaignType->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
