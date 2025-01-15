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

class PaymentWallController extends Controller
{
    public $currancy;

	public function planpay(Request $request)
    {
    	$data=$request->all();
    	 $admin_payment_setting = Utility::payment_settings();
    	return view('plan.planpay',compact('data','admin_payment_setting'));

    }

    public function invoicepay(Request $request)
    {
        $data=$request->all();
        $admin_payment_setting = Utility::payment_settings();
        return view('invoice.paymentwallpay',compact('data','admin_payment_setting'));

    }

    public function planerror(Request $request,$flag)
    {
          if($flag == 1){
            return redirect()->route("plan.index")->with('success', __('Plan activated Successfully! '));
        }else{
                return redirect()->route("plan.index")->with('error', __('Transaction has been failed! '));
        }

    }

        public function invoiceerror(Request $request,$flag,$invoice_id)
    {
        if(\Auth::check())
        {
            if($flag == 1){
                     return redirect()->route('invoice.show',$invoice_id)->with('success', __('Payment added Successfully'));
            }else{
                    return redirect()->route('invoice.show',$invoice_id)->with('error', __('Transaction has been failed! '));
            }

        }
        else
        {
            if($flag == 1){
                     return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully '));
            }else{
                    return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Transaction has been failed! '));
            }
        }

    }




   public function planPayWithPaymentWall(Request $request,$plan_id)
   {
        $admin_payment_setting = Utility::payment_settings();
        $this->planpaymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan           = Plan::find($planID);

        $authuser       = Auth::user();
        $coupon_id ='';
        if($plan)
        {

            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->price;

            if(isset($request->coupon) && !empty($request->coupon))
            {
                $request->coupon = trim($request->coupon);

                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    if($usedCoupun >= $coupons->limit)
                    {
                        return Utility::error_res( __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                    $coupon_id = $coupons->id;

                }
                else
                {
                    return Utility::error_res( __('This coupon code is invalid or has expired.'));
                }
            }
            if($price <= 0)
            {

                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id,$request->paymentwall_payment_frequency   );

                if($assignPlan['is_success'] == true && !empty($plan))
                {

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
                            'payment_type' => 'PaymentWall',
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 1;
                    return $res;
                }
                else
                {
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 2;
                    return $res;
                }
            }
            else
            {

                \Paymentwall_Config::getInstance()->set(array(

                    'private_key' => $admin_payment_setting['paymentwall_private_key']
                ));

                $parameters = $request->all();

                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(),true);
                $response = $charge->getPublicData();
                $user = Auth::user();
                $orderID = time();
                if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                       if($request->has('coupon') && $request->coupon != '')
                        {
                            $coupons = Coupon::find($request->coupon_id);
                            $discount_value         = ($plan->price / 100) * $coupons->discount;
                            $discounted_price = $plan->price - $discount_value;
                            if(!empty($coupons))
                            {
                                $userCoupon            = new UserCoupon();
                                $userCoupon->user   = $user->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order  = $orderID;
                                $userCoupon->save();
                                $discount_value         = ($price / 100) * $coupons->discount;
                                $price = $price - $discount_value;
                                $usedCoupun = $coupons->used_coupon();
                                if($coupons->limit <= $usedCoupun)
                                {
                                    $coupons->is_active = 0;
                                    $coupons->save();
                                }
                            }
                        }

                        $order                 = new Order();
                        $order->order_id       = $orderID;
                        $order->name           = $authuser->name;
                        $order->card_number    = '';
                        $order->card_exp_month = '';
                        $order->card_exp_year  = '';
                        $order->plan_name      = $plan->name;
                        $order->plan_id        = $plan->id;
                        $order->price          = isset($coupons) ? $discounted_price : $plan->price;
                        $order->price_currency = $this->currancy;
                        $order->txn_id         = isset($paydata['txid']) ? $paydata['txid'] : 0;
                        $order->payment_type   = __('PaymentWall');
                        $order->payment_status = 'succeeded';
                        $order->receipt        = '';
                        $order->user_id        = $authuser->id;
                        $order->save();
                        if(!empty($authuser->referral_user)){
                            Utility::transaction($order);
                        }
                        $assignPlan = $authuser->assignPlan($plan->id);

                        if($assignPlan['is_success'])
                        {
                             $res['flag'] = 1;
                             return $res;

                        }
                    } elseif ($charge->isUnderReview()) {
                          $res['flag'] = 2;
                             return $res;
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    $errors = json_decode($response, true);
                    $res['msg'] = __("Trasnsaction has been Fail.");

                    $res['flag'] = 2;
                    return $res;
                }

            }

            $res['flag'] = 2;
            return $res;
        }
        else
        {
            $res['flag'] = 2;
            return $res;
        }
    }

    public function invoicePayWithPaymentWall(Request $request,$invoice_id){

        $admin_payment_setting = Utility::payment_settings();
        $invoice = Invoice::find($invoice_id);

        $this->paymentSetting($invoice->created_by);
        $objUser = \Auth::user();
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        $amount = number_format((float)$request['amount'], 2, '.', '');

        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');

        if($invoice_getdue < $amount){
            return Utility::error_res('not correct amount');
        }

        $res_data['email'] = $request['email'];
        $res_data['total_price'] = $request['amount'];
        $res_data['currency'] = $this->currancy;
        $res_data['flag'] = 1;
        $res_data['invoice_id'] = $invoice->id;
        $request->session()->put('invoice_data', $res_data);
        $this->pay_amount =$request['amount'];

            \Paymentwall_Config::getInstance()->set(array(

                    'private_key' => $admin_payment_setting['paymentwall_private_key']
                ));

                $parameters = $request->all();

                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => isset($request['amount'])?$request['amount']:0,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(),true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                      $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($invoice->created_by);
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->amount         = isset($request['amount'])?$request['amount']:0;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   = __('PaymentWall');
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
                }
                 else {
                    $errors = json_decode($response, true);
                    $res['invoice']=$invoice_id;
                     $res['flag'] = 2;
                     return $res;
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

        $this->secret_key = isset($payment_setting['paymentwall_private_key'])?$payment_setting['paymentwall_private_key']:'';
        $this->public_key = isset($payment_setting['paymentwall_public_key'])?$payment_setting['paymentwall_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled'])?$payment_setting['is_paymentwall_enabled']:'off';
        return $this;
    }
}
