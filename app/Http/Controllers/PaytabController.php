<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\User;
use App\PayTab\paypage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use Exception;
use Illuminate\Support\Facades\DB;

class PaytabController extends Controller
{
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled, $invoiceData, $currancy ;

    public function paymentConfig()
    {
        $payment_setting = Utility::payment_settings();
        if (\Auth::check()) {
            config([
                'paytabs.profile_id' => isset($payment_setting['paytab_profile_id']) ? $payment_setting['paytab_profile_id'] : '',
                'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
                'paytabs.region' => isset($payment_setting['paytab_region']) ? $payment_setting['paytab_region'] : '',
                'paytabs.currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
            ]);
        }
    }
    public function paymentSetting($id)
    {
        $payment_setting = Utility::invoice_payment_settings($id);
        config([
            'paytabs.profile_id' => isset($payment_setting['paytab_profile_id']) ? $payment_setting['paytab_profile_id'] : '',
            'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
            'paytabs.region' => isset($payment_setting['paytab_region']) ? $payment_setting['paytab_region'] : '',
            'paytabs.currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
        ]);
    }
    public function planPayWithpaytab(Request $request)
    {
        try {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);
            $this->paymentconfig();
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
                                if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                    try {
                                        $authuser->cancel_subscription($authuser->id);
                                    } catch (\Exception $exception) {
                                        \Log::debug($exception->getMessage());
                                    }
                                }
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
                                        'price_currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
                                        'txn_id' => '',
                                        'payment_type' => 'Paytab',
                                        'payment_status' => 'succeeded',
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
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $paypage= new paypage();
                $pay = $paypage->sendPaymentCode('all')
                    ->sendTransaction('sale')
                    ->sendCart(1, $get_amount, 'invoice payment')
                    ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                    ->sendURLs(
                        route('plan.paytab.success', ['success' => 1, 'data' => $request->all(), $plan->id, 'amount' => $get_amount]),
                        route('plan.paytab.success', ['success' => 0, 'data' => $request->all(), $plan->id, 'amount' => $get_amount])
                    )
                    ->sendLanguage('en')
                    ->sendFramed($on = false)
                    ->create_pay_page();
                return $pay;
            } else {
                return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plan.index')->with('error', __($e->getMessage()));
        }
    }

    public function PaytabGetPayment(Request $request)
    {
        $planId = $request->plan_id;
        $couponCode = $request->coupon;
        $getAmount = $request->amount;


        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        $plan = Plan::find($planId);
        $user = auth()->user();
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        try {
            if ($request->success == "1") {
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
                $order->payment_type = __('Paytab');
                $order->payment_status = 'succeeded';
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);
                }
                $assignPlan = $user->assignPlan($plan->id);
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
                    if (!empty($coupons)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
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
            } else {
                return redirect()->route('plan.index')->with('error', __('Your Transaction is fail please try again'));
            }
        } catch (Exception $e) {
            return redirect()->route('plan.index')->with('error', __($e->getMessage()));
        }
    }

    public function invoicePayWithpaytab(Request $request)
    {
        try {
            $invoice_id = $request->invoice_id;
            $invoice = Invoice::find($invoice_id);
            $this->paymentSetting($invoice->created_by);
            if (\Auth::check()) {
                $user = Auth::user();
            } else {
                $user = User::where('id', $invoice->created_by)->first();
            }
            if ($user->type != 'owner') {
                $user = User::where('id', $user->created_by)->first();
            }

            $get_amount = $request->amount;
            if ($invoice && $get_amount != 0) {
                if ($get_amount > $invoice->getDue()) {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                } else {
                    $paypage=new paypage();
                    $pay = $paypage->sendPaymentCode('all')
                        ->sendTransaction('sale')
                        ->sendCart(1, $get_amount, 'invoice payment')
                        ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                        ->sendURLs(
                            route('invoice.paytab.status', ['success' => 1, 'data' => $request->all(), $invoice->id, 'amount' => $get_amount]),
                            route('invoice.paytab.status', ['success' => 0, 'data' => $request->all(), $invoice->id, 'amount' => $get_amount])
                        )
                        ->sendLanguage('en')
                        ->sendFramed($on = false)
                        ->create_pay_page();
                    return $pay;
                }
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function PaytabGetPaymentCallback(Request $request, $invoice_id, $amount)
    {
        $invoice = Invoice::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        if ($invoice) {
            try {
                if ($request->success == "1") {
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user->id);
                    $invoice_payment->client_id      = $user->id;
                    $invoice_payment->amount         = $amount;
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->payment_id     = 0;
                    $invoice_payment->notes          = "";
                    $invoice_payment->payment_type   = 'PayTab';
                    $invoice_payment->save();
                    $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
                    if ($invoice_getdue <= 0.0) {

                        Invoice::change_status($invoice->id, 3);
                    } else {

                        Invoice::change_status($invoice->id, 2);
                    }
                    //Notification
                    $setting  = Utility::settingsById($objUser->creatorId());
                    if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                        $uArr = [
                            'amount' => $invoice_payment->amount,
                            'payment_type' => $invoice_payment->payment_type,
                            'user_name' => $invoice->name,
                        ];
                        Utility::send_twilio_msg($invoice->contacts->phone, 'new_invoice_payment', $uArr, $invoice->created_by);
                    }

                    //webhook
                    $module = 'New Invoice Payment';
                    $webhook =  Utility::webhookSetting($module, $invoice->created_by);
                    if ($webhook) {
                        $parameter = json_encode($invoice);
                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                        if ($status != true) {
                            $msg = "Webhook call failed.";
                        }
                    }
                    if (Auth::user()) {
                        return redirect()->route('invoice.show', $invoice_id)->with('success', __('Invoice paid Successfully!!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                    } else {
                        $id = \Crypt::encrypt($invoice_id);
                        return redirect()->route('pay.invoice', $id)->with('success', __('Invoice paid Successfully!!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                    }

                    if (Auth::check()) {
                        return redirect()->route('invoices.show', $invoice_id['invoice_id'])->with('success', __('Invoice paid Successfully!'));
                    } else {
                        return redirect()->route('pay.invoice', encrypt($invoice_id['invoice_id']))->with('ERROR', __('Transaction fail'));
                    }
                } else {
                    if (Auth::user()) {
                        return redirect()->route('invoice.show', $invoice_id)->with('error', __('Transaction fail!'));
                    } else {
                        $id = \Crypt::encrypt($invoice_id);
                        return redirect()->route('pay.invoice', $id)->with('error', __('Transaction fail!'));
                    }
                }
            } catch (\Exception $e) {

                if (Auth::check()) {
                    return redirect()->route('invoice.show', $invoice_id['invoice_id'])->with('error', $e->getMessage());
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', $e->getMessage());
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('invoices.show', $invoice_id['invoice_id'])->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice_id['invoice_id']))->with('success', __('Invoice not found.'));
            }
        }
    }
}
