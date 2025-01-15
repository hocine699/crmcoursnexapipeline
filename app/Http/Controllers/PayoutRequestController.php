<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\ReferralSetting;
use App\Models\ReferralTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class PayoutRequestController extends Controller
{
    public function index()
    {
        if (\Auth::user()->type == 'super admin') {
            $payoutRequests = Payout::where('status',0)->get();
            return view('payout_request.index',compact('payoutRequests'));
        } elseif (\Auth::user()->type == 'owner') {
            $user = \Auth::user();
            $paymentRequest = Payout::where('status' , 0)->where('company_id',$user->id)->first();
            $transactions = ReferralTransaction::where('referral_code', $user->referral_code)->get();
            $payouts = Payout::where('company_id',$user->id)->get();
            $paidAmount = $payouts->where('status',2)->sum('requested_amount');
            $totalAmount = 0;
            foreach($transactions as $transaction){
                $totalAmount += $transaction->amount;
            }
            return view('payout_request.company_index',compact('payouts','transactions','paidAmount','paymentRequest'));
        }
    }

    public function companyRequest(){
        return view('payout_request.request');
        
    }

    public function payoutStore(Request $request){
        $validator = \Validator::make(
            $request->all(),
            [
                'requested_amount' => 'required',                
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user       =  \Auth::user();      
        $requestAmount = new Payout();
        $requestAmount->company_id       = $user->id;
        $requestAmount->request_date     = date('Y-m-d');
        $requestAmount->status           = 0;
        $requestAmount->requested_amount = $request->requested_amount;
        $requestAmount->created_by       = \Auth::user()->creatorId();
        $requestAmount->save();
        return redirect()->back()->with('success','Request Successfully Send.');
    }

    public function requestAmount($id , $status){
        $referralSetting = ReferralSetting::where('created_by',1)->first();
        $payout = Payout::find($id);
        $paidAmount = Payout::where('company_id',$payout->company_id)->where('status',2)->sum('requested_amount');
        $transactions = ReferralTransaction::where('ref_user_id',$payout->company_id)->first();
        $netAmount =  $transactions->amount - $paidAmount;
        if($payout->requested_amount > $netAmount && $status == 1){
            $payout->status = 1;
            $payout->save();
            return redirect()->back()->with('error', __('This request cannot be accepted because it exceeds the commission amount.'));
        }
        elseif($payout->requested_amount <= $referralSetting->minimum_amount && $status == 1){
            $payout->status = 1;
            $payout->save();
            return redirect()->back()->with('error', __('This request cannot be accepted because it less than the threshold amount.'));
        }
        if($status == 1){
            $payout->status = 2;
            $payout->save();
        return redirect()->back()->with('success', __('Request Aceepted Successfully..'));

        }
        if($status == 0){
            $payout->status = 1;
            $payout->save();
        return redirect()->back()->with('success', __('Request Rejected Successfully.'));
        }

        
    }

    public function payoutCancelRequest($id){
        $transaction = ReferralTransaction::where('ref_user_id',$id)->first();        
        $payout = Payout::where('company_id',$transaction->ref_user_id)->first();
        
        $payout->delete();
        return redirect()->back()->with('success', __('Request Cancel Successfully.'));

    }
}
