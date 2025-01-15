<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class PaystackPaymentController extends Controller
{

    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;

    public function planPayWithPaystack(Request $request)
    {
        $this->planpaymentSetting();
        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $authuser       = Auth::user();
        $coupon_id = '';
        if ($plan) {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->price;

            if (isset($request->coupon) && !empty($request->coupon)) {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    if ($usedCoupun >= $coupons->limit) {
                        return Utility::error_res(__('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                    $coupon_id = $coupons->id;
                } else {
                    return Utility::error_res(__('This coupon code is invalid or has expired.'));
                }
            }
            if ($price <= 0) {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

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
                            'price' => $price,
                            'price_currency' => !empty($this->currancy) ? $this->currancy : 'usd',
                            'txn_id' => '',
                            'payment_type' => 'Paystack',
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 2;
                    return $res;
                } else {
                    return Utility::error_res(__('Plan fail to upgrade.'));
                }
            }
            $res_data['email'] = Auth::user()->email;
            $res_data['total_price'] = $price;
            $res_data['currency'] = $this->currancy;
            $res_data['flag'] = 1;
            $res_data['coupon'] = $coupon_id;
            return $res_data;
        } else {
            return Utility::error_res(__('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request, $pay_id, $plan)
    {

        $this->planpaymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan           = Plan::find($planID);
        $user = Auth::user();
        $result = array();

        if ($plan) {
            //The parameter after verify/ is the transaction reference to be verified
            $url = "https://api.paystack.co/transaction/verify/$pay_id";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: Bearer ' . $this->secret_key,
                ]
            );
            $result = curl_exec($ch);
            curl_close($ch);
            if ($result) {
                $result = json_decode($result, true);
            }
            $orderID = time();
            if (isset($result['status']) && $result['status'] == true) {
                if ($request->has('coupon_id') && $request->coupon_id != '')
                {
                    $coupons = Coupon::find($request->coupon_id);
                        $discount_value         = ($plan->price / 100) * $coupons->discount;
                        $discounted_price = $plan->price - $discount_value;

                    if(!empty($coupons))
                    {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
                        $userCoupon->save();

                        $usedCoupun = $coupons->used_coupon();
                        if($coupons->limit <= $usedCoupun)
                        {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                }
                $objUser                    = \Auth::user();
                $assignPlan = $objUser->assignPlan($plan->id);
                // if($assignPlan['is_success'])
                // {
                //     return redirect()->route('plan.index')->with('success', __('Plan activated Successfully!'));
                // }
                // $plan = \DB::table('plans')->find($request->plan_id);

                if ($assignPlan['is_success'] == true  && !empty($plan)) {
                    // if(!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '')
                    // {
                    //     try
                    //     {
                    //         $authuser->cancel_subscription($authuser->id);
                    //     }
                    //     catch(\Exception $exception)
                    //     {
                    //         \Log::debug($exception->getMessage());
                    //     }
                    // }

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                   $order =  Order::create(
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
                            'price_currency' => !empty(env('CURRENCY_CODE')) ? env('CURRENCY_CODE') : 'usd',
                            'txn_id' => '',
                            'payment_type' => __('paystack'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => \Auth::user()->id,
                        ]
                    );
                    if(!empty($objUser->referral_user)){
                        Utility::transaction($order);
                    }
                    return redirect()->route('plan.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->route('plan.index')->with('error', __($assignPlan['error']));
                }
            } else {
                return redirect()->route('plan.index')->with('error', __('Transaction fail'));
            }
        } else {
            return redirect()->route('plan.index')->with('error', __('Plan not found!'));
        }
    }

     public function invoicePayWithPaystack(Request $request){

        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
            'email' => 'required|email'
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        )->setAttributeNames(
            ['invoice_id' => 'Invoice']
        );
        if($validator->fails())
        {
            return Utility::error_res($validator->errors()->first());
        }

        $invoice = Invoice::find($request->invoice_id);

        $this->paymentSetting($invoice->created_by);

        $amount = number_format((float)$request->amount, 2, '.', '');

        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');

        if($invoice_getdue < $amount){
            return Utility::error_res('not correct amount');
        }

        $res_data['email'] = $request->email;
        $res_data['total_price'] = $request->amount;
        $res_data['currency'] = $this->currancy;
        $res_data['flag'] = 1;
        $res_data['invoice_id'] = $invoice->id;
        $request->session()->put('invoice_data', $res_data);
        $this->pay_amount =$request->amount;
        return $res_data;
    }

    public function getInvociePaymentStatus($pay_id,$invoice_id,Request $request){

        $encrypt_invoiceid = \Illuminate\Support\Facades\Crypt::encrypt(decrypt($invoice_id));


        if(!empty($invoice_id) && !empty($pay_id))
        {
            $invoice_id = decrypt($invoice_id);
            $invoice    = Invoice::find($invoice_id);

            $this->paymentSetting($invoice->created_by);
            $objUser = \Auth::user();
                    $user = User::where('id',$invoice->created_by)->first();
                    $objUser = $user;

            $invoice_data =  $request->session()->get('invoice_data') ;
            if($invoice && !empty($invoice_data))
            {

                $url = "https://api.paystack.co/transaction/verify/$pay_id";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, [
                           'Authorization: Bearer ' . $this->secret_key,
                       ]
                );
                $result = curl_exec($ch);
                curl_close($ch);
                if($result)
                {
                    $result = json_decode($result, true);
                }
                if(isset($result['status']) && $result['status'] == true)
                {

                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($invoice->created_by);
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->amount         = isset($invoice_data['total_price'])?$invoice_data['total_price']:0;
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->payment_id     = 0;
                    $invoice_payment->payment_type   = __('Paystack');
                    $invoice_payment->client_id      = 0;
                    $invoice_payment->notes          = '';
                    $invoice_payment->save();

                    $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');

                    if($invoice_getdue <= 0.0)
                    {

                        Invoice::change_status($invoice->id, 3);
                    }
                    else{

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
                    $webhook =  Utility::webhookSetting($module,$invoice->created_by);
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
            }else{
                return redirect()->route('pay.invoice',$encrypt_invoiceid)->with('error', __('Invoice not found.'));
            }
        }else{
            return redirect()->route('pay.invoice',$encrypt_invoiceid)->with('error', __('Invoice not found.'));
        }
    }

    public function paymentSetting($id)
    {
        $payment_setting = Utility::invoice_payment_settings($id);

        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';

        $this->secret_key = isset($payment_setting['paystack_secret_key'])?$payment_setting['paystack_secret_key']:'';
        $this->public_key = isset($payment_setting['paystack_public_key'])?$payment_setting['paystack_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled'])?$payment_setting['is_paystack_enabled']:'off';
        return $this;
    }

    public function planpaymentSetting()
    {
        $payment_setting = Utility::payment_settings();

        $this->currancy = isset($payment_setting['currency'])?$payment_setting['currency']:'';

        $this->secret_key = isset($payment_setting['paystack_secret_key'])?$payment_setting['paystack_secret_key']:'';
        $this->public_key = isset($payment_setting['paystack_public_key'])?$payment_setting['paystack_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled'])?$payment_setting['is_paystack_enabled']:'off';
        return $this;
    }
}
