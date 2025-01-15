<?php

namespace App\Http\Controllers;

use App\Models\CaseType;
use Illuminate\Http\Request;

class CaseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage CaseType'))
        {
            if(\Auth::user()->type == 'owner'){
            $types = CaseType::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $types = CaseType::where('created_by', \Auth::user()->id)->get();

            }
            return view('case_type.index', compact('types'));
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
        if(\Auth::user()->can('Create CaseType'))
        {
            return view('case_type.create');
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
        if(\Auth::user()->can('Create CaseType'))
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
            $name                   = $request['name'];
            $casetype               = new CaseType();
            $casetype->name         = $name;
            $casetype['created_by'] = \Auth::user()->creatorId();
            $casetype->save();

            return redirect()->route('case_type.index')->with('success', 'Case Type ' . $casetype->name . ' added!');

        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CaseType $caseType
     *
     * @return \Illuminate\Http\Response
     */
    public function show(CaseType $caseType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CaseType $caseType
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(CaseType $caseType)
    {
        if(\Auth::user()->can('Edit CaseType'))
        {
            return view('case_type.edit', compact('caseType'));
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
     * @param \App\CaseType $caseType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CaseType $caseType)
    {
        if(\Auth::user()->can('Edit CaseType'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $caseType['name'] = $request->name;
            $caseType['created_by']  = \Auth::user()->creatorId();
            $caseType->update();

            return redirect()->route('case_type.index')->with(
                'success', 'Case Type ' . $caseType->name . ' updated!'
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
     * @param \App\CaseType $caseType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(CaseType $caseType)
    {
        if(\Auth::user()->can('Delete CaseType'))
        {
            $caseType->delete();

            return redirect()->route('case_type.index')->with(
                'success', 'Case Type ' . $caseType->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
