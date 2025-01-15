<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Store;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Shipping;
use Google\Service\Fitness\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Catch_;

class SspayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData,$currency;

    public function __construct()
    {

        $payment_setting = Utility::payment_settings();

        $this->secretKey = isset($payment_setting['sspay_secret_key']) ? $payment_setting['sspay_secret_key'] : '';
        $this->categoryCode                = isset($payment_setting['sspay_category_code']) ? $payment_setting['sspay_category_code'] : '';
        $this->is_enabled          = isset($payment_setting['is_sspay_enabled']) ? $payment_setting['is_sspay_enabled'] : 'off';
        return $this;
    }

    public function SspayPaymentPrepare(Request $request)
    {
        try {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan   = Plan::find($planID);

            if($plan)
            {
                $coupon_id = null;
                $price= $plan->price;

                if(!empty($request->coupon))
                {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if(!empty($coupons))
                    {
                        $usedCoupun     = $coupons->used_coupon();

                        $discount_value         = ($plan->price / 100) * $coupons->discount;
                        $price = $plan->price - $discount_value;

                        if($coupons->limit == $usedCoupun)
                        {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        $coupon_id = $coupons->id;
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                    if($price <= 0)
                    {
                        $authuser   = \Auth::user();
                        $authuser->plan = $plan->id;
                        $authuser->save();
                        $assignPlan = $authuser->assignPlan($plan->id);
                        $coupons = Coupon::find($coupon_id);
                        $user = \Auth::user();
                        $orderID = time();
                        if(!empty($coupons))
                        {
                            $userCoupon            = new UserCoupon();
                            $userCoupon->user   = $user->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order  = $orderID;
                            $userCoupon->save();
                            $usedCoupun = $coupons->used_coupon();
                            if($coupons->limit == $usedCoupun)
                            {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                        if($assignPlan['is_success'] == true && !empty($plan))
                        {
                            if(!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '')
                            {
                                try
                                {
                                    $authuser->cancel_subscription($authuser->id);
                                }
                                catch(\Exception $exception)
                                {
                                    \Log::debug($exception->getMessage());
                                }
                            }
                            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                            Order::create(
                                [
                                    'order_id'        => $orderID,
                                    'name'            => null,
                                    'email'           => null,
                                    'card_number'     => null,
                                    'card_exp_month'  => null,
                                    'card_exp_year'   => null,
                                    'plan_name'       => $plan->name,
                                    'plan_id'         => $plan->id,
                                    'price'           => $price==null?0:$price,
                                    'price_currency'  => !empty($this->currency) ? $this->currency : 'USD',
                                    'txn_id'          => '',
                                    'payment_type'    => __('Sspay'),
                                    'payment_status'  => 'succeeded',
                                    'receipt'         => null,
                                    'user_id'         => $authuser->id,
                                ]
                            );

                            $assignPlan = $authuser->assignPlan($plan->id , $plan->price);

                            return redirect()->route('plan.index')->with('success',__('Plan Successfully Activated'));
                        }
                    }
                }
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $this->callBackUrl = route('plan.sspay.callback', [$plan->id, $price, $coupon]);
                $this->returnUrl = route('plan.sspay.callback', [$plan->id, $price, $coupon]);
                $Date = date('d-m-Y');
                $ammount = $price;
                $description = !empty($plan->description) ?  $plan->description : $plan->name;
                $billName = $plan->name;
                $billExpiryDays = 3;
                $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                $billContentEmail = "Thank you for purchasing our product!";
                $user = \Auth::user();

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
                    'billTo' => !empty($user->name) ? $user->name : '',
                    'billEmail' => !empty($user->email) ? $user->email : '',
                    'billPhone' => !empty($user->phone_no) ? $user->phone_no : '0000000000',
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
                curl_setopt($curl, CURLOPT_URL, 'https://sspay.my/index.php/api/createBill');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                $result = curl_exec($curl);
                $info = curl_getinfo($curl);
                curl_close($curl);
                $obj = json_decode($result);
                return redirect('https://sspay.my/' . $obj[0]->BillCode);

            }
            else
            {
                return redirect()->route('plan.index')->with('error',__('Plan is Deleted'));
            }
            } catch (Exception $e) {
                return $e->getMessage();

            }
  }

    public function SspayPlanGetPayment(Request $request, $planId, $getAmount, $couponCode)
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
                $order->price          = $getAmount;
                $order->price_currency = isset($this->currency) ? $this->currency : 'USD';
                $order->payment_type   = __('Sspay');
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);
                }
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
                $order->price          = $getAmount;
                $order->price_currency = isset($this->currency) ? $this->currency : 'USD';
                $order->payment_type   = __('Sspay');
                $order->payment_status = $statuses;
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();
                if(!empty($user->referral_user)){
                    Utility::transaction($order);
                }
                return redirect()->route('plan.index')->with('error', __('Your transaction on pending'));
            } else if ($request->status_id == 1) {
                $statuses = 'succeeded';
                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $getAmount;
                $order->price_currency = isset($this->currency) ? $this->currency : 'USD';
                $order->payment_type   = __('Sspay');
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

    public function invoicepaywithsspaypay(Request $request)
    {
        $invoice_id =$request->input('invoice_id');
        $invoice = Invoice::find($invoice_id);
        $this->invoiceData = $invoice;
        $settings = DB::table('settings')->where('created_by', '=' ,$invoice->created_by)->get()->pluck('value','name');
        $setting = \App\Models\Utility::settings();
        $get_amount = $request->amount;

        $user = User::where('id',$invoice->user_id)->first();
        if ($invoice) {

            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $name = isset($user->name) ? $user->name : 'public' . " - " . $invoice->invoice_id;
                $this->callBackUrl = route('customer.sspay', [$invoice->id, $get_amount]);
                $this->returnUrl = route('customer.sspay', [$invoice->id, $get_amount]);
            }
            $Date = date('d-m-Y');
            $description = !empty($invoice->description) ?  $invoice->description : $invoice->name;
            $billName = $invoice->name;
            $billExpiryDays = 3;
            $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
            $billContentEmail = "Thank you for purchasing our product!";
            $user = User::where('id',$invoice->user_id)->first();

            $some_data = array(
                'userSecretKey' => $this->secretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => $billName,
                'billDescription' => $description,
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => 100 * $get_amount,
                'billReturnUrl' => $this->returnUrl,
                'billCallbackUrl' => $this->callBackUrl,
                'billExternalReferenceNo' => 'AFR341DFI',
                'billTo' => !empty($user->name) ? $user->name : '',
                'billEmail' => !empty($user->email) ? $user->email : '',
                'billPhone' => !empty($user->phone_no) ? $user->phone_no : '0000000000',
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
            curl_setopt($curl, CURLOPT_URL, 'https://sspay.my/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $obj = json_decode($result);
            return redirect('https://sspay.my/' . $obj[0]->BillCode);
            return redirect()
            ->route('invoice.show', \Crypt::encrypt($invoice->id))
            ->with('error', 'Something went wrong.');
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount)
    {
        $invoice = Invoice::find($invoice_id);
        $this->invoiceData = $invoice;
        $settings  = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');
        // $payment_id = Session::get('PayerID');
        // Session::forget('PayerID');
        // if (empty($request->PayerID || empty($request->token))) {
        //     return redirect()->back()->with('error', __('Payment failed'));
        // }
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        try
        {

            if ($request->status_id == 3)
            {

                return redirect()->route('pay.invoice', Crypt::encrypt($invoice->id))->with('error', __('Your Transaction is fail please try again'));
            }
            else if ($request->status_id == 2)
            {
                return redirect()->route('pay.invoice', Crypt::encrypt($invoice->id))->with('error', __('Your Transaction on pending'));
            }
            else if ($request->status_id == 1)
            {
                $new                 = new \App\Models\Payment();
                $new->invoice_id     = $invoice_id;
                $new->transacrion_id = $request->transaction_id;
                $new->date           = Date('Y-m-d');
                $new->amount         = $amount;
                $new->client_id      = Auth::check()?Auth::user()->id:' public';
                $new->description    = '';
                $new->payment_method = 'Sspay';
                $new->save();
                if ($invoice->dueAmount() == 0) {
                    $invoice->status = 'Paid';
                } else {
                    $invoice->status = 'Partialy Paid';
                }
                $invoice->save();

                if (Auth::check()) {
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                } else
                {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }

            }
        }
        catch (\Exception $e)
        {
            if (Auth::check())
            {
                return redirect()->route('invoices.show', $invoice_id)->with('error',$e->getMessage());
            } else
            {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success',$e->getMessage());
            }
        }
    }

}
