<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\InvoicePayment;
use Google\Service\Spanner\Transaction;

class PayfastController extends Controller
{
    public $invoiceData;
    public $currancy;
    public function index(Request $request)
    {
        if (Auth::check()) {
            $payment_setting = Utility::payment_settings();

            $planID = Crypt::decrypt($request->plan_id);
            $plan = Plan::find($planID);
            if ($plan) {

                $plan_amount = $plan->price;
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                $user = Auth::user();
                if ($request->coupon_amount > 0) {
                    $coupons = Coupon::where('code', $request->coupon_code)->first();
                    // $discount_value         = ($plan->price / 100) * $coupons->discount;
                    // $discounted_price = $plan->price - $discount_value;

                    if (!empty($coupons)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $order_id;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }

                        // $plan_amount = $plan_amount - $discounted_price;

                    }
                    $plan_amount = $plan_amount - $request->coupon_amount;
                }
                $success = Crypt::encrypt([
                    'plan' => $plan->toArray(),
                    'order_id' => $order_id,
                    'plan_amount' => $plan_amount
                ]);

                $data = array(
                    // Merchant details
                    'merchant_id' => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
                    'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
                    'return_url' => route('payfast.payment.success', $success),
                    'cancel_url' => route('plan.index'),
                    'notify_url' => route('plan.index'),
                    // Buyer details
                    'name_first' => $user->name,
                    'name_last' => '',
                    'email_address' => $user->email,
                    // Transaction details
                    'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
                    'amount' => number_format(sprintf('%.2f', $plan_amount), 2, '.', ''),
                    'item_name' => $plan->name,
                );

                $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
                $signature = $this->generateSignature($data, $passphrase);
                $data['signature'] = $signature;

                $htmlForm = '';

                foreach ($data as $name => $value) {
                    $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
                }

                return response()->json([
                    'success' => true,
                    'inputs' => $htmlForm,
                ]);
            }
        }
    }
    public function generateSignature($data, $passPhrase = null)
    {
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    public function success($success)
    {
        try {
            $user = Auth::user();
            $data = Crypt::decrypt($success);

            $order = new Order();
            $order->order_id = $data['order_id'];
            $order->name = $user->name;
            $order->card_number = '';
            $order->card_exp_month = '';
            $order->card_exp_year = '';
            $order->plan_name = $data['plan']['name'];
            $order->plan_id = $data['plan']['id'];
            $order->price = $data['plan_amount'];
            $order->price_currency = !empty($this->currancy) ? $this->currancy : 'usd';
            $order->txn_id = $data['order_id'];
            $order->payment_type = __('PayFast');
            $order->payment_status = 'succeeded';
            $order->txn_id = '';
            $order->receipt = '';
            $order->user_id = $user->id;
            $order->save();
            if(!empty($user->referral_user)){
                Utility::transaction($order);
            }
            $assignPlan = $user->assignPlan($data['plan']['id']);

            if ($assignPlan['is_success']) {
                return redirect()->route('plan.index')->with('success', __('Plan activated Successfully.'));
            } else {
                return redirect()->route('plan.index')->with('error', __($assignPlan['error']));
            }
        } catch (Exception $e) {
            return redirect()->route('plan.index')->with('error', __($e));
        }
    }

    public function invoicepaywithpayfast(Request $request)
    {
        $invoice_id = Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $user = User::where('id', $invoice->created_by)->first();

        $this->invoiceData  = $invoice;
        $settings = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');
        $setting = \App\Models\Utility::settings();

        $get_amount = $request->amount;
        $payment_setting = Utility::invoice_payment_settings($user->id);
        if ($invoice) {
            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $invoice_success = Crypt::encrypt([
                'invoice_amount' => $get_amount,
                'invoice_id' => $invoice_id
            ]);
            $data = array(
                // Merchant details
                'merchant_id'  => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
                'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
                'return_url' => route('invoice.payfast.status', $invoice_success),
                'cancel_url' => route('pay.invoice', $invoice->id),
                'notify_url' => route('pay.invoice', $invoice->id),
                // Buyer details
                'name_first' => $user->name,
                'name_last' => '',
                'email_address' => $user->email,
                // Transaction details
                'm_payment_id' => $orderID, //Unique payment ID to pass through to notify_url
                'amount' => number_format(sprintf('%.2f', $get_amount), 2, '.', ''),
                'item_name' => 'Invoice',
            );
            $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
            $signature = $this->generateSignature($data, $passphrase);
            $data['signature'] = $signature;
            $htmlForm = '';
            foreach ($data as $name => $value) {
                $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';

            }
            return response()->json([
                'success' => true,
                'inputs' => $htmlForm,
            ]);
        }
    }

    public function invoicepayfaststatus($invoice_success)
    {

        $invoice_id = Crypt::decrypt($invoice_success);

        $invoice = Invoice::find($invoice_id['invoice_id']);

        $user = User::where('id', $invoice->created_by)->first();
        $objUser = \Auth::user();
        $objUser = $user;
        $get_amount = $invoice_id['invoice_amount'];
        // $get_amount = ;
        if ($invoice) {
            try {
                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->invoice_id     = $invoice_id['invoice_id'];
                $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber($user->id);
                $invoice_payment->client_id      = $user->id;
                $invoice_payment->amount         = $get_amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->payment_id     = 0;
                $invoice_payment->notes          = "";
                $invoice_payment->payment_type   = 'Payfast';
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
                    return redirect()->route('invoice.show', $invoice_id['invoice_id'])->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                } else {
                    $id = \Crypt::encrypt($invoice_id['invoice_id']);
                    return redirect()->route('pay.invoice', $id)->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
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
                    return redirect()->route('pay.invoice', encrypt($invoice_id['invoice_id']))->with('success', $e->getMessage());
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
