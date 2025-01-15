<?php

namespace App\Http\Controllers;

use App\Models\TargetList;
use Illuminate\Http\Request;

class TargetListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage TargetList'))
        {
            if(\Auth::user()->type == 'owner'){

                $targetlists = TargetList::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $targetlists = TargetList::where('created_by', \Auth::user()->id)->get();

            }
            return view('target_list.index', compact('targetlists'));
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
        if(\Auth::user()->can('Create TargetList'))
        {
            return view('target_list.create');
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
        if(\Auth::user()->can('Create TargetList'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $targetlist               = new TargetList();
            $targetlist->name         = $request['name'];
            $targetlist->description  = $request['description'];
            $targetlist['created_by'] = \Auth::user()->creatorId();
            $targetlist->save();
            return redirect()->route('target_list.index')->with('success', 'Target List ' . $targetlist->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\TargetList $targetList
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TargetList $targetList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\TargetList $targetList
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TargetList $targetList)
    {
        if(\Auth::user()->can('Edit TargetList'))
        {
            return view('target_list.edit', compact('targetList'));
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
     * @param \App\TargetList $targetList
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TargetList $targetList)
    {
        if(\Auth::user()->can('Edit TargetList'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $targetList->name        = $request['name'];
            $targetList->description = $request['description'];
            $targetList->update();

            return redirect()->route('target_list.index')->with('success', 'Target List' . $targetList->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\TargetList $targetList
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TargetList $targetList)
    {
        if(\Auth::user()->can('Delete TargetList'))
        {
            $targetList->delete();

            return redirect()->route('target_list.index')->with(
                'success', 'Target List ' . $targetList->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
