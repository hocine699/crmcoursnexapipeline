<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;



class FedapayController extends Controller
{
    public function planPayWithFedapay(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $fedapay = !empty($payment_setting['fedapay_secret_key']) ? $payment_setting['fedapay_secret_key'] : '';
        $fedapay_mode = !empty($payment_setting['fedapay_mode']) ? $payment_setting['fedapay_mode'] : 'sandbox';
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'XOF';

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $authuser = Auth::user();

        if ($plan) {
            /* Check for code usage */
            $integerValue = $plan->price;

            $get_amount = intval($integerValue);
            if (!empty($request->coupon)) {
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun = $coupons->used_coupon();
                    $discount_value = ($plan->price / 100) * $coupons->discount;

                    $get_amount = $plan->price - $discount_value;

                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    if ($get_amount <= 0) {
                        $authuser = Auth::user();
                        $authuser->plan = $plan->id;
                        $authuser->save();
                        $assignPlan = $authuser->assignPlan($plan->id);
                        if ($assignPlan['is_success'] == true && !empty($plan)) {

                            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                            $userCoupon = new UserCoupon();

                            $userCoupon->user = $authuser->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order = $orderID;
                            $userCoupon->save();
                            Order::create(
                                [
                                    'order_id' => $orderID,
                                    'name' => null,
                                    'email' => null,
                                    'card_number' => null,
                                    'card_exp_month' => null,
                                    'card_exp_year' => null,
                                    'plan_name' => $plan->name,
                                    'plan_id' => $plan->id,
                                    'price' => $get_amount == null ? 0 : $get_amount,
                                    'price_currency' => $currency,
                                    'txn_id' => '',
                                    'payment_type' => __('Feda Pay'),
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            $assignPlan = $authuser->assignPlan($plan->id);
                            return redirect()->route('plan.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            try {
                \FedaPay\FedaPay::setApiKey($fedapay);

                \FedaPay\FedaPay::setEnvironment($fedapay_mode);
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                $transaction = \FedaPay\Transaction::create([
                    "description" => "Fedapay Payment",
                    "amount" => $get_amount,
                    "currency" => ["iso" => $currency],

                    "callback_url" => route('fedapay.status', [
                        'order_id' => $orderID,
                        'plan_id' => $plan->id,
                        "amount" => $get_amount,
                        "coupon_id" => !empty($coupons->id) ? $coupons->id : '',
                        'coupon_code' => !empty($request->coupon) ? $request->coupon : '',
                    ]),
                    "cancel_url" => route('fedapay.status', [
                        'order_id' => $orderID,
                        'plan_id' => $plan->id,
                        "amount" => $get_amount,
                        "coupon_id" => !empty($coupons->id) ? $coupons->id : '',
                        'coupon_code' => !empty($request->coupon) ? $request->coupon : '',
                    ]),

                ]);

                Order::create(
                    [
                        'order_id' => $orderID,
                        'name' => null,
                        'email' => null,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' => !empty($plan->name) ? $plan->name : 'Basic Package',
                        'plan_id' => $plan->id,
                        'price' => !empty($get_amount) ? $get_amount : 0,
                        'price_currency' => $currency,
                        'txn_id' => '',
                        'payment_type' => __('Fedapay'),
                        'payment_status' => 'pending',
                        'receipt' => null,
                        'user_id' => $authuser->id,
                    ]
                );

                $token = $transaction->generateToken();

                return redirect($token->url);
            } catch (\Exception $e) {
                return redirect()->route('plan.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetFedapayStatus(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : '';

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $getAmount = $request->amount;
        $authuser = Auth::user();
        $plan = Plan::find($request->plan_id);
        // Utility::transaction($plan);

        try {

            if ($request->status == 'approved') {

                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $authuser->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = $currency;
                $order->txn_id = $orderID;
                $order->payment_type = __('Fedapay');
                $order->payment_status = 'success';
                $order->receipt = '';
                $order->user_id = $authuser->id;
                $order->save();
                $assignPlan = $authuser->assignPlan($plan->id);
            } else {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
            if(!empty($authuser->referral_user)){
                Utility::transaction($order);
            }
            $coupons = Coupon::find($request->coupon_id);

            if (!empty($request->coupon_id)) {
                if (!empty($coupons)) {
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = $authuser->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $orderID;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
            }

            if ($assignPlan['is_success']) {
                return redirect()->route('plan.index')->with('success', __('Plan activated Successfully!'));
            } else {
                return redirect()->route('plan.index')->with('error', __($assignPlan['error']));
            }
        } catch (\Exception $e) {
            return redirect()->route('plan.index')->with('error', $e->getMessage());
        }
    }


    public function invoicePayWithfedapay(Request $request) {
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);
        $get_amount = $request->amount;

        // Convert amount to integer if it's a whole number, otherwise to float
        $amount = is_float($get_amount) ? $get_amount : intval($get_amount * 100);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        $payment_setting = Utility::invoice_payment_settings($user->id);
        $fedapay_secret = !empty($payment_setting['fedapay_secret_key']) ? $payment_setting['fedapay_secret_key'] : '';
        $fedapay_mode = !empty($payment_setting['fedapay_mode']) ? $payment_setting['fedapay_mode'] : 'sandbox';
        // $currency='XOF';
        $currency = !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'XOF';
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($invoice) {
            try {
                \FedaPay\FedaPay::setApiKey($fedapay_secret);
                \FedaPay\FedaPay::setEnvironment($fedapay_mode);

                $transaction = \FedaPay\Transaction::create([
                    "description" => "Fedapay Payment",
                    "amount" => $amount,
                    "currency" => ["iso" => $currency],
                    "callback_url" => route('invoice.fedapay.status', ['invoice_id' => $invoice_id, 'amount' => $get_amount]),
                    "cancel_url" => route('invoice.fedapay.status', ['invoice_id' => $invoice_id, 'amount' => $get_amount]),
                ]);

                $token = $transaction->generateToken();
                return redirect($token->url);

            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?? 'Something went wrong.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



public function invoiceGetfedapayStatus(Request $request)


{
    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
    $invoice = Invoice::find($request->invoice_id);
    if (Auth::check()) {
        $user = Auth::user();
    } else {
        $user = User::where('id', $invoice->created_by)->first();
    }

    $response = json_decode($request->json, true);

    if ($invoice) {

        try {

            if ($request->status == 'approved') {

                try {

                    $new = new InvoicePayment();
                    $new->invoice_id = $request->invoice_id;
                    $new->transaction_id= $orderID;
                    $new->date = Date('Y-m-d');
                    $new->amount = $request->has('amount') ? $request->amount : 0;
                    $new->client_id = $user->id;

                    $new->payment_type = 'Fedapay';
                    $new->save();
                    $invoice->save();

                    $settings = Utility::invoice_payment_settings($invoice->created_by);
                    //chnage status
                    $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
                    if ($invoice_getdue <= 0.0) {

                        Invoice::change_status($invoice->id, 3);
                    } else {

                        Invoice::change_status($invoice->id, 2);
                    }

                if (Auth::check()) {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
                }

                } catch (\Exception $e) {
                    dd($e);
                    return redirect()->route('pay.invoice',$invoice->id)->with('error', __($e->getMessage()));
                }


            } else {
                return redirect()->back()->with('error', $response['status_message']);
            }

        } catch (\Exception $e) {
            // dd($e);
            if (Auth::check()) {
                return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', $e->getMessage());
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', $e->getMessage());
            }
        }

    } else {
        if (Auth::check()) {
            return redirect()->route('pay.invoice', $invoice->id)->with('error', __('Invoice not found.'));
        } else {
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice not found.'));
        }
    }
}



}
