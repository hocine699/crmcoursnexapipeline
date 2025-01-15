<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class NepalstePaymnetController extends Controller
{
    public function planPayWithnepalste(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $api_key = isset($payment_setting['nepalste_public_key']) ? $payment_setting['nepalste_public_key'] : '';
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'NPR';

        // $currency = 'NPR';
        // $planID = \Illuminate\Support\Facades\decrypt($request->plan_id);
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);

        $plan = Plan::find($planID);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $user = Auth::user();

        if ($plan) {
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
                                    'payment_type' => 'Nepalste',
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            // $assignPlan = $authuser->assignPlan($plan->id);
                            return redirect()->route('plan.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
        }
        if (!empty($request->coupon)) {
            $response = ['get_amount' => $get_amount, 'plan' => $plan, 'coupon_id' => $coupons->id];
        } else {
            $response = ['get_amount' => $get_amount, 'plan' => $plan];
        }
        try {

            $parameters = [
                'identifier' => 'DFU80XZIKS',
                'currency' => $currency,
                'amount' => $get_amount,
                'details' => $plan->name,
                'ipn_url' => route('nepalste.status', $response),
                'cancel_url' => route('nepalste.cancel'),
                'success_url' => route('nepalste.status', $response),
                'public_key' => $api_key,
                'site_logo' => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                'checkout_theme' => 'dark',
                'customer_name' => 'John Doe',
                'customer_email' => 'john@mail.com',
            ];
            //live end point
            $liveUrl    = "https://nepalste.com.np/payment/initiate";

            //test end point
            $sandboxUrl = "https://nepalste.com.np/sandbox/payment/initiate";

            $url = $payment_setting['nepalste_mode'] == 'live' ? $liveUrl : $sandboxUrl;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            curl_close($ch);

            $result = json_decode($result, true);

            if (isset($result['success'])) {
                return redirect($result['url']);
            } else {
                return redirect()->back()->with('error', __($result['message']));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function planGetNepalsteStatus(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : '';

        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        $getAmount = $request->get_amount;
        $authuser = Auth::user();
        $plan = Plan::find($request->plan);

        // Utility::referralTransaction($plan);

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
        $order->payment_type = __('Neplaste');
        $order->payment_status = 'success';
        $order->txn_id = '';
        $order->receipt = '';
        $order->user_id = $authuser->id;
        $order->save();
        $assignPlan = $authuser->assignPlan($plan->id);
        if (!empty($user->referral_user)) {
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
    }

    public function planGetNepalsteCancel(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has failed'));
    }


    public function invoicePayWithnepalste(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);
        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $get_amount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($invoice) {
            $payment_setting = Utility::invoice_payment_settings($user->id);
            $api_key = $payment_setting['nepalste_public_key'];
            $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'NPR';

            // $currency = 'NPR';
            $response = ['user' => $user, 'get_amount' => $get_amount, 'invoice' => $invoice];

            try {
                $parameters = [
                    'identifier' => 'DFU80XZIKS',
                    'currency' => $currency,
                    'amount' => $get_amount,
                    'details' => 'Invoice',
                    'ipn_url' => route('invoice.nepalste.status', $response),
                    'cancel_url' => route('invoice.nepalste.cancel'),
                    'success_url' => route('invoice.nepalste.status', $response),
                    'public_key' => $api_key,
                    'site_logo' => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                    'checkout_theme' => 'dark',
                    'customer_name' => 'John Doe',
                    'customer_email' => 'john@mail.com',
                ];

                //live end point
                $liveUrl    = "https://nepalste.com.np/payment/initiate";

                //test end point
                $sandboxUrl = "https://nepalste.com.np/sandbox/payment/initiate";

                $url = $payment_setting['nepalste_mode'] == 'live' ? $liveUrl : $sandboxUrl;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);

                curl_close($ch);

                $result = json_decode($result, true);

                if (isset($result['success'])) {
                    return redirect($result['url']);
                } else {
                    return redirect()->back()->with('error', __($result['message']));
                }
            } catch (\Throwable $e) {
                return redirect()->route('pay.invoice', $invoice->id)->with('error', $e->getMessage());
            }
        }
    }
    public function invoiceGetNepalsteStatus(Request $request)
    {

        $invoice = Invoice::find($request->invoice);
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $company_payment_setting = Utility::invoice_payment_settings($user->id);
        $get_amount = $request->get_amount;
        if ($invoice) {
            try {
                $invoice_payment = new InvoicePayment();
                $invoice_payment->transaction_id =  app('App\Http\Controllers\InvoiceController')->transactionNumber($invoice->created_by);
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->amount = $get_amount;
                $invoice_payment->date = date('Y-m-d');
                $invoice_payment->payment_id = 0;
                $invoice_payment->payment_type = __('Nepalste');
                $invoice_payment->client_id = 0;
                $invoice_payment->notes = '';
                $invoice_payment->save();
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
                return redirect()->route('pay.invoice')->with('error', __($e->getMessage()));
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice',  encrypt($invoice->id))->with('success', __('Invoice not found.'));
            }
        }
    }

    public function invoiceGetNepalsteCancel(Request $request)
    {
        return redirect()->back()->with('error', __('Transaction has failed'));
    }
}
