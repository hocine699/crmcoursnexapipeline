<?php

namespace App\Http\Controllers;

use App\Models\AccountIndustry;
use Illuminate\Http\Request;

class AccountIndustryController extends Controller
{
    public function index()
    {
         if(\Auth::user()->can('Manage AccountIndustry'))
        {
            if(\Auth::user()->type == 'owner'){
        $industrys = AccountIndustry::where('created_by', \Auth::user()->creatorId())->get();
            }
            else{
        $industrys = AccountIndustry::where('created_by', \Auth::user()->id)->get();

            }
        return view('account_industry.index', compact('industrys'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function create()
    {
         if(\Auth::user()->can('Create AccountIndustry'))
        {
        return view('account_industry.create');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function store(Request $request)
    {
         if(\Auth::user()->can('Create AccountIndustry'))
        {
        $this->validate(
            $request, ['name' => 'required|max:40',]
        );
        $name                          = $request['name'];
        $accountIndustry               = new accountIndustry();
        $accountIndustry->name         = $name;
        $accountIndustry['created_by'] = \Auth::user()->creatorId();
        $accountIndustry->save();

        return redirect()->route('account_industry.index')->with('success', 'Account  Industry ' . $accountIndustry->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function show(accountIndustry $accountIndustry)
    {
        //
    }

    public function edit(accountIndustry $accountIndustry)
    {
         if(\Auth::user()->can('Edit AccountIndustry'))
        {
        return view('account_industry.edit', compact('accountIndustry'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function update(Request $request, accountIndustry $accountIndustry)
    {
         if(\Auth::user()->can('Edit AccountIndustry'))
        {
        $this->validate(
            $request, ['name' => 'required|max:40',]
        );
        $accountIndustry['name'] = $request->name;
        $accountIndustry['created_by'] = \Auth::user()->creatorId();
        $accountIndustry->update();

        return redirect()->route('account_industry.index')->with(
            'success', 'Account Type ' . $accountIndustry->name . ' updated!'
        );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function destroy(accountIndustry $accountIndustry)
    {
         if(\Auth::user()->can('Delete AccountIndustry'))
        {
        $accountIndustry->delete();

        return redirect()->route('account_industry.index')->with(
            'success', 'Account Type ' . $accountIndustry->name . ' Deleted!'
        );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
