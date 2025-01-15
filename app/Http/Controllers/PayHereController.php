<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lahirulhr\PayHere\PayHere;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class PayHereController extends Controller
{
    public function planPayWithPayHere(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $payhere_merchant_id = !empty($payment_setting['payhere_merchant_id']) ? $payment_setting['payhere_merchant_id'] : '';
        $payhere_merchant_secret = !empty($payment_setting['payhere_merchant_secret']) ? $payment_setting['payhere_merchant_secret'] : '';
        $payhere_app_id = !empty($payment_setting['payhere_app_id']) ? $payment_setting['payhere_app_id'] : '';
        $payhere_app_secret = !empty($payment_setting['payhere_app_secret']) ? $payment_setting['payhere_app_secret'] : '';
        $payhere_mode = !empty($payment_setting['payhere_mode']) ? $payment_setting['payhere_mode'] : 'sandbox';
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'XOF';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);

        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $authuser = Auth::user();

        if ($plan) {
            /* Check for code usage */
            $get_amount = $plan->price;

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
                                    'payment_type' => __('Paiement Pro'),
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

                $config = [
                    'payhere.api_endpoint' => $payhere_mode === 'sandbox'
                        ? 'https://sandbox.payhere.lk/'
                        : 'https://www.payhere.lk/',
                ];

                $config['payhere.merchant_id'] = $payhere_merchant_id ?? '';
                $config['payhere.merchant_secret'] = $payhere_merchant_secret ?? '';
                $config['payhere.app_secret'] = $payhere_app_secret ?? '';
                $config['payhere.app_id'] = $payhere_app_id ?? '';
                config($config);

                $hash = strtoupper(
                    md5(
                        $payhere_merchant_id .
                            $orderID .
                            number_format($get_amount, 2, '.', '') .
                            'LKR' .
                            strtoupper(md5($payhere_merchant_secret))
                    )
                );

                $data = [
                    'first_name' => $authuser->name,
                    'last_name' => '',
                    'email' => $authuser->email,
                    'phone' => $authuser->mobile_no ?? '',
                    'address' => 'Main Rd',
                    'city' => 'Anuradhapura',
                    'country' => 'Sri lanka',
                    'order_id' => $orderID,
                    'items' => $plan->name ?? 'Add-on',
                    'currency' => 'LKR',
                    'amount' => $get_amount,
                    'hash' => $hash,
                ];


                return PayHere::checkOut()
                    ->data($data)
                    ->successUrl(route('payhere.status', [
                        $plan->id,
                        'amount' => $get_amount,
                        'coupon_code' => !empty($request->coupon_code) ? $request->coupon_code : '',
                        'coupon_id' => !empty($coupons->id) ? $coupons->id : '',
                    ]))
                    ->failUrl(route('payhere.status', [
                        $plan->id,
                        'amount' => $get_amount,
                        'coupon_code' => !empty($request->coupon_code) ? $request->coupon_code : '',
                        'coupon_id' => !empty($coupons->id) ? $coupons->id : '',
                    ]))
                    ->renderView();
            } catch (\Exception $e) {
                \Log::debug($e->getMessage());
                return redirect()->route('plan.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPayHereStatus(Request $request)
    {
        $payment_setting = Utility::getAdminPaymentSetting();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : '';

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $getAmount = $request->amount;
        $authuser = Auth::user();
        $plan = Plan::find($request->plan_id);
        // Utility::referralTransaction($plan);
        if ($plan) {

            try {
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
                $order->payment_type = __('PayHere');
                $order->payment_status = 'success';
                $order->receipt = '';
                $order->user_id = $authuser->id;
                $order->save();
                $assignPlan = $authuser->assignPlan($plan->id);
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
                    return redirect()->route('plan.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plan.index')->with('error', __($assignPlan['error']));
                }
            } catch (\Exception $e) {
                return redirect()->route('plan.index')->with('error', __('Transaction has been failed.'));
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
        }
    }



    public function invoicePayWithPayHere(Request $request)
    {

        $invoice_id = $request->invoice_id;

        $invoice = Invoice::find($invoice_id);
        $get_amount = $request->amount;

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        $payment_setting = Utility::invoice_payment_settings($user->id);
        $payhere_merchant_id = !empty($payment_setting['payhere_merchant_id']) ? $payment_setting['payhere_merchant_id'] : '';
        $payhere_merchant_secret = !empty($payment_setting['payhere_merchant_secret']) ? $payment_setting['payhere_merchant_secret'] : '';
        $payhere_app_id = !empty($payment_setting['payhere_app_id']) ? $payment_setting['payhere_app_id'] : '';
        $payhere_app_secret = !empty($payment_setting['payhere_app_secret']) ? $payment_setting['payhere_app_secret'] : '';
        $payhere_mode = !empty($payment_setting['payhere_mode']) ? $payment_setting['payhere_mode'] : 'sandbox';
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'XOF';
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($invoice) {
            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                try {

                    $config = [
                        'payhere.api_endpoint' => $payhere_mode === 'sandbox'
                        ? 'https://sandbox.payhere.lk/'
                        : 'https://www.payhere.lk/',
                    ];

                    $config['payhere.merchant_id'] = $payhere_merchant_id ?? '';
                    $config['payhere.merchant_secret'] = $payhere_merchant_secret ?? '';
                    $config['payhere.app_secret'] = $payhere_app_secret ?? '';
                    $config['payhere.app_id'] = $payhere_app_id ?? '';
                    config($config);

                    $hash = strtoupper(
                        md5(
                            $payhere_merchant_id .
                            $orderID .
                            number_format($get_amount, 2, '.', '') .
                            'LKR' .
                            strtoupper(md5($payhere_merchant_secret))
                        )
                    );

                    $data = [
                        'first_name' => $user->name,
                        'last_name' => '',
                        'email' => $user->email,
                        'phone' => $user->mobile_no ?? '',
                        'address' => 'Main Rd',
                        'city' => 'Anuradhapura',
                        'country' => 'Sri lanka',
                        'order_id' => $orderID,
                        'items' => $invoice->name ?? 'Add-on',
                        'currency' => 'LKR',
                        'amount' => $get_amount,
                        'hash' => $hash,
                    ];

                    return PayHere::checkOut()
                        ->data($data)
                        ->successUrl(route('invoice.payhere.status', [
                            'amount' => $get_amount,
                            'invoice_id' => $invoice_id,
                        ]))
                        ->failUrl(route('invoice.payhere.status', [
                            'amount' => $get_amount,
                            'invoice_id' => $invoice_id,
                        ]))
                        ->renderView();

                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage() ?? 'Something went wrong.');
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function invoiceGetPayHereStatus(Request $request)
    {
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($request->invoice_id);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }

        if ($invoice) {

            try {

                $new = new InvoicePayment();
                $new->invoice_id = $invoice_id;
                $new->transaction_id= $orderID;
                $new->date = Date('Y-m-d');
                $new->amount = $request->has('amount') ? $request->amount : 0;
                $new->client_id = $user->id;

                $new->payment_type = 'PayHere';
                $new->save();
                //chnage status
                $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
                if ($invoice_getdue <= 0.0) {

                    Invoice::change_status($invoice->id, 3);
                } else {

                    Invoice::change_status($invoice->id, 2);
                }
                $invoice->save();



                $settings = Utility::invoice_payment_settings($invoice->created_by);

                if (Auth::check()) {
                    return redirect()->route('pay.invoice',encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
                }

            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', $e->getMessage());
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', $e->getMessage());
                }
            }
            } else {
                if (Auth::check()) {
                    return redirect()->route('pay.invoice',encrypt($invoice->id))->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice not found.'));
                }
            }
    }





}
