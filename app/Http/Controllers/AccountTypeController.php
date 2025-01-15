<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage AccountType'))
        {
            if(\Auth::user()->type == 'owner'){

                $types = AccountType::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
                $types = AccountType::where('created_by', \Auth::user()->id)->get();
            }
            return view('account_type.index', compact('types'));
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
            return view('account_type.create');
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
        if(\Auth::user()->can('Create AccountType'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $name                      = $request['name'];
            $accounttype               = new AccountType();
            $accounttype->name         = $name;
            $accounttype['created_by'] = \Auth::user()->creatorId();
            $accounttype->save();

            return redirect()->route('account_type.index')->with('success', 'Account Type' . $accounttype->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\accountType $accountType
     *
     * @return \Illuminate\Http\Response
     */
    public function show(accountType $accountType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\accountType $accountType
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(accountType $accountType)
    {
        if(\Auth::user()->can('Edit AccountType'))
        {
            return view('account_type.edit', compact('accountType'));
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
     * @param \App\accountType $accountType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, accountType $accountType)
    {
        if(\Auth::user()->can('Edit AccountType'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $accountType['name']       = $request->name;
            $accountType['created_by'] = \Auth::user()->creatorId();
            $accountType->update();

            return redirect()->route('account_type.index')->with(
                'success', 'Account Type ' . $accountType->name . ' updated!'
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
     * @param \App\accountType $accountType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(accountType $accountType)
    {
        if(\Auth::user()->can('Delete AccountType'))
        {
            $accountType->delete();

            return redirect()->route('account_type.index')->with(
                'success', 'Account Type ' . $accountType->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
