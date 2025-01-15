<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PaiementProController extends Controller
{
    public function planPayWithpaiementpro(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $merchant_id = isset($payment_setting['paiementpro_merchant_id']) ? $payment_setting['paiementpro_merchant_id'] : '';
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
       
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
                                    'payment_type' => __('Paiement Pro'),
                                    'payment_status' => 'success',
                                    'receipt' => null,
                                    'user_id' => $authuser->id,
                                ]
                            );
                            $assignPlan = $authuser->assignPlan($plan->id);
                            return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                        }
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
            $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
            if (!empty($request->coupon)) {
                $call_back = route('paiementpro.status', [
                    'get_amount' => $get_amount,
                    'plan' => $plan,
                    'coupon_id' => $coupons->id
                ]);
            } else {
                $call_back = route('paiementpro.status', [
                    'get_amount' => $get_amount,
                    'plan' => $plan,
                ]);
            }
            $merchant_id = isset($payment_setting['paiementpro_merchant_id']) ? $payment_setting['paiementpro_merchant_id'] : '';
            $data = array(
                'merchantId' => $merchant_id,
                'amount' =>  $get_amount,
                'description' => "Api PHP",
                'channel' => $request->channel,
                'countryCurrencyCode' => $currency,
                'referenceNumber' => "REF-" . time(),
                'customerEmail' => $user->email,
                'customerFirstName' => $user->name,
                'customerLastname' =>  $user->name,
                'customerPhoneNumber' => $request->mobile_number,
                'notificationURL' => $call_back,
                'returnURL' => $call_back,
                'returnContext' => json_encode([
                    'coupon_code' => $request->coupon_code,
                ]),
            );
            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);

            curl_close($ch);
            $response = json_decode($response);

            if (isset($response->success) && $response->success == true) {
                // redirect to approve href
                return redirect($response->url);

                return redirect()
                    ->route('plan.index', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))
                    ->with('error', 'Something went wrong. OR Unknown error occurred');
            } else {
                return redirect()
                    ->route('plan.index', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))
                    ->with('error', $response->message ?? 'Something went wrong.');
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetpaiementproStatus(Request $request)
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
            $order->payment_type = __('Paiement Pro');
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
    }


    public function invoicePayWithpaiementpro(Request $request)
{
    $invoice_id = $request->invoice_id;
    $invoice = Invoice::find($invoice_id);
    if (Auth::check()) {
        $user = Auth::user();
    } else {
        $user = User::where('id', $invoice->created_by)->first();
    }
    $get_amount = $request->amount;

    // Convert amount to integer if it's a whole number, otherwise to float
    // $amount = is_float($get_amount) ? $get_amount : intval($get_amount * 100);

    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

    $payment_setting = Utility::invoice_payment_settings($user->id);
    $merchant_id = isset($payment_setting['paiementpro_merchant_id']) ? $payment_setting['paiementpro_merchant_id'] : '';
    $currency = !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'IDR';

    try {
        if ($invoice) {
            $merchant_id = isset($payment_setting['paiementpro_merchant_id']) ? $payment_setting['paiementpro_merchant_id'] : '';
            $data = array(
                'merchantId' => $merchant_id,
                'amount' => $get_amount, // Use the converted amount here
                // 'amount' => round($get_amount),
                'description' => "Api PHP",
                'channel' => $request->channel,
                'countryCurrencyCode' => 'USD',
                'referenceNumber' => "REF-" . time(),
                'customerEmail' => $user->email,
                'customerFirstName' => $user->name,
                'customerLastname' => $user->name,
                'customerPhoneNumber' => $request->mobile_number,
                'notificationURL' => route('invoice.paiementpro.status', $invoice_id),
                'returnURL' => route('invoice.paiementpro.status', $invoice_id),
                'returnContext' => json_encode([
                    'coupon_code' => $request->coupon_code,
                ]),
            );
            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paiementpro.net/webservice/onlinepayment/init/curl-init.php");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);

            curl_close($ch);
            $response = json_decode($response);
            if (isset($response->success) && $response->success == true) {
                // redirect to approve href
                return redirect($response->url);
            }
        } else {
            return redirect()->back()->with('error', 'Invoice not found.');
        }
    } catch (\Exception $e) {
        return redirect()->route('pay.invoice', $invoice_id)->with('error', __($e->getMessage()));
    }
}

public function invoiceGetpaiementproStatus(Request $request,$invoice_id)
{
    $invoice = Invoice::find($invoice_id);
    if (Auth::check()) {
        $user = Auth::user();
    } else {
        $user = User::where('id', $invoice->created_by)->first();
    }
    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
    $company_payment_setting = Utility::invoice_payment_settings($user->id);
    // $get_amount = $request->has('amount') ? $request->amount : 0;
    $get_amount = $request->amount;
    if ($invoice) {
        try {
            $new = new InvoicePayment();
            $new->invoice_id = $invoice_id;
            $new->transaction_id= $orderID;
            $new->date = Date('Y-m-d');
            $new->amount = $get_amount;
            // $new->amount = $get_amount;// Use the received amount here
            $new->client_id = $user->id;
            $new->payment_type = 'Paiement Pro';
            $new->save();

            // Change status based on invoice due
            $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
            if ($invoice_getdue <= 0.0) {
                Invoice::change_status($invoice->id, 3); // Paid
            } else {
                Invoice::change_status($invoice->id, 2); // Partially Paid
            }
            $invoice->save();

            // Redirect with success message
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->route('pay.invoice',encrypt($invoice->id))->with('error', __($e->getMessage()));
        }

    } else {
        // Redirect with error message if invoice not found
        return redirect()->route('pay.invoice',encrypt($invoice->id))->with('error', __('Invoice not found.'));
    }
}

}
