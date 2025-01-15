<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Xendit\Xendit;
use Illuminate\Support\Str;

class XenditPaymentController extends Controller
{
    public function planPayWithXendit(Request $request)
    {
        $payment_setting = Utility::payment_settings();
        $xendit_api = $payment_setting['xendit_api'];
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
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $userCoupon = new UserCoupon();
                    $userCoupon->user = Auth::user()->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order = $orderID;
                    $userCoupon->save();
                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }
            $response = ['orderId' => $orderID, 'user' => $user, 'get_amount' => $get_amount, 'plan' => $plan, 'currency' => $currency];
            Xendit::setApiKey($xendit_api);
            $params = [
                'external_id' => $orderID,
                'payer_email' => Auth::user()->email,
                'description' => 'Payment for order ' . $orderID,
                'amount' => $get_amount,
                'callback_url' =>  route('plan.xendit.status'),
                'success_redirect_url' => route('plan.xendit.status', $response),
                'failure_redirect_url' => route('plan.index'),
            ];

            $invoice = \App\Xendit\Invoice::create($params);
            Session::put('invoice', $invoice);

            return redirect($invoice['invoice_url']);
        }
    }

    public function planGetXenditStatus(Request $request)
    {
        $data = request()->all();

        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }

        $payment_setting = Utility::payment_settings();
        $xendit_api = $payment_setting['xendit_api'];
        Xendit::setApiKey($xendit_api);

        $session = Session::get('invoice');
        $getInvoice =\App\Xendit\Invoice::retrieve($session['id']);

        $authuser = User::find($fixedData['user']);
        $plan = Plan::find($fixedData['plan']);

        if ($getInvoice['status'] == 'PAID') {

            $order = Order::create(
                [
                    'order_id' => $fixedData['orderId'],
                    'name' => null,
                    'email' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => isset($plan->name) ? $plan->name : '',
                    'plan_id' => isset($plan->id) ? $plan->id : '',
                    'price' => $fixedData['get_amount'] == null ? 0 : $fixedData['get_amount'],
                    'price_currency' => isset($fixedData['currency']) ? $fixedData['currency'] : '$',
                    'txn_id' => '',
                    'payment_type' => __('Xendit'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => isset($fixedData['user']) ? $fixedData['user'] : '',
                ]
            );
            if(!empty($authuser->referral_user)){
                Utility::transaction($order);
            }
            $assignPlan = $authuser->assignPlan($plan->id);

            if ($assignPlan['is_success']) {
                return redirect()->route('plan.index')->with('success', __('Plan activated Successfully!'));
            } else {
                return redirect()->route('plan.index')->with('error', __($assignPlan['error']));
            }
        }
    }

    public function invoicePayWithXendit(Request $request)
    {
        $data = request()->all();

        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }
        $invoice_id = $fixedData['invoice_id'];
        $invoice = Invoice::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $get_amount = $fixedData['amount'];
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {
            if ($invoice) {
                $payment_setting = Utility::invoice_payment_settings($user->id);
                $xendit_token = $payment_setting['xendit_token'];
                $xendit_api = $payment_setting['xendit_api'];
                $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'RUB';
                $response = ['orderId' => $orderID, 'user' => $user, 'get_amount' => $get_amount, 'invoice' => $invoice, 'currency' => $currency];
                Xendit::setApiKey($xendit_api);
                $params = [
                    'external_id' => $orderID,
                    'payer_email' => $user->email,
                    'description' => 'Payment for order ' . $orderID,
                    'amount' => $get_amount,
                    'callback_url' =>  route('invoice.xendit.status'),
                    'success_redirect_url' => route('invoice.xendit.status', $response),
                ];

                $Xenditinvoice = \App\Xendit\Invoice::create($params);
                Session::put('invoicepay', $Xenditinvoice);
                return redirect($Xenditinvoice['invoice_url']);
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            // dd($e);
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvociePaymentStatus(Request $request)
    {
        $data = request()->all();

        $fixedData = [];
        foreach ($data as $key => $value) {
            $fixedKey = str_replace('amp;', '', $key);
            $fixedData[$fixedKey] = $value;
        }
        $session = Session::get('invoicepay');
        $invoice = Invoice::find($fixedData['invoice']);
        $user = User::where('id', $invoice->created_by)->first();
        $payment_setting = Utility::invoice_payment_settings($user->id);
        $xendit_api = $payment_setting['xendit_api'];
        Xendit::setApiKey($xendit_api);
        $getInvoice = \App\Xendit\Invoice::retrieve($session['id']);
        $get_amount = $fixedData['get_amount'];

        if ($getInvoice['status'] == 'PAID') {

            $invoice_payment = new InvoicePayment();
            $invoice_payment->transaction_id =  app('App\Http\Controllers\InvoiceController')->transactionNumber($invoice->created_by);
            $invoice_payment->invoice_id = $invoice->id;
            $invoice_payment->amount = $get_amount;
            $invoice_payment->date = date('Y-m-d');
            $invoice_payment->payment_id = 0;
            $invoice_payment->payment_type = __('Xendit');
            $invoice_payment->client_id = 0;
            $invoice_payment->notes = '';
            $invoice_payment->save();

            $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');

            if ($invoice_getdue <= 0.0) {

                Invoice::change_status($invoice->id, 3);
            } else {

                Invoice::change_status($invoice->id, 2);
            }
        }
        if (Auth::check()) {
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
        } else {
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
        }
    }
}
