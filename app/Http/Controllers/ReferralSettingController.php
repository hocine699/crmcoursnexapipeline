<?php

namespace App\Http\Controllers;

use App\Models\ReferralSetting;
use Illuminate\Http\Request;

class ReferralSettingController extends Controller
{
    public function index()
    {
        $referralSettings = ReferralSetting::where('created_by', \Auth::user()->creatorId())->first();
    
        return view('referral_setting.index',compact('referralSettings'));
    }
    public function store(Request $request)
    {        
        $validator = \Validator::make(
            $request->all(),
            [
                'commission_per'      => 'required',
                'minimum_amount'      => 'required',
            ]
        );
    
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
    
        $referralSetting = ReferralSetting::first();
    
        if ($referralSetting) {
            $referralSetting->update([
                'is_referral_enabled' => isset($request->is_referral_enabled) && $request->is_referral_enabled == 'on' ?  'on' : 'off',
                'commission_per' => $request->commission_per,
                'minimum_amount' => $request->minimum_amount,
                'guidelines' => $request->guidelines,
            ]);
        } else {
            ReferralSetting::create([
                'is_referral_enabled' => isset($request->is_referral_enabled) && $request->is_referral_enabled == 'on' ?  'on' : 'off',
                'commission_per' => $request->commission_per,
                'minimum_amount' => $request->minimum_amount,
                'guidelines' => $request->guidelines,
                'created_by' => \Auth::user()->creatorId(),
            ]);
        }
    
        return redirect()->route('referral_setting.index')->with('success', __('Setting successfully saved.'));

    }

    public function guideline(Request $request){
        $referralSettings = ReferralSetting::first();
        $user = \Auth::user();
        return view('referral_setting.guideline',compact('referralSettings','user'));
        
    }

    public function copylink(Request $request,$id){
        
    }
    
}
