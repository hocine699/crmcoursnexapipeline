<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Opportunities;
use App\Models\Product;
use App\Models\ProductTax;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Exports\QuoteExport;
use App\Models\Plan;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\ShippingProvider;
use App\Models\Stream;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Google\Service\ServiceControl\Auth;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage User')) {
            if (\Auth::user()->type == 'owner') {
                $quotes = Quote::with('assign_user', 'accounts')->where('created_by', \Auth::user()->creatorId())->get();
            } else {
                $quotes = Quote::with('assign_user', 'accounts')->where('user_id', \Auth::user()->id)->get();
            }
            return view('quote.index', compact('quotes'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('Create Quote')) {
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account->prepend('--', 0);
            $opportunities = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunities->prepend('--', '');
            $status  = Quote::$status;
            $contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $contact->prepend('--', 0);
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');


            return view('quote.create', compact('user', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider'));
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
        if (\Auth::user()->can('Create Quote')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                    // 'account'=> 'required',
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

            $quote                        = new Quote();
            $quote['user_id']             = $request->user;
            $quote['quote_id']            = $this->quoteNumber();
            $quote['name']                = $request->name;
            $quote['opportunity']         = $request->opportunity;
            // $quote['status']              = 0;
            $quote['status']              = $request->status;
            $quote['account']             = $request->account_id;
            $quote['date_quoted']         = $request->date_quoted;
            $quote['quote_number']        = $request->quote_number;
            $quote['billing_address']     = $request->billing_address;
            $quote['billing_city']        = $request->billing_city;
            $quote['billing_state']       = $request->billing_state;
            $quote['billing_country']     = $request->billing_country;
            $quote['billing_postalcode']  = $request->billing_postalcode;
            $quote['shipping_address']    = $request->shipping_address;
            $quote['shipping_city']       = $request->shipping_city;
            $quote['shipping_state']      = $request->shipping_state;
            $quote['shipping_country']    = $request->shipping_country;
            $quote['shipping_postalcode'] = $request->shipping_postalcode;
            $quote['billing_contact']     = $request->billing_contact;
            $quote['shipping_contact']    = $request->shipping_contact;
            // $quote['tax']                 = implode(',', $request->tax);
            $quote['shipping_provider']   = $request->shipping_provider;
            $quote['description']         = $request->description;
            $quote['created_by']          = \Auth::user()->creatorId();

            $quote->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'quote',
                            'stream_comment' => '',
                            'user_name' => $quote->name,
                        ]
                    ),
                ]
            );
            $Assign_user_phone = User::where('id', $request->user)->first();
            $setting  = Utility::settings(\Auth::user()->creatorId());

            $uArr = [
                'quote_number' => $quote->quote_number,
                'billing_address' => $quote->billing_address,
                'shipping_address' => $quote->shipping_address,
                'description' => $quote->description,
                'date_quoted' => $quote->date_quoted,
                // 'quote_assign_user' => $Assign_user_phone->name,
            ];

            $resp = Utility::sendEmailTemplate('quote_created', [$quote->id => $Assign_user_phone->email], $uArr);


            if (isset($setting['twilio_quotes_create']) && $setting['twilio_quotes_create'] == 1) {
                $uArr = [
                    'quote_number' => $request->quote_number,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                    'date_quoted' => $request->date_quoted,
                    'user_name' => \Auth::user()->name,
                ];

                Utility::send_twilio_msg($Assign_user_phone->phone, 'new_quotes', $uArr);
            }
            //webhook
            $module = 'New Quotes';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($quote);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status != true) {
                    $msg = "Webhook call failed.";
                }
            }
            if (\Auth::user()) {
                return redirect()->back()->with('success', __('Quote successfully created!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }

            return redirect()->back()->with('success', __('Quote Successfully Created.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Quote $quote
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Quote $quote)
    {
        if (\Auth::user()->can('Show Quote')) {
            $settings = Utility::settings();

            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach ($quote->itemsdata as $item) {
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
            $quote->items         = $items;
            $quote->totalTaxPrice = $totalTaxPrice;
            $quote->totalQuantity = $totalQuantity;
            $quote->totalRate     = $totalRate;
            $quote->totalDiscount = $totalDiscount;
            $quote->taxesData     = $taxesData;

            $company_setting = Utility::settings();

            return view('quote.view', compact('quote', 'company_setting'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Quote $quote
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Quote $quote)
    {
        if (\Auth::user()->can('Edit Quote')) {
            $opportunity = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunity->prepend('--', '');
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account->prepend('--', 0);
            $billing_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $billing_contact->prepend('--', 0);
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);

            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $salesorders = SalesOrder::where('quote', $quote->id)->get();
            $invoices    = Invoice::where('quote', $quote->id)->get();
            $status      = Quote::$status;
            // get previous user id
            $previous = Quote::where('id', '<', $quote->id)->max('id');
            // get next user id
            $next = Quote::where('id', '>', $quote->id)->min('id');


            return view('quote.edit', compact('quote', 'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user', 'salesorders', 'invoices', 'previous', 'next'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Quote $quote
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quote $quote)
    {
        if (\Auth::user()->can('Edit Quote')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // if (count($request->tax) > 1 && in_array(0, $request->tax)) {
            //     return redirect()->back()->with('error', 'Please select valid tax');
            // }

            $quote['user_id']             = $request->user;
            $quote['quote_id']            = $this->quoteNumber();
            $quote['name']                = $request->name;
            $quote['opportunity']         = $request->opportunity;
            $quote['status']              = $request->status;
            $quote['account']             = $request->account;
            $quote['date_quoted']         = $request->date_quoted;
            $quote['quote_number']        = $request->quote_number;
            $quote['billing_address']     = $request->billing_address;
            $quote['billing_city']        = $request->billing_city;
            $quote['billing_state']       = $request->billing_state;
            $quote['billing_country']     = $request->billing_country;
            $quote['billing_postalcode']  = $request->billing_postalcode;
            $quote['shipping_address']    = $request->shipping_address;
            $quote['shipping_city']       = $request->shipping_city;
            $quote['shipping_state']      = $request->shipping_state;
            $quote['shipping_country']    = $request->shipping_country;
            $quote['shipping_postalcode'] = $request->shipping_postalcode;
            $quote['billing_contact']     = $request->billing_contact;
            $quote['shipping_contact']    = $request->shipping_contact;
            // $quote['tax']                 = implode(',', $request->tax);
            $quote['shipping_provider']   = $request->shipping_provider;
            $quote['description']         = $request->description;
            $quote['created_by']          = \Auth::user()->creatorId();
            $quote->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'quote',
                            'stream_comment' => '',
                            'user_name' => $quote->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Quote Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Quote $quote
     *
     * @return \Illuminate\Http\Response
     */

    public function destroy(Quote $quote)
    {
        if (\Auth::user()->can('Delete Quote')) {
            $quote->delete();

            return redirect()->back()->with('success', __('Account Successfully delete.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $quotes = Quote::where('created_by', \Auth::user()->creatorId())->get();

        return view('quote.grid', compact('quotes'));
    }

    function quoteNumber()
    {
        $latest = Quote::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->quote_id + 1;
    }

    public function getaccount(Request $request)
    {
        if ($request->opportunities_id) {
            $opportunitie = Opportunities::where('id', $request->opportunities_id)->first()->toArray();

            $account = Account::find($opportunitie['account'])->toArray();

            return response()->json(
                [
                    'opportunitie' => $opportunitie,
                    'account' => $account,
                ]
            );
        }
    }

    public function quoteitem($id)
    {
        $quote = Quote::find($id);

        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select Item', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('quote.quoteitem', compact('items', 'quote', 'tax_rate'));
    }

    public function itemsDestroy($id)
    {
        $item = QuoteItem::find($id);
        $item->delete();

        return redirect()->back()->with('success', __('Item Successfully delete.'));
    }

    public function items(Request $request)
    {
        $items        = Product::where('id', $request->item_id)->first();
        $items->taxes = $items->tax($items->tax);

        return json_encode($items);
    }

    public function storeitem(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            []
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $quoteitem                = new QuoteItem();
        $quoteitem['quote_id']    = $request->id;
        $quoteitem['item']        = $request->item;
        $quoteitem['quantity']    = $request->quantity;
        $quoteitem['price']       = $request->price;
        $quoteitem['discount']    = $request->discount;
        $quoteitem['tax']         = $request->tax;
        $quoteitem['description'] = $request->description;
        $quoteitem['created_by']  = \Auth::user()->creatorId();
        $quoteitem->save();

        return redirect()->back()->with('success', __('Quote Item Successfully Created.'));
    }

    public function quoteitemEdit($id)
    {
        $quoteItem = QuoteItem::find($id);
        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select Item', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('quote.quoteitemEdit', compact('items', 'quoteItem', 'tax_rate'));
    }

    public function quoteitemUpdate(Request $request, $id)
    {

        $quoteitem                = QuoteItem::find($id);
        $quoteitem['item']        = $request->item;
        $quoteitem['quantity']    = $request->quantity;
        $quoteitem['price']       = $request->price;
        $quoteitem['discount']    = $request->discount;
        $quoteitem['tax']         = $request->tax;
        $quoteitem['description'] = $request->description;
        $quoteitem->save();

        return redirect()->back()->with('success', __('Quote Item Successfully Updated.'));
    }

    public function previewQuote($template, $color)
    {

        $objUser  = \Auth::user();
        $settings = Utility::settings();
        // qr
        if (isset($settings['quote_qr_enabled']) && $settings['quote_qr_enabled'] == 'on') {
            $qr_invoice = 'on';
        } else {
            $qr_invoice = 'off';
        }
        $quote    = new Quote();

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

        $quote->quote_id   = 1;
        $quote->issue_date = date('Y-m-d H:i:s');
        $quote->due_date   = date('Y-m-d H:i:s');
        $quote->items      = $items;

        $quote->totalTaxPrice = 60;
        $quote->totalQuantity = 3;
        $quote->totalRate     = 300;
        $quote->totalDiscount = 10;
        $quote->taxesData     = $taxesData;


        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);
        $logo = \App\Models\Utility::get_file('uploads/logo/');
        $dark_logo    = Utility::getValByName('company_logo_dark');
        $quote_logo = Utility::getValByName('quote_logo');
        if (isset($quote_logo) && !empty($quote_logo)) {
            $img = \App\Models\Utility::get_file('/') . $quote_logo;
        } else {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }

        return view('quote.templates.' . $template, compact('quote', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }

    public function saveQuoteTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);
        // qr
        if (isset($post['quote_template']) && isset($request->quote_qr_enabled) && $request->quote_qr_enabled == 'on') {
            $post['quote_qr_enabled'] = 'on';
        } else {
            $post['quote_qr_enabled'] = 'off';
        }
        if (isset($post['quote_template']) && (!isset($post['quote_color']) || empty($post['quote_color']))) {
            $post['quote_color'] = "ffffff";
        }

        if ($request->quote_logo) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'quote_logo' => 'image|mimes:png|max:2048',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $quote_logo         = $user->id . '_quote_logo.png';
            $dir = 'quote_logo/';

            $validation = [
                'mimes:' . 'png',
                'max:' . '20480',
            ];


            $path = Utility::upload_file($request, 'quote_logo', $quote_logo, $dir, $validation);
            if ($path['flag'] == 1) {
                $quote_logo = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            // $path                 = $request->file('quote_logo')->storeAs('quote_logo', $quote_logo);
            $post['quote_logo'] = $quote_logo;
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

        return redirect()->back()->with('success', __('Quote Setting successfully updated.'));
    }

    public function pdf($id)
    {
        $settings = Utility::settings();

        $quoteId = Crypt::decrypt($id);
        $quote   = Quote::where('id', $quoteId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('created_by', '=', $quote->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }
        $user         = new User();
        $user->name   = $quote->name;
        $user->email  = $quote->contacts->email ?? '';
        $user->mobile = $quote->contacts->phone ?? '';

        $user->bill_address = $quote->billing_address;
        $user->bill_zip     = $quote->billing_postalcode;
        $user->bill_city    = $quote->billing_city;
        $user->bill_country = $quote->billing_country;
        $user->bill_state   = $quote->billing_state;

        $user->address = $quote->shipping_address;
        $user->zip     = $quote->shipping_postalcode;
        $user->city    = $quote->shipping_city;
        $user->country = $quote->shipping_country;
        $user->state   = $quote->shipping_state;

        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach ($quote->itemsdata as $product) {
            $item           = new \stdClass();
            $item->name     = $product->item;
            $item->quantity = $product->quantity;
            $item->tax      = !empty($product->taxs) ? $product->taxs->rate : '';
            $item->discount = $product->discount;
            $item->price    = $product->price;
            $totalQuantity  += $item->quantity;
            $totalRate      += $item->price;
            $totalDiscount  += $item->discount;

            $taxes     = Utility::tax($product->tax);
            $itemTaxes = [];
            if (!empty($item->tax)) {
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
            } else {
                $item->itemTax = [];
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }
        $quote->issue_date = $quote->date_quoted;
        $quote->items         = $items;
        $quote->totalTaxPrice = $totalTaxPrice;
        $quote->totalQuantity = $totalQuantity;
        $quote->totalRate     = $totalRate;
        $quote->totalDiscount = $totalDiscount;
        $quote->taxesData     = $taxesData;

        //Set your logo
        $logo = \App\Models\Utility::get_file('uploads/logo/');
        // $company_logo = Utility::getValByName('company_logo');
        // $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));
        $dark_logo    = Utility::getValByName('company_logo_dark');
        $quote_logo = Utility::getValByName('quote_logo');
        if (isset($quote_logo) && !empty($quote_logo)) {
            $img = \App\Models\Utility::get_file('/') . $quote_logo;
        } else {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }



        if ($quote) {
            $color      = '#' . $settings['quote_color'];
            $font_color = Utility::getFontColor($color);

            return view('quote.templates.' . $settings['quote_template'], compact('quote', 'user', 'color', 'settings', 'img', 'font_color'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function convert($id)
    {
        $quote = Quote::find($id);

        $salesorder                        = new SalesOrder();
        $salesorder['user_id']             = $quote->user_id;
        $salesorder['salesorder_id']       = $this->salesorderNumber();
        $salesorder['name']                = $quote->name;
        $salesorder['quote']               = $id;
        $salesorder['opportunity']         = $quote->opportunity;
        $salesorder['status']              = $quote->status;
        $salesorder['account']             = $quote->account;
        $salesorder['amount']              = $quote->amount;
        $salesorder['date_quoted']         = $quote->date_quoted;
        $salesorder['quote_number']        = $quote->quote_number;
        $salesorder['billing_address']     = $quote->billing_address;
        $salesorder['billing_city']        = $quote->billing_city;
        $salesorder['billing_state']       = $quote->billing_state;
        $salesorder['billing_country']     = $quote->billing_country;
        $salesorder['billing_postalcode']  = $quote->billing_postalcode;
        $salesorder['shipping_address']    = $quote->shipping_address;
        $salesorder['shipping_city']       = $quote->shipping_city;
        $salesorder['shipping_state']      = $quote->shipping_state;
        $salesorder['shipping_country']    = $quote->shipping_country;
        $salesorder['shipping_postalcode'] = $quote->shipping_postalcode;
        $salesorder['billing_contact']     = $quote->billing_contact;
        $salesorder['shipping_contact']    = $quote->shipping_contact;
        $salesorder['tax']                 = $quote->tax;
        $salesorder['shipping_provider']   = $quote->shipping_provider;
        $salesorder['description']         = $quote->description;
        $salesorder['created_by']          = $quote->created_by;
        $salesorder->save();

        if (!empty($salesorder)) {
            $quote->converted_salesorder_id = $salesorder->id;
            $quote->save();
        }
        Stream::create(
            [
                'user_id' => \Auth::user()->id,
                'created_by' => \Auth::user()->creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => \Auth::user()->username,
                        'title' => 'salesorder',
                        'stream_comment' => '',
                        'user_name' => $salesorder->name,
                    ]
                ),
            ]
        );

        if ($salesorder) {
            $quotesProduct = QuoteItem::where('quote_id', $quote->id)->get();

            foreach ($quotesProduct as $product) {
                $salesorderProduct                = new SalesOrderItem();
                $salesorderProduct->salesorder_id = $salesorder->id;
                $salesorderProduct->item          = $product->item;
                $salesorderProduct->quantity      = $product->quantity;
                $salesorderProduct->price         = $product->price;
                $salesorderProduct->discount      = $product->discount;
                $salesorderProduct->tax           = $product->tax;
                $salesorderProduct->description   = $product->description;
                $salesorderProduct->created_by    = $product->created_by;
                $salesorderProduct->save();
            }
        }

        return redirect()->back()->with('success', __('Quotes to SalesOrder Successfully Converted.'));
    }

    function salesorderNumber()
    {
        $latest = SalesOrder::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();

        if (!$latest) {
            return 1;
        }

        return $latest->salesorder_id + 1;
    }

    public function duplicate($id)
    {
        if (\Auth::user()->can('Create Quote')) {
            $quote                            = Quote::find($id);

            $duplicate                        = new Quote();
            $duplicate['user_id']             = $quote->user_id;
            $duplicate['quote_id']            = $this->quoteNumber();
            $duplicate['name']                = $quote->name;
            $duplicate['opportunity']         = $quote->opportunity;
            $duplicate['status']              = $quote->status;
            $duplicate['account']             = $quote->account;
            $duplicate['amount']              = $quote->amount;
            $duplicate['date_quoted']         = $quote->date_quoted;
            $duplicate['quote_number']        = $quote->quote_number;
            $duplicate['billing_address']     = $quote->billing_address;
            $duplicate['billing_city']        = $quote->billing_city;
            $duplicate['billing_state']       = $quote->billing_state;
            $duplicate['billing_country']     = $quote->billing_country;
            $duplicate['billing_postalcode']  = $quote->billing_postalcode;
            $duplicate['shipping_address']    = $quote->shipping_address;
            $duplicate['shipping_city']       = $quote->shipping_city;
            $duplicate['shipping_state']      = $quote->shipping_state;
            $duplicate['shipping_country']    = $quote->shipping_country;
            $duplicate['shipping_postalcode'] = $quote->shipping_postalcode;
            $duplicate['billing_contact']     = $quote->billing_contact;
            $duplicate['shipping_contact']    = $quote->shipping_contact;
            $duplicate['tax']                 = $quote->tax;
            $duplicate['shipping_provider']   = $quote->shipping_provider;
            $duplicate['description']         = $quote->description;
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
                            'title' => 'quote',
                            'stream_comment' => '',
                            'user_name' => $quote->name,
                        ]
                    ),
                ]
            );

            if ($duplicate) {
                $quoteItem = QuoteItem::where('quote_id', $quote->id)->get();

                foreach ($quoteItem as $item) {

                    $quoteitem                = new QuoteItem();
                    $quoteitem['quote_id']    = $duplicate->id;
                    $quoteitem['item']        = $item->item;
                    $quoteitem['quantity']    = $item->quantity;
                    $quoteitem['price']       = $item->price;
                    $quoteitem['discount']    = $item->discount;
                    $quoteitem['tax']         = $item->tax;
                    $quoteitem['description'] = $item->description;
                    $quoteitem['created_by']  = \Auth::user()->creatorId();
                    $quoteitem->save();
                }
            }

            return redirect()->back()->with('success', __('Quote duplicate successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payquote($quote_id)
    {

        if (!empty($quote_id)) {

            try {
                $id  = \Illuminate\Support\Facades\Crypt::decrypt($quote_id);
            } catch (\RuntimeException $e) {
                return redirect()->back()->with('error', __('Quote not avaliable'));
            }
            // $id = \Illuminate\Support\Facades\Crypt::decrypt($quote_id);

            $quote = Quote::where('id', $id)->first();

            if (!is_null($quote)) {

                $settings = Utility::settings();

                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach ($quote->itemsdata as $item) {
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
                $quote->items         = $items;
                $quote->totalTaxPrice = $totalTaxPrice;
                $quote->totalQuantity = $totalQuantity;
                $quote->totalRate     = $totalRate;
                $quote->totalDiscount = $totalDiscount;
                $quote->taxesData     = $taxesData;

                $company_setting = Utility::settings();

                $ownerId = Utility::ownerIdforQuote($quote->created_by);

                $payment_setting = Utility::invoice_payment_settings($ownerId);
                $site_setting = Utility::settingsById($ownerId);


                $users = User::where('id', $quote->created_by)->first();

                if (!is_null($users)) {
                    \App::setLocale($users->lang);
                } else {
                    $users = User::where('type', 'owner')->first();
                    \App::setLocale($users->lang);
                }

                return view('quote.quotepay', compact('quote', 'company_setting', 'users', 'payment_setting', 'site_setting'));
            } else {
                return abort('404', 'The Link You Followed Has Expired');
            }
        } else {
            return abort('404', 'The Link You Followed Has Expired');
        }
    }

    public function fileExport()
    {
        $name = 'quote_' . date('Y-m-d i:h:s');
        $data = Excel::download(new QuoteExport(), $name . '.xlsx');
        ob_end_clean();


        return $data;
    }
}
