<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaypalController extends Controller
{

    public $paypal_client_id;
    public $paypal_mode;
    public $paypal_secret_key;
    public $currancy_symbol;
    public $currancy;

    public function planPayWithPaypal(Request $request)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan   = Plan::find($planID);
        $user = Auth::user();
        $authuser       = Auth::user();

        $this->paymentconfig($user);
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $get_amount = $plan->price;

        if ($plan) {
            try {
                $coupon_id = null;
                $price     = $plan->price;
                if (!empty($request->coupon) && !empty($request->coupon)) {
                    $request->coupon = trim($request->coupon);
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    $discount_value         = ($plan->price / 100) * $coupons->discount;
                    $discounted_price = $plan->price - $discount_value;
                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;
                        $coupon_id = $coupons->id;
                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                if ($price <= 0) {
                    $authuser->plan = $plan->id;
                    $authuser->save();

                    $assignPlan = $authuser->assignPlan($plan->id, $request->paypal_payment_frequency);

                    if ($assignPlan['is_success'] == true && !empty($plan)) {

                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
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
                                'price' => isset($coupons) ? $discounted_price : $plan->price,
                                'price_currency' => !empty($this->currancy) ? $this->currancy : 'usd',
                                'txn_id' => '',
                                'payment_type' => 'Paypal',
                                'payment_status' => 'succeeded',
                                'receipt' => null,
                                'user_id' => $authuser->id,
                            ]
                        );
                        // $assignPlan = $authuser->assignPlan($plan->id, $request->paypal_payment_frequency);
                        return redirect()->route('plan.index')->with('success', __('Plan activated Successfully!'));
                    } else {
                        return Utility::error_res(__('Plan fail to upgrade.'));
                    }
                }
                if (!empty($coupons)) {
                    $userCoupon         = new UserCoupon();
                    $userCoupon->user   = $user->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->save();
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }
                $this->paymentConfig($user);
                $paypalToken = $provider->getAccessToken();
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('plan.get.payment.status', [$plan->id, $get_amount]),
                        "cancel_url" =>  route('plan.get.payment.status', [$plan->id, $get_amount]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD',
                                "value" => $get_amount
                            ]
                        ]
                    ]
                ]);

                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('plan.index')
                        ->with('error', 'Something went wrong.');
                } else {

                    return redirect()
                        ->route('plan.index')
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }
            } catch (\Exception $e) {

                return redirect()->route('plan.index')->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id, $amount)
    {
        $user = Auth::user();
        $plan = Plan::find($plan_id);

        if ($plan) {

            $this->paymentconfig($user);
            $provider = new PayPalClient;

            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $payment_id = Session::get('paypal_payment_id');

            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {

                if ($response['status'] == 'COMPLETED') {
                    $statuses = 'succeeded';
                }
                $order                 = new Order();
                $order->order_id       = $order_id;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $amount;
                $order->price_currency = !empty($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : 'USD';
                $order->txn_id         = '';
                $order->payment_type   = 'PAYPAL';
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);                       
                }

                $assignPlan = $user->assignPlan($plan->id);
                if ($assignPlan['is_success']) {    

                    return redirect()->route('plan.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plan.index')->with('error', __($assignPlan['error']));
                }

                return redirect()
                    ->route('plan.index')
                    ->with('success', 'Transaction complete.');
            } else {
                return redirect()
                    ->route('plan.index')
                    ->with('error', $response['message'] ?? 'Something went wrong.');
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function clientPayWithPaypal(Request $request, $invoice_id)
    {

        // $user = Auth::user();
        $invoice = Invoice::find($request->invoice_id);

        $this->paymentSetting($invoice->created_by);

        $invoice                 = Invoice::find($invoice_id);
        $this->invoiceData       = $invoice;
        $settings                = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');

        $get_amount = $request->amount;
        $request->validate(['amount' => 'required|numeric|min:0']);

        $provider = new PayPalClient;

        $provider->setApiCredentials(config('paypal'));

        if ($invoice) {
            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                // $name = Auth::user()->invoiceNumberFormat($invoice->invoice_id);

                $paypalToken = $provider->getAccessToken();
                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('client.get.payment.status', [$invoice->id, $get_amount]),
                        "cancel_url" =>  route('client.get.payment.status', [$invoice->id, $get_amount]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                                "value" => $get_amount
                            ]
                        ]
                    ]
                ]);

                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('invoice.show', \Crypt::encrypt($invoice->id))
                        ->with('error', 'Something went wrong.');
                } else {
                    return redirect()
                        ->route('invoice.show', \Crypt::encrypt($invoice->id))
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }

                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function clientGetPaymentStatus(Request $request, $invoice_id, $amount)
    {
        $invoice = Invoice::find($invoice_id);
        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $this->paymentConfig($invoice->created_by);
        $objUser = \Auth::user();
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        // $this->paymentSetting($invoice->created_by);

        $encrypt_invoiceid = \Illuminate\Support\Facades\Crypt::encrypt($invoice_id);

        if ($invoice) {
            // $this->setApiContext($user);

            $payment_id = Session::get('paypal_payment_id');

            Session::forget('paypal_payment_id');

            if (empty($request->PayerID || empty($request->token))) {
                return redirect()->route('pay.invoice', $encrypt_invoiceid)->with('error', __('Payment failed'));
            }
            // try
            // {

            // $payment   = Payment::get($payment_id, $this->_api_context);

            // $execution = new PaymentExecution();
            // $execution->setPayerId($request->PayerID);


            // $result = $payment->execute($execution, $this->_api_context)->toArray();

            $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

            // if($result['state'] == 'approved')
            // {
            $invoice_payment = new InvoicePayment();
            $invoice_payment->transaction_id =  app('App\Http\Controllers\InvoiceController')->transactionNumber($invoice->created_by);
            $invoice_payment->invoice_id = $invoice->id;
            $invoice_payment->amount = $amount;
            $invoice_payment->date = date('Y-m-d');
            $invoice_payment->payment_id = 0;
            $invoice_payment->payment_type = __('PAYPAL');
            $invoice_payment->client_id = 0;
            $invoice_payment->notes = '';
            $invoice_payment->save();

            $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');

            if ($invoice_getdue <= 0.0) {

                Invoice::change_status($invoice->id, 3);
            } else {

                Invoice::change_status($invoice->id, 2);
            }

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
                return redirect()->route('invoice.show', $invoice_id)->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                $id = \Crypt::encrypt($invoice_id);
                return redirect()->route('pay.invoice', $id)->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }
        }
        // return redirect()->route('pay.invoice', $encrypt_invoiceid)->with('success', __('Payment added Successfully'));
        // }
        // else
        // {
        //     return redirect()->route('pay.invoice',$encrypt_invoiceid)->with('error', __('Transaction has been ' . $status));
        // }
        // } catch(\Exception $e) {
        //     return redirect()->route('pay.invoice',$encrypt_invoiceid)->with('error', __('Transaction has been failed!'));
        // }
        else {
            return redirect()->route('pay.invoice', $encrypt_invoiceid)->with('error', __('Permission denied.'));
        }
    }

    public function paymentSetting($id)
    {

        $payment_setting = Utility::invoice_payment_settings($id);

        if ($payment_setting['paypal_mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }
    }

    public function paymentConfig()
    {
        $payment_setting = Utility::payment_settings();

        if ($payment_setting['paypal_mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }
    }
}
