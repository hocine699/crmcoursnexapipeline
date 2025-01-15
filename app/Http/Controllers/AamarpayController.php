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
use App\Models\Store;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Shipping;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class AamarpayController extends Controller
{

    public function planPayWithpay(Request $request)
    {
        $url = 'https://sandbox.aamarpay.com/request.php';
        $payment_setting = Utility::payment_settings();
        $aamarpay_store_id = $payment_setting['aamarpay_store_id'];
        $aamarpay_signature_key = $payment_setting['aamarpay_signature_key'];
        $aamarpay_description = $payment_setting['aamarpay_description'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $authuser = Auth::user();
        $plan = Plan::find($planID);

        if ($plan) {
            $get_amount = $plan->price;

            // if (Auth::user()->phone == null) {
            //     return redirect()->back()->with('failed', __('Please add phone number to your profile.'));
            // }
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
                                        'price_currency' => isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD',
                                        'txn_id' => '',
                                        'payment_type' => 'Aamarpay',
                                        'payment_status' => 'succeeded',
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
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $fields = array(
                    'store_id' => $aamarpay_store_id,
                    //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                    'amount' => $get_amount,
                    //transaction amount
                    'payment_type' => '',
                    //no need to change
                    'currency' => $currency,
                    //currenct will be USD/BDT
                    'tran_id' => $orderID,
                    //transaction id must be unique from your end
                    'cus_name' => $authuser->name,
                    //customer name
                    'cus_email' => $authuser->email,
                    //customer email address
                    'cus_add1' => '',
                    //customer address
                    'cus_add2' => '',
                    //customer address
                    'cus_city' => '',
                    //customer city
                    'cus_state' => '',
                    //state
                    'cus_postcode' => '',
                    //postcode or zipcode
                    'cus_country' => '',
                    //country
                    'cus_phone' => '1234567890',
                    //customer phone number
                    'success_url' => route('plan.aamarpay', Crypt::encrypt(['response' => 'success', 'coupon' => $coupon, 'plan_id' => $plan->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your success route
                    'fail_url' => route('plan.aamarpay', Crypt::encrypt(['response' => 'failure', 'coupon' => $coupon, 'plan_id' => $plan->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your fail route
                    'cancel_url' => route('plan.aamarpay', Crypt::encrypt(['response' => 'cancel'])),
                    //your cancel url
                    'signature_key' => $aamarpay_signature_key,
                    'desc' => $aamarpay_description,
                ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key


                $fields_string = http_build_query($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
                curl_close($ch);

                $this->redirect_to_merchant($url_forward);
            } catch (\Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    function redirect_to_merchant($url)
    {

        $token = csrf_token();
?>
        <html xmlns="http://www.w3.org/1999/xhtml">

        <head>
            <script type="text/javascript">
                function closethisasap() {
                    document.forms["redirectpost"].submit();
                }
            </script>
        </head>

        <body onLoad="closethisasap();">

            <form name="redirectpost" method="post" action="<?php echo 'https://sandbox.aamarpay.com/' . $url; ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </body>

        </html>
<?php
        exit;
    }

    public function getPaymentStatus($data, Request $request)
    {
        $data = Crypt::decrypt($data);
        $user = \Auth::user();
        if ($data['response'] == "success") {
            $plan = Plan::find($data['plan_id']);
            $couponCode = $data['coupon'];
            $getAmount = $data['price'];
            $orderID = $data['order_id'];
            if ($couponCode != 0) {
                $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
                $request['coupon_id'] = $coupons->id;
            } else {
                $coupons = null;
            }

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
            $order->payment_type = __('Aamarpay');
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
        } elseif ($data['response'] == "cancel") {
            return redirect()->route('plan.index')->with('error', __('Your payment is cancel'));
        } else {
            return redirect()->route('plan.index')->with('error', __('Your Transaction is fail please try again'));
        }
    }

    public function invoicePayWithaamarpay(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);

        $user = User::where('id', $invoice->created_by)->first();
        $url = 'https://sandbox.aamarpay.com/request.php';
        $payment_setting = Utility::invoice_payment_settings($user->id);
        $aamarpay_store_id = $payment_setting['aamarpay_store_id'];
        $aamarpay_signature_key = $payment_setting['aamarpay_signature_key'];
        $aamarpay_description = $payment_setting['aamarpay_description'];
        $currency = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';

        $setting = \App\Models\Utility::settings();

        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);

        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {

            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            try {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $fields = array(
                    'store_id' => $aamarpay_store_id,
                    //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                    'amount' => $get_amount,
                    //transaction amount
                    'payment_type' => '',
                    //no need to change
                    'currency' => $currency,
                    //currenct will be USD/BDT
                    'tran_id' => $orderID,
                    //transaction id must be unique from your end
                    'cus_name' => $user->name,
                    //customer name
                    'cus_email' => $user->email,
                    //customer email address
                    'cus_add1' => '',
                    //customer address
                    'cus_add2' => '',
                    //customer address
                    'cus_city' => '',
                    //customer city
                    'cus_state' => '',
                    //state
                    'cus_postcode' => '',
                    //postcode or zipcode
                    'cus_country' => '',
                    //country
                    'cus_phone' => '1234567890',
                    //customer phone number
                    'success_url' => route('invoice.aamarpay.status', Crypt::encrypt(['response' => 'success', 'invoice_id' => $invoice->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your success route
                    'fail_url' => route('invoice.aamarpay.status', Crypt::encrypt(['response' => 'failure', 'invoice_id' => $invoice->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your fail route
                    'cancel_url' => route('invoice.aamarpay.status', Crypt::encrypt(['response' => 'cancel'])),
                    //your cancel url
                    'signature_key' => $aamarpay_signature_key,
                    'desc' => $aamarpay_description,
                ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key
                $fields_string = http_build_query($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
                curl_close($ch);
                $this->redirect_to_merchant($url_forward);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e);
            }
        }
    }

    public function getInvociePaymentStatus($data)
    {
        $data = Crypt::decrypt($data);
        $getAmount = $data['price'];
        $invoice_id = $data['invoice_id'];
        $invoice = Invoice::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        if ($invoice) {
            if ($data['response'] == "success") {
                try {
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user->id);
                    $invoice_payment->client_id      = $user->id;
                    $invoice_payment->amount         = $getAmount;
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->payment_id     = 0;
                    $invoice_payment->notes          = "";
                    $invoice_payment->payment_type   = 'Aamarpay';
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
                        return redirect()->route('invoice.show', $invoice_id['invoice_id'])->with('success', __('Invoice paid Successfully!'));
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
                return redirect()->back()->with('error', __('Your Transaction is fail please try again'));
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
