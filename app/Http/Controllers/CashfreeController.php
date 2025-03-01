<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\PlanOrder;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\Shipping;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class CashfreeController extends Controller
{
    public function planPayWithcashfree(Request $request)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $user = \Auth::user();
        $payment_setting = Utility::payment_settings();
            config(
                [
                    'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
                    'services.currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',

                    ]
                );
        $url = config('services.cashfree.url');
        if ($plan) {

            $get_amount = $plan->price;

            try {
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
                            $authuser = \Auth::user();
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
                                        'price_currency' =>isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
                                        'txn_id' => '',
                                        'payment_type' => 'Cashfree',
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
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: 2022-01-01",
                    "x-client-id: " . config('services.cashfree.key'),
                    "x-client-secret: " . config('services.cashfree.secret')
                );
                $data = json_encode([
                    'order_id' => $orderID,
                    'order_amount' => $get_amount,
                    "order_currency" => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
                    "order_name" => $plan->name,
                    "customer_details" => [
                        "customer_id" => 'customer_' . $user->id,
                        "customer_name" => $user->name,
                        "customer_email" => $user->email,
                        "customer_phone" => '1234567890',
                    ],
                    "order_meta" => [
                        "return_url" => route('plan.cashfree') . '?order_id={order_id}&order_token={order_token}&plan_id=' . $plan->id . '&amount=' . $get_amount . '&coupon=' . $coupon . ''

                        ]
                    ]);
                    try {

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    return redirect()->to(json_decode($resp)->payment_link);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }
            } catch (\Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request)
    {
        $payment_setting = Utility::payment_settings();
            config(
                [
                    'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
                    'services.currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',

                    ]
                );
        $user = \Auth::user();
        $plan = Plan::find($request->plan_id);
        $couponCode = $request->coupon;
        $getAmount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    "x-client-id" => config('services.cashfree.key'),
                    "x-client-secret" => config('services.cashfree.secret')
                ],
            ]);


            $respons = json_decode($response->getBody());
            if ($respons->order_id && $respons->cf_payment_id != NULL) {

                $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                    'headers' => [
                        'accept' => 'application/json',
                        'x-api-version' => '2022-09-01',
                        'x-client-id' => config('services.cashfree.key'),
                        'x-client-secret' => config('services.cashfree.secret'),
                    ],
                ]);
                $info = json_decode($response->getBody());


                if ($info->payment_status == "SUCCESS") {

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
                    $order->payment_type = __('Cashfree');
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
            } else {
                return redirect()->route('plan.index')->with('error', 'Payment Failed.');
            }
            return redirect()->route('plan.index')->with('success', 'Plan activated Successfully.');
        } catch (\Exception $e) {
            return redirect()->route('plan.index')->with('error', __($e->getMessage()));
        }
    }

    public function invoicePayWithcashfree(Request $request)
    {

        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);
        $url = config('services.cashfree.url');
        
        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $payment_settings = Utility::invoice_payment_settings($user->id);
        config(
            [
                'services.cashfree.key' => isset($payment_settings['cashfree_api_key']) ? $payment_settings['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($payment_settings['cashfree_secret_key']) ? $payment_settings['cashfree_secret_key'] : '',
                'services.currency' => isset($payment_settings['currency']) ? $payment_settings['currency'] : 'USD',

                ]
            );
        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {

            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }

            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: " . config('services.cashfree.key'),
                "x-client-secret: " . config('services.cashfree.secret')
            );
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $data = json_encode([
                'order_id' => $orderID,
                'order_amount' => $get_amount,
                "order_currency" =>isset($payment_settings['currency']) ? $payment_settings['currency'] : 'USD',
                "customer_details" => [
                    "customer_id" => 'customer_' . $user->id,
                    "customer_name" => $user->name,
                    "customer_email" => $user->email,
                    "customer_phone" => '1234567890',
                ],
                "order_meta" => [
                    "return_url" => route('invoice.cashfree.status') . '?order_id={order_id}&invoice_id=' . $invoice_id . '&amount=' . $get_amount
                    ]
                ]);
            try {

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $resp = curl_exec($curl);
                curl_close($curl);
                return redirect()->to(json_decode($resp)->payment_link);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
            }
        }
    }

    public function getInvociePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice    = Invoice::find($request->invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));

        $payment_setting = Utility::invoice_payment_settings($user->id);
        config(
            [
                'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
                'services.currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',

            ]
        );
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
            'headers' => [
                'accept' => 'application/json',
                'x-api-version' => '2022-09-01',
                "x-client-id" => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret')
            ],
        ]);
        $respons = json_decode($response->getBody());
        if ($respons->order_id && $respons->cf_payment_id != NULL) {

            $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    'x-client-id' => config('services.cashfree.key'),
                    'x-client-secret' => config('services.cashfree.secret'),
                ],
            ]);
            $info = json_decode($response->getBody());
            try {
                if ($info->payment_status == "SUCCESS") {

                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->invoice_id     = $invoice_id;
                $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user->id);
                $invoice_payment->client_id      = $user->id;
                $invoice_payment->amount         = $request->amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->payment_id     = 0;
                $invoice_payment->notes          = "";
                $invoice_payment->payment_type   = 'Cashfree';
                $invoice_payment->save();
                $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
                if ($invoice_getdue <= 0.0) {

                    Invoice::change_status($invoice->id, 3);
                } else {

                    Invoice::change_status($invoice->id, 2);
                }
            }else {
                return redirect()->back()->with('error', __('Your Transaction is fail please try again'));
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
