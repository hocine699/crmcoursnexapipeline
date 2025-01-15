<?php

namespace App\Http\Controllers;

use App\Models\OpportunitiesStage;
use Illuminate\Http\Request;

class OpportunitiesStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage OpportunitiesStage'))
        {
            if(\Auth::user()->type == 'owner')
            {
                $opportunities_stages = OpportunitiesStage::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $opportunities_stages = OpportunitiesStage::where('created_by', \Auth::user()->id)->get();

            }
            return view('opportunities_stage.index', compact('opportunities_stages'));
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
        if(\Auth::user()->can('Manage OpportunitiesStage'))
        {

            return view('opportunities_stage.create');
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
        if(\Auth::user()->can('Manage OpportunitiesStage'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $name                             = $request['name'];
            $opportunitiesstage               = new OpportunitiesStage();
            $opportunitiesstage->name         = $name;
            $opportunitiesstage['created_by'] = \Auth::user()->creatorId();
            $opportunitiesstage->save();

            return redirect()->route('opportunities_stage.index')->with('success', 'Opportunities Stage ' . $opportunitiesstage->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\OpportunitiesStage $opportunitiesStage
     *
     * @return \Illuminate\Http\Response
     */
    public function show(OpportunitiesStage $opportunitiesStage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\OpportunitiesStage $opportunitiesStage
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(OpportunitiesStage $opportunitiesStage)
    {
        if(\Auth::user()->can('Manage OpportunitiesStage'))
        {
            return view('opportunities_stage.edit', compact('opportunitiesStage'));
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
     * @param \App\OpportunitiesStage $opportunitiesStage
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OpportunitiesStage $opportunitiesStage)
    {
        if(\Auth::user()->can('Manage OpportunitiesStage'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $name                             = $request['name'];
            $opportunitiesStage->name         = $name;
            $opportunitiesStage['created_by'] = \Auth::user()->creatorId();
            $opportunitiesStage->save();

            return redirect()->route('opportunities_stage.index')->with('success', 'Lead Source ' . $opportunitiesStage->name . ' Update!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OpportunitiesStage $opportunitiesStage
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(OpportunitiesStage $opportunitiesStage)
    {
        if(\Auth::user()->can('Manage OpportunitiesStage'))
        {
            $opportunitiesStage->delete();

            return redirect()->route('opportunities_stage.index')->with('success', 'Lead Source ' . $opportunitiesStage->name . ' Deleted!');

        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
