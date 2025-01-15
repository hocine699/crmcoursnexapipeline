<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage LeadSource'))
        {
            if(\Auth::user()->type == 'owner'){
            $lead_sources = LeadSource::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $lead_sources = LeadSource::where('created_by', \Auth::user()->id)->get();

            }
            return view('lead_source.index', compact('lead_sources'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *_name
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create LeadSource'))
        {
            return view('lead_source.create');
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
        if(\Auth::user()->can('Create LeadSource'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $name             = $request['name'];
            $leadsource       = new LeadSource();
            $leadsource->name = $name;
            $leadsource['created_by']   = \Auth::user()->creatorId();
            $leadsource->save();

            return redirect()->route('lead_source.index')->with('success', 'Lead Source ' . $leadsource->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\LeadSource $leadSource
     *
     * @return \Illuminate\Http\Response
     */
    public function show(LeadSource $leadSource)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\LeadSource $leadSource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(LeadSource $leadSource)
    {
        if(\Auth::user()->can('Edit LeadSource'))
        {
            return view('lead_source.edit', compact('leadSource'));
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
     * @param \App\LeadSource $leadSource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LeadSource $leadSource)
    {
        if(\Auth::user()->can('Edit LeadSource'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $name             = $request['name'];
            $leadSource->name = $name;
            $leadSource->save();

            return redirect()->route('lead_source.index')->with('success', 'Lead Source ' . $leadSource->name . ' Update!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\LeadSource $leadSource
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeadSource $leadSource)
    {
        if(\Auth::user()->can('Delete LeadSource'))
        {
            $leadSource->delete();

            return redirect()->route('lead_source.index')->with('success', 'Lead Source ' . $leadSource->name . ' Deleted!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
