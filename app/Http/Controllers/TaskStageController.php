<?php

namespace App\Http\Controllers;

use App\Models\TaskStage;
use Illuminate\Http\Request;

class TaskStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage TaskStage'))
        {
            if(\Auth::user()->type == 'owner'){
            $stages = TaskStage::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
            $stages = TaskStage::where('created_by', \Auth::user()->id)->get();

            }
            return view('task_stage.index', compact('stages'));
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
        if(\Auth::user()->can('Create AccountType'))
        {
            return view('task_stage.create');
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
       if(\Auth::user()->can('Create TaskStage'))
       {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $name                    = $request['name'];
            $taskstage               = new TaskStage();
            $taskstage->name         = $name;
            $taskstage['created_by'] = \Auth::user()->creatorId();
            $taskstage->save();

            return redirect()->route('task_stage.index')->with('success', 'Task Stage ' . $taskstage->name . ' added!');
       }
       else
       {
           return redirect()->back()->with('error', 'permission Denied');
       }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\TaskStage $taskStage
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TaskStage $taskStage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\TaskStage $taskStage
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskStage $taskStage)
    {
        if(\Auth::user()->can('Edit TaskStage'))
        {
            return view('task_stage.edit', compact('taskStage'));
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
     * @param \App\TaskStage $taskStage
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaskStage $taskStage)
    {
       if(\Auth::user()->can('Edit TaskStage'))
       {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $taskStage['name']       = $request->name;
            $taskStage['created_by'] = \Auth::user()->creatorId();
            $taskStage->update();

            return redirect()->route('task_stage.index')->with(
                'success', 'Task Stage ' . $taskStage->name . ' updated!'
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
     * @param \App\TaskStage $taskStage
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskStage $taskStage)
    {
       if(\Auth::user()->can('Delete TaskStage'))
       {
            $taskStage->delete();

            return redirect()->route('task_stage.index')->with(
                'success', 'Task Stage ' . $taskStage->name . ' Deleted!'
            );
       }
       else
       {
           return redirect()->back()->with('error', 'permission Denied');
       }
    }
}
