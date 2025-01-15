<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use Exception;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\User;
use Google\Service\Fitness\Session;
use Google\Service\ServiceControl\Auth;
use Illuminate\Support\Facades\DB;

class ToyyibpayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData;

    public function __construct()
    {

        $payment_setting = Utility::payment_settings();


        $this->secretKey = isset($payment_setting['toyyibpay_secret_key']) ? $payment_setting['toyyibpay_secret_key'] : '';
        $this->categoryCode = isset($payment_setting['category_code']) ? $payment_setting['category_code'] : '';
        $this->is_enabled = isset($payment_setting['is_toyyibpay_enabled']) ? $payment_setting['is_toyyibpay_enabled'] : 'off';
    }

    public function index()
    {
        return view('payment');
    }

    public function planPayWithToyyibpay(Request $request)
    {
        try {

            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan   = Plan::find($planID);
            if ($plan) {

                // $get_amount = $plan->price;
                $discounted_price = $plan->price;

                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value         = ($plan->price / 100) * $coupons->discount;
                        $discounted_price = $plan->price - $discount_value;


                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code   is invalid or has expired.'));
                    }
                }
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $this->callBackUrl = route('plan.toyyibpay', [$plan->id, $discounted_price, $coupon]);
                $this->returnUrl = route('plan.toyyibpay', [$plan->id, $discounted_price, $coupon]);

                $Date = date('d-m-Y');
                $ammount = $discounted_price;
                $billName = $plan->name;
                $description = $plan->name;
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";
                $some_data = array(
                    'userSecretKey' => $this->secretKey,
                    'categoryCode' => $this->categoryCode,
                    'billName' => $billName,
                    'billDescription' => $description,
                    'billPriceSetting' => 1,
                    'billPayorInfo' => 1,
                    'billAmount' => 100 * $ammount,
                    'billReturnUrl' => $this->returnUrl,
                    'billCallbackUrl' => $this->callBackUrl,
                    'billExternalReferenceNo' => 'AFR341DFI',
                    'billTo' => \Auth::user()->username,
                    'billEmail' => \Auth::user()->email,
                    'billPhone' => '0000000000',
                    'billSplitPayment' => 0,
                    'billSplitPaymentArgs' => '',
                    'billPaymentChannel' => '0',
                    'billContentEmail' => $billContentEmail,
                    'billChargeToCustomer' => 1,
                    'billExpiryDate' => $billExpiryDate,
                    'billExpiryDays' => $billExpiryDays
                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
            } else {
                return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plan.index')->with('error', __($e->getMessage()));
        }
    }

    public function planGetPaymentStatus(Request $request, $planId, $amount, $couponCode = null)
    {
        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        $plan = Plan::find($planId);
        $user = auth()->user();
        // $request['status_id'] = 1;

        // 1=success, 2=pending, 3=fail
        try {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($request->status_id == 3) {
                $statuses = 'Fail';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $amount;
                $order->price_currency = env('CURRENCY');
                $order->payment_type   = __('Toyyibpay');
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);
                }
                // return redirect()->route('plan.index')->with('error', __('Your Transaction is fail please try again'));
                return redirect()->route('plan.index')->with('error', __('Your Transaction is fail please try again'));
            } else if ($request->status_id == 2) {
                $statuses = 'pandding';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $amount;
                $order->price_currency = env('CURRENCY');
                $order->payment_type   = __('Toyyibpay');
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);
                }
                return redirect()->route('plan.index')->with('success', __('Your transaction on pandding'));
            } else if ($request->status_id == 1) {
                $statuses = 'success';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $amount;
                $order->price_currency = env('CURRENCY');
                $order->payment_type   = __('Toyyibpay');
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);
                }
                $assignPlan = $user->assignPlan($plan->id);
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
                    if (!empty($coupons)) {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
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
                return redirect()->route('plan.index')->with('error', __('Plan is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->route('plan.index')->with('error', __($e->getMessage()));
        }
    }


    // invoice

    public function invoicepaywithtoyyibpay(Request $request)
    {
        $user    = \Auth::user();

        $invoice_id = $request->input('invoice_id');
        $invoice = Invoice::find($invoice_id);
        $this->invoiceData  = $invoice;
        $settings = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');
        $setting = \App\Models\Utility::settings();

        $get_amount = $request->amount;
        $user = User::where('id', $invoice->user_id)->first();

        if ($invoice && $get_amount != 0) {

            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $name = isset($user->name) ? $user->name : 'public' . " - " . $invoice->invoice_id;

                $this->callBackUrl = route('invoice.toyyibpay.status', [$invoice->id, $get_amount]);
                $this->returnUrl = route('invoice.toyyibpay.status', [$invoice->id, $get_amount]);
            }

            $Date = date('d-m-Y');
            $ammount = $get_amount;
            $billExpiryDays = 3;
            $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
            $billContentEmail = "Thank you for purchasing our product!";

            $some_data = array(
                'userSecretKey' => $this->secretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => "invoice",
                'billDescription' => "invoice",
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => 100 * $ammount,
                'billReturnUrl' => $this->returnUrl,
                'billCallbackUrl' => $this->callBackUrl,
                'billExternalReferenceNo' => 'AFR341DFI',
                'billTo' => $user->name,
                'billEmail' => $user->email,
                'billPhone' => '0000000000',
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => '0',
                'billContentEmail' => $billContentEmail,
                'billChargeToCustomer' => 1,
                'billExpiryDate' => $billExpiryDate,
                'billExpiryDays' => $billExpiryDays,
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $obj = json_decode($result);
            if ($obj != null) {
                try {
                    return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);
                } catch (Exception $e) {
                    return redirect()->route('invoice.show', \Crypt::encrypt($invoice_id))->back()->with('error', __('Unknown error occurred'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoicetoyyibpaystatus(Request $request, $invoice_id, $amount)
    {
        $invoice = Invoice::find($invoice_id);
        $objUser = \Auth::user();
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        if (\Auth::check()) {
            $settings = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');
            $user     = \Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
            $settings = Utility::payment_settings($invoice->created_by);
        }

        $payment_id = \Session::get('toyyib_id');
        \Session::forget('toyyib_id');
        if (empty($request->PayerID || empty($request->token))) {
            return redirect()->route('invoices.show', $invoice_id)->with('error', __('Payment failed'));
        }
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));

        try {

            if ($request->return_type == 'success') {
                $new                 = new \App\Models\Payment();
                $new->invoice_id     = $invoice_id;
                $new->transacrion_id = '';
                $new->date           = Date('Y-m-d');
                $new->amount         = $amount;
                $new->client_id      = isset(\Auth::user()->id) ? \Auth::user()->id : 0;
                $new->description    = '';
                $new->payment_method = 'Toyyibpay';
                $new->save();
                if ($invoice->dueAmount() == 0) {
                    $invoice->status = 'Paid';
                } else {
                    $invoice->status = 'Partialy Paid';
                }
                $invoice->save();
            } else {
                if (\Auth::check()) {
                    return redirect()->route('invoice.show', $invoice_id)->with('error', __('Transaction fail'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('error', __('Transaction fail'));
                }
            }

            $setting  = Utility::settingsById($objUser->creatorId());
            if (isset($setting['payment_notification']) && $setting['payment_notification'] == 1) {
                $uArr = [
                    'amount' => $new->amount,
                    'payment_type' => $new->payment_type,
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
        } catch (\Exception $e) {
            if (\Auth::check()) {
                return redirect()->route('pay.invoice', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
            } else {
                return redirect()->back()->with('success', __('Transaction has been complted.'));
            }
        }
    }
}
