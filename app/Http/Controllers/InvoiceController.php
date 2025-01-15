<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Opportunities;
use App\Exports\InvoiceExport;
use App\Models\Product;
use App\Models\ProductTax;
use App\Models\Quote;
use App\Models\SalesOrder;
use App\Models\ShippingProvider;
use App\Models\Stream;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Mail\Invoicemail;
use App\Models\BankTransfer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\InvoicePayment;
use App\Models\Payment;
use App\Models\Plan;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage Invoice')) {
            if (\Auth::user()->type == 'owner') {
                $invoices = Invoice::with('accounts', 'assign_user')->where('created_by', \Auth::user()->creatorId())->get();
            } else {
                $invoices = Invoice::with('accounts', 'assign_user')->where('user_id', \Auth::user()->id)->get();
            }
            return view('invoice.index', compact('invoices'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type, $id)
    {
        if (\Auth::user()->can('Create Invoice')) {
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account->prepend('--', '');
            $opportunities = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunities->prepend('--', '');
            $salesorder = SalesOrder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $salesorder->prepend('--', 0);
            $quote = Quote::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $quote->prepend('--', 0);
            $status  = Invoice::$status;
            $contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $contact->prepend('--', 0);
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            return view('invoice.create', compact('user', 'salesorder', 'quote', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider', 'type', 'id'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Invoice')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                    'account' => 'required',
                    'description' => 'required',

                    //    'tax' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            // if(count($request->tax) > 1 && in_array(0, $request->tax))
            // {
            //     return redirect()->back()->with('error', 'Please select valid tax');
            // }
            $invoice                        = new Invoice();
            $invoice['user_id']             = $request->user;
            $invoice['invoice_id']          = $this->invoiceNumber();
            $invoice['name']                = $request->name;
            $invoice['salesorder']          = $request->salesorder;
            $invoice['quote']               = $request->quote;
            $invoice['opportunity']         = $request->opportunity;
            $invoice['status']              = 0;
            $invoice['account']             = $request->account;
            $invoice['date_quoted']         = $request->date_quoted;
            $invoice['quote_number']        = $request->quote_number;
            $invoice['billing_address']     = $request->billing_address;
            $invoice['billing_city']        = $request->billing_city;
            $invoice['billing_state']       = $request->billing_state;
            $invoice['billing_country']     = $request->billing_country;
            $invoice['billing_postalcode']  = $request->billing_postalcode;
            $invoice['shipping_address']    = $request->shipping_address;
            $invoice['shipping_city']       = $request->shipping_city;
            $invoice['shipping_state']      = $request->shipping_state;
            $invoice['shipping_country']    = $request->shipping_country;
            $invoice['shipping_postalcode'] = $request->shipping_postalcode;
            $invoice['billing_contact']     = $request->billing_contact;
            $invoice['shipping_contact']    = $request->shipping_contact;
            // $invoice['tax']                 = implode(',', $request->tax);
            $invoice['shipping_provider']   = $request->shipping_provider;
            $invoice['description']         = $request->description;
            $invoice['created_by']          = \Auth::user()->creatorId();
            $invoice->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );
            $Assign_user_phone = User::where('id', $request->user)->first();

            $uArr = [
                'invoice_id' => $this->invoiceNumber(),
                // 'invoice_client' => $Assign_user_phone->name,
                'date_quoted' => $request->date_quoted,
                'description' => $request->description,
                'invoice_status' => 0,
                'invoice_sub_total' =>  \Auth::user()->priceFormat($invoice->getTotal()),
                'created_at' => $request->created_at,

            ];
            $resp = Utility::sendEmailTemplate('new_invoice', [$invoice->id => $Assign_user_phone->email], $uArr);
            //webhook
            $module = 'New Invoice';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($invoice);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status != true) {
                    $msg = "Webhook call failed.";
                }
            }
            if (\Auth::user()) {
                return redirect()->back()->with('success', __('Invoice successfully created!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }
            $setting  = Utility::settings(\Auth::user()->creatorId());

            if (isset($setting['twilio_invoice_create']) && $setting['twilio_invoice_create'] == 1) {
                // $msg = "New invoice" . " " . \Auth::user()->invoiceNumberFormat($this->invoiceNumber()) . " created by " . \Auth::user()->name . '.';
                $uArr = [
                    'invoice_id' => $this->invoiceNumber(),
                    'invoice_sub_total' =>  \Auth::user()->priceFormat($invoice->getTotal()),
                    'user_name' => \Auth::user()->name,

                ];
                Utility::send_twilio_msg($Assign_user_phone->phone, 'new_invoice', $uArr);
            }

            return redirect()->back()->with('success', __('Invoice Successfully Created.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        if (\Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = Invoice::where('id', $invoice->created_by)->first();
        }
        if (\Auth::user()->can('Show Invoice')) {
            $settings = Utility::settings();

            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach ($invoice->itemsdata as $item) {

                $totalQuantity += $item->quantity;
                $totalRate     += $item->price;
                $totalDiscount += $item->discount;
                $taxes         = Utility::tax($item->tax);

                $itemTaxes = [];
                foreach ($taxes as $tax) {
                    if (!empty($tax)) {
                        $taxPrice            = Utility::taxRate($tax->rate, $item->price, $item->quantity);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = $tax->tax_name;
                        $itemTax['tax']      = $tax->tax . '%';
                        $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                        $itemTaxes[]         = $itemTax;
                        if (array_key_exists($tax->name, $taxesData)) {
                            $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
                        } else {
                            $taxesData[$tax->tax_name] = $taxPrice;
                        }
                    } else {
                        $taxPrice            = Utility::taxRate(0, $item->price, $item->quantity);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = 'No Tax';
                        $itemTax['tax']      = '';
                        $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                        $itemTaxes[]         = $itemTax;

                        if (array_key_exists('No Tax', $taxesData)) {
                            $taxesData[$itemTax['tax_name']] = $taxesData['No Tax'] + $taxPrice;
                        } else {
                            $taxesData['No Tax'] = $taxPrice;
                        }
                    }
                }

                $item->itemTax = $itemTaxes;
                $items[]       = $item;
            }
            $invoice->items         = $items;
            $invoice->totalTaxPrice = $totalTaxPrice;
            $invoice->totalQuantity = $totalQuantity;
            $invoice->totalRate     = $totalRate;
            $invoice->totalDiscount = $totalDiscount;
            $invoice->taxesData     = $taxesData;

            $company_setting = Utility::settings();
            $invoicepayments = BankTransfer::where('created_by', $user->id)->get();
            $plan = Plan::find($user->plan);
            return view('invoice.view', compact('invoice', 'company_setting', 'invoicepayments', 'plan', 'user'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        if (\Auth::user()->can('Edit Invoice')) {

            $opportunity = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunity->prepend('--', '');
            $salesorder = SalesOrder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $salesorder->prepend('--', 0);
            $quote = Quote::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $quote->prepend('--', 0);
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account->prepend('--', '');
            $status          = Invoice::$status;
            $billing_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $billing_contact->prepend('--', 0);
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            // get previous user id
            $previous = Invoice::where('id', '<', $invoice->id)->max('id');
            // get next user id
            $next = Invoice::where('id', '>', $invoice->id)->min('id');


            return view('invoice.edit', compact('invoice', 'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user', 'salesorder', 'quote', 'previous', 'next'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        if (\Auth::user()->can('Edit Invoice')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                    // 'tax' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // if (count($request->tax) > 1 && in_array(0, $request->tax)) {
            //     return redirect()->back()->with('error', 'Please select valid tax');
            // }

            $invoice['user_id']             = $request->user;
            $invoice['invoice_id']          = $this->invoiceNumber();
            $invoice['name']                = $request->name;
            $invoice['salesorder']          = $request->salesorder;
            $invoice['quote']               = $request->quote;
            $invoice['opportunity']         = $request->opportunity;
            $invoice['account']             = $request->account;
            $invoice['date_quoted']         = $request->date_quoted;
            $invoice['quote_number']        = $request->quote_number;
            $invoice['billing_address']     = $request->billing_address;
            $invoice['billing_city']        = $request->billing_city;
            $invoice['billing_state']       = $request->billing_state;
            $invoice['billing_country']     = $request->billing_country;
            $invoice['billing_postalcode']  = $request->billing_postalcode;
            $invoice['shipping_address']    = $request->shipping_address;
            $invoice['shipping_city']       = $request->shipping_city;
            $invoice['shipping_state']      = $request->shipping_state;
            $invoice['shipping_country']    = $request->shipping_country;
            $invoice['shipping_postalcode'] = $request->shipping_postalcode;
            $invoice['billing_contact']     = $request->billing_contact;
            $invoice['shipping_contact']    = $request->shipping_contact;
            // $invoice['tax']                 = implode(',', $request->tax);
            $invoice['shipping_provider']   = $request->shipping_provider;
            $invoice['description']         = $request->description;
            $invoice['created_by']          = \Auth::user()->creatorId();
            $invoice->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Invoice Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if (\Auth::user()->can('Delete Invoice')) {
            $invoice->delete();

            return redirect()->back()->with('success', __('Invoice Successfully delete.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function getaccount(Request $request)
    {
        if ($request->opportunities_id) {
            $opportunitie = Opportunities::where('id', $request->opportunities_id)->first()->toArray();
            $account      = Account::find($opportunitie['account'])->toArray();

            return response()->json(
                [
                    'opportunitie' => $opportunitie,
                    'account' => $account,
                ]
            );
        }
    }

    public function invoiceitem($id)
    {
        $invoice = Invoice::find($id);

        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('invoice.invoiceitem', compact('items', 'invoice', 'tax_rate'));
    }

    public function invoiceItemEdit($id)
    {
        $invoiceItem = InvoiceItem::find($id);

        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('invoice.invoiceitemEdit', compact('items', 'invoiceItem', 'tax_rate'));
    }

    public function items(Request $request)
    {
        $items        = Product::where('id', $request->item_id)->first();
        $items->taxes = $items->tax($items->tax);

        return json_encode($items);
    }

    public function itemsDestroy($id)
    {
        $item = InvoiceItem::find($id);
        $invoice = Invoice::find($item->invoice_id);
        $item->delete();
        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
        if ($invoice_getdue <= 0.0) {
            Invoice::change_status($invoice->id, 3);
        }

        return redirect()->back()->with('success', __('Item Successfully delete.'));
    }

    public function storeitem(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'description' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $invoice = Invoice::find($id);
        if ($invoice->getTotal() == 0.0) {
            Invoice::change_status($invoice->id, 1);
        }
        $invoiceitem                = new InvoiceItem();
        $invoiceitem['invoice_id']  = $request->id;
        $invoiceitem['item']        = $request->item;
        $invoiceitem['quantity']    = $request->quantity;
        $invoiceitem['price']       = $request->price;
        $invoiceitem['discount']    = $request->discount;
        $invoiceitem['tax']         = $request->tax;
        $invoiceitem['description'] = $request->description;
        $invoiceitem['created_by']  = \Auth::user()->creatorId();
        $invoiceitem->save();
        $invoice = Invoice::find($id);
        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
        if ($invoice_getdue > 0.0 || $invoice_getdue < 0.0) {
            Invoice::change_status($invoice->id, 2);
        }
        //webhook
        $module = 'New Invoice';
        $webhook =  Utility::webhookSetting($module);
        if ($webhook) {
            $parameter = json_encode($invoice);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
            if ($status != true) {
                $msg = "Webhook call failed.";
            }
        }
        if (\Auth::user()) {
            return redirect()->back()->with('success', __('Invoice Item successfully created!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
        } else {
            return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
        }
        return redirect()->back()->with('success', __('Invoice Item Successfully Created.'));
    }

    public function invoiceItemUpdate(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            []
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $invoiceitem                = InvoiceItem::find($id);
        $invoiceitem['item']        = $request->item;
        $invoiceitem['quantity']    = $request->quantity;
        $invoiceitem['price']       = $request->price;
        $invoiceitem['discount']    = $request->discount;
        $invoiceitem['tax']         = $request->tax;
        $invoiceitem['description'] = $request->description;
        $invoiceitem->save();

        return redirect()->back()->with('success', __('Invoice Item Successfully Updated.'));
    }

    public function previewInvoice($template, $color)
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();
        //    invoice qr
        if (isset($settings['is_qr_enabled']) && $settings['is_qr_enabled'] == 'on') {
            $qr_invoice = 'on';
        } else {
            $qr_invoice = 'off';
        }

        $invoice  = new Invoice();

        $user               = new \stdClass();
        $user->company_name = '<Company Name>';
        $user->name         = '<Name>';
        $user->email        = '<Email>';
        $user->mobile       = '<Phone>';
        $user->address      = '<Address>';
        $user->country      = '<Country>';
        $user->state        = '<State>';
        $user->city         = '<City>';


        $totalTaxPrice = 0;
        $taxesData     = [];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->items      = $items;

        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate     = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData     = $taxesData;


        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        // $logo         = asset(Storage::url('logo/'));
        $logo = \App\Models\Utility::get_file('uploads/logo/');
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        $dark_logo    = Utility::getValByName('dark_logo');
        $invoice_logo = Utility::getValByName('invoice_logo');
        if (isset($invoice_logo) && !empty($invoice_logo)) {
            $img = \App\Models\Utility::get_file('/') . $invoice_logo;
        } else {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }

        return view('invoice.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }

    public function saveInvoiceTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);

            //invoice qr
            if (isset($post['invoice_template']) && isset($request->is_qr_enabled) && $request->is_qr_enabled == 'on') {
                $post['is_qr_enabled'] = 'on';
            } else {
                $post['is_qr_enabled'] = 'off';
            }

        if (isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color']))) {
            $post['invoice_color'] = "ffffff";
        }
        if ($request->invoice_logo) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'invoice_logo' => 'image',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice_logo         = $user->id . '_invoice_logo.png';
            $dir = 'invoice_logo/';

            $validation = [
                'mimes:' . 'png',
                'max:' . '20480',
            ];


            $path = Utility::upload_file($request, 'invoice_logo', $invoice_logo, $dir, $validation);
            if ($path['flag'] == 1) {
                $invoice_logo = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            // $path                 = $request->file('invoice_logo')->storeAs('invoice_logo', $invoice_logo);
            $post['invoice_logo'] = $invoice_logo;
        }
        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->creatorId(),
                ]
            );
        }

        return redirect()->back()->with('success', __('Invoice Setting successfully updated.'));
    }

    public function pdf($id)
    {
        $settings = Utility::settings();

        $invoiceId = Crypt::decrypt($id);
        $invoice   = Invoice::where('id', $invoiceId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('created_by', '=', $invoice->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $user         = new User();
        $user->name   = $invoice->name;
        $user->email  = $invoice->contacts->email ?? '';
        $user->mobile = $invoice->contacts->phone ?? '';

        $user->bill_address = $invoice->billing_address;
        $user->bill_zip     = $invoice->billing_postalcode;
        $user->bill_city    = $invoice->billing_city;
        $user->bill_country = $invoice->billing_country;
        $user->bill_state   = $invoice->billing_state;

        $user->address = $invoice->shipping_address;
        $user->zip     = $invoice->shipping_postalcode;
        $user->city    = $invoice->shipping_city;
        $user->country = $invoice->shipping_country;
        $user->state   = $invoice->shipping_state;


        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach ($invoice->itemsdata as $product) {
            $item           = new \stdClass();
            $item->name     = $product->item;
            $item->quantity = $product->quantity;
            $item->tax      = !empty($product->taxs) ? $product->taxs->rate : '';
            $item->discount = $product->discount;
            $item->price    = $product->price;


            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = \Utility::tax($product->tax);
            $itemTaxes = [];
            foreach ($taxes as $tax) {
                $taxPrice      = Utility::taxRate($tax->rate, $item->price, $item->quantity);
                $totalTaxPrice += $taxPrice;

                $itemTax['name']  = $tax->tax_name;
                $itemTax['rate']  = $tax->rate . '%';
                $itemTax['price'] = \Utility::priceFormat($settings, $taxPrice);
                $itemTaxes[]      = $itemTax;


                if (array_key_exists($tax->tax_name, $taxesData)) {
                    $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
                } else {
                    $taxesData[$tax->tax_name] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }
        $invoice->issue_date = $invoice->date_quoted;
        $invoice->items         = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate     = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData     = $taxesData;

        //Set your logo
        // $logo         = asset(Storage::url('logo/'));
        $logo = \App\Models\Utility::get_file('uploads/logo/');
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        $dark_logo    = Utility::getValByName('dark_logo');
        $invoice_logo = Utility::getValByName('invoice_logo');
        if (isset($invoice_logo) && !empty($invoice_logo)) {
            $img = \App\Models\Utility::get_file('/') . $invoice_logo;
        } else {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }

        if ($invoice) {
            $color      = '#' . $settings['invoice_color'];
            $font_color = Utility::getFontColor($color);

            return view('invoice.templates.' . $settings['invoice_template'], compact('invoice', 'user', 'color', 'settings', 'img', 'font_color'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function duplicate($id)
    {
        if (\Auth::user()->can('Create Invoice')) {
            $invoice                          = Invoice::find($id);
            $duplicate                        = new Invoice();
            $duplicate['user_id']             = $invoice->user_id;
            $duplicate['invoice_id']          = $this->invoiceNumber();
            $duplicate['name']                = $invoice->name;
            $duplicate['salesorder']          = $invoice->salesorder;
            $duplicate['quote']               = $invoice->quote;
            $duplicate['opportunity']         = $invoice->opportunity;
            $duplicate['status']              = 0;
            $duplicate['account']             = $invoice->account;
            $duplicate['amount']              = $invoice->amount;
            $duplicate['date_quoted']         = $invoice->date_quoted;
            $duplicate['quote_number']        = $invoice->quote_number;
            $duplicate['billing_address']     = $invoice->billing_address;
            $duplicate['billing_city']        = $invoice->billing_city;
            $duplicate['billing_state']       = $invoice->billing_state;
            $duplicate['billing_country']     = $invoice->billing_country;
            $duplicate['billing_postalcode']  = $invoice->billing_postalcode;
            $duplicate['shipping_address']    = $invoice->shipping_address;
            $duplicate['shipping_city']       = $invoice->shipping_city;
            $duplicate['shipping_state']      = $invoice->shipping_state;
            $duplicate['shipping_country']    = $invoice->shipping_country;
            $duplicate['shipping_postalcode'] = $invoice->shipping_postalcode;
            $duplicate['billing_contact']     = $invoice->billing_contact;
            $duplicate['shipping_contact']    = $invoice->shipping_contact;
            $duplicate['tax']                 = $invoice->tax;
            $duplicate['shipping_provider']   = $invoice->shipping_provider;
            $duplicate['description']         = $invoice->description;
            $duplicate['created_by']          = \Auth::user()->creatorId();
            $duplicate->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );

            if ($duplicate) {
                $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)->get();

                foreach ($invoiceItem as $item) {

                    $invoiceitem                = new InvoiceItem();
                    $invoiceitem['invoice_id']  = $duplicate->id;
                    $invoiceitem['item']        = $item->item;
                    $invoiceitem['quantity']    = $item->quantity;
                    $invoiceitem['price']       = $item->price;
                    $invoiceitem['discount']    = $item->discount;
                    $invoiceitem['tax']         = $item->tax;
                    $invoiceitem['description'] = $item->description;
                    $invoiceitem['created_by']  = \Auth::user()->creatorId();
                    $invoiceitem->save();
                }
            }

            return redirect()->back()->with('success', __('Invoice duplicate successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoicelink($invoice_id)
    {

        return view('invoice.invoicelink', compact('invoice_id'));
    }

    public function sendmail(Request $request, $id)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
                'email' => 'required|email'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $invoice = Invoice::where('id', $id)->first();
        if (!is_null($invoice)) {
            $settings = $settings = Utility::settings();
            $invoice->invoice = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
            $invoice->url = route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id));
            $invoice->reciverName = $request->name;

            try {
                Mail::to($request->email)->send(new Invoicemail($invoice, $settings));
            } catch (\Exception $e) {

                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Email send Successfully.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function payinvoice($invoice_id)
    {
        if (!empty($invoice_id)) {
            try {
                $id  = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
            } catch (\RuntimeException $e) {
                return redirect()->back()->with('error', __('Invoice not avaliable'));
            }
            // dd($id);
            $invoice = Invoice::where('id', $id)->first();

            if (\Auth::check()) {
                $usr = \Auth::user();
            } else {
                $usr = User::where('id', $invoice->created_by)->get();
            }

            if (!is_null($invoice)) {

                $settings = Utility::settings();

                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach ($invoice->itemsdata as $item) {
                    $totalQuantity += $item->quantity;
                    $totalRate     += $item->price;
                    $totalDiscount += $item->discount;
                    $taxes         = Utility::tax($item->tax);

                    $itemTaxes = [];

                    foreach ($taxes as $tax) {
                        if (!empty($tax)) {

                            $taxPrice            = Utility::taxRate($tax->rate, $item->price, $item->quantity);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = $tax->tax_name;
                            $itemTax['tax']      = $tax->tax . '%';
                            $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                            $itemTaxes[]         = $itemTax;

                            if (array_key_exists($tax->name, $taxesData)) {
                                $taxesData[$itemTax['tax_name']] = $taxesData[$tax->tax_name] + $taxPrice;
                            } else {
                                $taxesData[$tax->tax_name] = $taxPrice;
                            }
                        } else {
                            $taxPrice            = Utility::taxRate(0, $item->price, $item->quantity);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = 'No Tax';
                            $itemTax['tax']      = '';
                            $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                            $itemTaxes[]         = $itemTax;

                            if (array_key_exists('No Tax', $taxesData)) {

                                $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                            } else {
                                $taxesData['No Tax'] = $taxPrice;
                            }
                        }
                    }
                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $invoice->items         = $items;
                $invoice->totalTaxPrice = $totalTaxPrice;
                $invoice->totalQuantity = $totalQuantity;
                $invoice->totalRate     = $totalRate;
                $invoice->totalDiscount = $totalDiscount;
                $invoice->taxesData     = $taxesData;

                $company_setting = Utility::settings();

                $ownerId = Utility::ownerIdforInvoice($invoice->created_by);

                $payment_setting = Utility::invoice_payment_settings($ownerId);
                $site_setting = Utility::settingsById($ownerId);
                $users = User::where('id', $invoice->created_by)->first();
                $plan = Plan::find($users->plan);
                if (!is_null($users)) {
                    \App::setLocale($users->lang);
                } else {
                    $users = User::where('type', 'owner')->first();
                    \App::setLocale($users->lang);
                }

                $invoicePayment = InvoicePayment::where("invoice_id", $invoice->id)->first();
                if (\Auth::check()) {
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                } else {
                    $setting  = Utility::settings($users->id);
                }

                if (isset($setting['twilio_invoicepay_create']) && $setting['twilio_invoicepay_create'] == 1) {
                    $uArr = [
                        'user' => $users,
                        'user_name' => \Auth::user()->name,
                    ];
                    // if ($invoicePayment) {
                    //     $msg = "New payment of " . $invoicePayment->amount . " created for " . $users->name . " by " . $invoicePayment->payment_type . '.';
                    // } else {
                    //     $msg = "New payment created by " . $users->name . '.';
                    // }

                    if (\Auth::check()) {

                        Utility::send_twilio_msg($users->phone, 'new_invoice_payment', $uArr);
                    } else {

                        Utility::send_twilio_msg_withoutauth($users->phone, $uArr, $users);
                    }
                }
                $bankPayments  = BankTransfer::where('invoice_id', $invoice->id)->get();

                return view('invoice.invoicepay', compact('invoice', 'company_setting', 'users', 'payment_setting', 'site_setting', 'bankPayments', 'plan'));
            } else {
                return abort('404', 'The Link You Followed Has Expired');
            }
        } else {
            return abort('404', 'The Link You Followed Has Expired');
        }
    }

    public function payments()
    {
        if (Auth::user()->can('Manage Invoice Payment')) {
            if (\Auth::user()->type == 'owner') {

                $payments = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=', Auth::user()->creatorId())->get();
            } else {
                $payments = InvoicePayment::select(['invoice_payments.*'])->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=', Auth::user()->id)->get();
            }

            return view('invoice.all-payments', compact('payments'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function paymentAdd($id)
    {
        if (\Auth::user()->can('Create Invoice Payment')) {
            $invoice = Invoice::find($id);
            if ($invoice->created_by == Auth::user()->creatorId()) {
                $payment_methods = Payment::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('invoice.payment', compact('invoice', 'payment_methods'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function paymentStore($id, Request $request)
    {
        if (\Auth::user()->can('Create Invoice Payment')) {
            $invoice = Invoice::find($id);
            if ($invoice->created_by == Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|numeric|min:1',
                        'date' => 'required',
                        'payment_id' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('invoices.show', $invoice->id)->with('error', $messages->first());
                }
                InvoicePayment::create(
                    [
                        'transaction_id' => $this->transactionNumber($invoice->created_by),
                        'invoice_id' => $invoice->id,
                        'amount' => $request->amount,
                        'date' => $request->date,
                        'payment_id' => $request->payment_id,
                        'notes' => $request->notes,
                        'payment_type' => __('MANUAL')
                    ]
                );
                $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
                if ($invoice_getdue == 0.0) {
                    Invoice::change_status($invoice->id, 3);
                } else {
                    Invoice::change_status($invoice->id, 2);
                }

                return redirect()->route('invoice.show', $invoice->id)->with('success', __('Payment successfully added.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function pdffrominvoice($id)
    {
        $settings = Utility::settings();

        $invoiceId = Crypt::decrypt($id);
        $invoice   = Invoice::where('id', $invoiceId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('created_by', '=', $invoice->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $user         = new User();
        $user->name   = $invoice->name;
        $user->email  = $invoice->contacts->email;
        $user->mobile = $invoice->contacts->phone;

        $user->bill_address = $invoice->billing_address;
        $user->bill_zip     = $invoice->billing_postalcode;
        $user->bill_city    = $invoice->billing_city;
        $user->bill_country = $invoice->billing_country;
        $user->bill_state   = $invoice->billing_state;

        $user->address = $invoice->shipping_address;
        $user->zip     = $invoice->shipping_postalcode;
        $user->city    = $invoice->shipping_city;
        $user->country = $invoice->shipping_country;
        $user->state   = $invoice->shipping_state;


        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach ($invoice->itemsdata as $product) {
            $item           = new \stdClass();
            $item->name     = $product->item;
            $item->quantity = $product->quantity;
            $item->tax      = !empty($product->taxs) ? $product->taxs->rate : '';
            $item->discount = $product->discount;
            $item->price    = $product->price;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = \Utility::tax($product->tax);
            $itemTaxes = [];
            foreach ($taxes as $tax) {
                $taxPrice      = \Utility::taxRate($tax->rate, $item->price, $item->quantity);
                $totalTaxPrice += $taxPrice;

                $itemTax['name']  = $tax->tax_name;
                $itemTax['rate']  = $tax->rate . '%';
                $itemTax['price'] = \App\Models\Utility::priceFormat($settings, $taxPrice);
                $itemTaxes[]      = $itemTax;


                if (array_key_exists($tax->tax_name, $taxesData)) {
                    $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
                } else {
                    $taxesData[$tax->tax_name] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $invoice->items         = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate     = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData     = $taxesData;

        //Set your logo
        $logo = \App\Models\Utility::get_file('logo/');
        $company_logo = Utility::getValByName('company_logo_dark');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if ($invoice) {
            $color      = '#' . $settings['invoice_color'];
            $font_color = Utility::getFontColor($color);

            return view('invoice.templates.' . $settings['invoice_template'], compact('invoice', 'user', 'color', 'settings', 'img', 'font_color'));
        } else {
            return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoiceId))->with('error', __('Permission denied.'));
        }
    }

    public function transactionNumber($id)
    {
        $latest = InvoicePayment::select('invoice_payments.*')->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=', $id)->latest()->first();
        if ($latest) {
            return $latest->transaction_id + 1;
        }

        return 1;
    }

    public function fileExport()
    {

        $name = 'invoice_' . date('Y-m-d i:h:s');
        $data = Excel::download(new InvoiceExport(), $name . '.xlsx');
        ob_end_clean();


        return $data;
    }
    public function paymentshow($id)
    {
        $payment_show = InvoicePayment::find($id);
        $admin_payment_setting = Utility::payment_settings();

        return view('invoice.show', compact('payment_show', 'admin_payment_setting'));
    }
    public function invoicePaymentDestroy($id)
    {
        $payment = InvoicePayment::find($id);
        $payment->delete();
        return redirect()->back()->with('success', __('Payment Successfully delete.'));
    }
}
