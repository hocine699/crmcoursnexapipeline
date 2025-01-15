<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\plan_request;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;

class PlanController extends Controller
{

    public function index()
    {
        if (\Auth::user()->type == 'super admin' || (\Auth::user()->type == 'owner')) {
            if (\Auth::user()->type == 'super admin') {
                $plans = Plan::all();
            } else {
                $plans = Plan::where('status', 1)->get();
            }

            return view('plan.index', compact('plans'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->type == 'super admin') {
            $arrDuration = Plan::$arrDuration;

            return view('plan.create', compact('arrDuration'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'price' => 'required',

            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        if (\Auth::user()->type == 'super admin') {
            $payment = Utility::set_payment_settings();
            if (count($payment) > 0 || $request->price <= 0) {

                $validation                   = [];
                $validation['name']           = 'required|unique:plans';
                $validation['price']          = 'required|numeric|min:0';
                $validation['duration']       = 'required';
                $validation['max_user']       = 'required|numeric';
                $validation['max_account']    = 'required|numeric';
                $validation['max_contact']    = 'required|numeric';
                $validation['storage_limit']  = 'required|numeric';
                $validation['enable_chatgpt'] = 'required|string';

                $post = $request->all();

                if ($request->trial == 1) {
                    $post['trial_days'] = !empty($request->trial_days) ? $request->trial_days : 0;
                }
                $post['status'] = 1;

                if (Plan::create($post)) {
                    return redirect()->back()->with('success', __('Plan Successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Something is wrong.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please set payment api key & secret key for add new plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($plan_id)
    {
        if (\Auth::user()->type == 'super admin') {
            $arrDuration = Plan::$arrDuration;
            $plan        = Plan::find($plan_id);
            return view('plan.edit', compact('plan', 'arrDuration'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $plan_id)
    {
        if (\Auth::user()->type == 'super admin') {
            $plan = Plan::find($plan_id);
            $payment = Utility::set_payment_settings();
            if (count($payment) > 0 || $request->price <= 0) {


                if (!empty($plan)) {
                    $validation                = [];
                    $validation['name']        = 'required|unique:plans,name,' . $plan_id;
                    $validation['duration']    = 'required';
                    $validation['max_user']    = 'required|numeric';
                    $validation['max_account'] = 'required|numeric';
                    $validation['max_contact'] = 'required|numeric';
                    $validation['storage_limit']  = 'required|numeric';
                    $validation['enable_chatgpt'] = 'required|string';

                    $post = $request->all();
                    $post['enable_chatgpt'] = ($request->enable_chatgpt == 'on') ? 'on' : 'off';

                    $post['trial'] = !empty($request->trial) ? $request->trial : 0;

                    if ($request->trial == 1) {

                        $post['trial_days'] = !empty($request->trial_days) ? $request->trial_days : 0;
                    }
                    if ($plan->update($post)) {
                        return redirect()->back()->with('success', __('Plan successfully updated.'));
                    } else {
                        return redirect()->back()->with('error', __('Something is wrong.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('Plan not found.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please set payment api key & secret key for add new plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy(Request $request, $id)
    {
        $userPlan = User::where('plan', $id)->first();
        if ($userPlan != null) {
            return redirect()->back()->with('error', __('The company has subscribed to this plan, so it cannot be deleted.'));
        }
        $plan = Plan::find($id);
        if ($plan->id == $id) {
            $plan->delete();

            return redirect()->back()->with('success', __('Plan deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Something went wrong'));
        }
    }

    public function userPlan(Request $request)
    {
        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        $plan    = Plan::find($planID);
        if ($plan) {
            if ($plan->price <= 0) {
                $objUser->assignPlan($plan->id);

                return redirect()->route('plan.index')->with('success', __('Plan successfully activated.'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back()->with('error', __('Plan not found.'));
        }
    }

    public function getpaymentgatway($code)
    {
        if (\Auth::user()->type != 'super admin') {

            $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
            $plan    = Plan::find($plan_id);
            $planReqs = plan_request::where('user_id', \Auth::user()->id)->where('plan_id', $plan_id)->first();
            if ($plan) {
                $admin_payment_setting = Utility::payment_settings();
                if ((isset($admin_payment_setting['is_bank_enabled']) && $admin_payment_setting['is_bank_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_toyyibpay_enabled']) && $admin_payment_setting['is_toyyibpay_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_payfast_enabled']) && $admin_payment_setting['is_payfast_enabled'] == 'on') ||
                    (isset($admin_payment_setting['enable_bank']) && $admin_payment_setting['enable_bank'] == 'on') ||
                    (isset($admin_payment_setting['manually_enabled']) && $admin_payment_setting['manually_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_iyzipay_enabled']) && $admin_payment_setting['is_iyzipay_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_sspay_enabled']) && $admin_payment_setting['is_sspay_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paytab_enabled']) && $admin_payment_setting['is_paytab_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_benefit_enabled']) && $admin_payment_setting['is_benefit_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_aamarpay_enabled']) && $admin_payment_setting['is_aamarpay_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paytr_enabled']) && $admin_payment_setting['is_paytr_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_yookassa_enabled']) && $admin_payment_setting['is_yookassa_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_midtrans_enabled']) && $admin_payment_setting['is_midtrans_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_xendit_enabled']) && $admin_payment_setting['is_xendit_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_nepalste_enabled']) && $admin_payment_setting['is_nepalste_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_paiementpro_enabled']) && $admin_payment_setting['is_paiementpro_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_fedapay_enabled']) && $admin_payment_setting['is_fedapay_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_payhere_enabled']) && $admin_payment_setting['is_payhere_enabled'] == 'on') ||
                    (isset($admin_payment_setting['is_cinetpay_enabled']) && $admin_payment_setting['is_cinetpay_enabled'] == 'on')
                ) {
                    return view('plan/payments', compact('plan', 'admin_payment_setting', 'planReqs'));
                } else {
                    return redirect()->route('plan.index')->with('error', __('The admin has not set the payment method. '));
                }
            } else {
                return redirect()->back()->with('error', __('Plan is deleted.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function PlanTrial($id)
    {
        if (\Auth::user()->type != 'super admin') {
            try {
                $id       = \Crypt::decrypt($id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Plan Not Found.'));
            }
            $plan = Plan::find($id);
            $user = User::where('id', \Auth::user()->id)->first();

            if (!empty($plan->trial) == 1) {

                $user->assignPlan($plan->id, 'Trial', $user->id);
                $user->is_trial_done = 1;
                $user->save();
            }

            return redirect()->back()->with('success', 'Your trial has been started.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateStatus(Request $request)
    {
        $userPlan = User::where('plan', $request->plan_id)->first();
        if ($userPlan != null) {
            return response()->json(['error' => __('The company has subscribed to this plan, so it cannot be disabled.')]);
        }
        $planId = $request->input('plan_id');
        $plan = Plan::find($planId);

        $plan->status = !$plan->status;
        $plan->save();

        if ($plan->status == true) {
            return response()->json(['success' => __('Plan successfully enable.')]);
        } else {
            return response()->json(['error' => __('Plan successfully disable.')]);
        }
    }
}
