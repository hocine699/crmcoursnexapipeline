<?php

namespace App\Http\Controllers;

use App\Models\ReferralTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        if (\Auth::user()->type == 'super admin') {
            $transactions = ReferralTransaction::get();
            return view('transaction.index', compact('transactions'));
        } elseif (\Auth::user()->type == 'owner') {
            $user = \Auth::user();
            $refferalUser = User::where('referral_user',$user->referral_code)->first();
            if (!empty($refferalUser->referral_user)) {
                $transactions = ReferralTransaction::where('referral_code', $user->referral_code)->get();
                return view('transaction.company_index', compact('transactions'));
            }
            return view('transaction.company_index');
        }
    }
}
