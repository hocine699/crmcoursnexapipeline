<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Opportunities;
use App\Models\Product;
use App\Models\ProductTax;
use App\Exports\SalesOrderExport;
use App\Models\Plan;
use App\Models\Quote;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\ShippingProvider;
use App\Models\Stream;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage SalesOrder')) {
            if (\Auth::user()->type == 'owner') {

                $salesorders = SalesOrder::with('assign_user', 'accounts')->where('created_by', \Auth::user()->creatorId())->get();
            } else {
                $salesorders = SalesOrder::with('assign_user', 'accounts')->where('user_id', \Auth::user()->id)->get();
            }
            return view('salesorder.index', compact('salesorders'));
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
        if (\Auth::user()->can('Create SalesOrder')) {
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account->prepend('--', 0);
            $opportunities = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunities->prepend('--', '');
            $quote = Quote::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $quote->prepend('--', '');
            $status  = SalesOrder::$status;
            $contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $contact->prepend('--', 0);
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('salesorder.create', compact('user', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider', 'quote', 'type', 'id'));
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
        if (\Auth::user()->can('Create SalesOrder')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
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
            $salesorder                        = new SalesOrder();
            $salesorder['user_id']             = $request->user;
            $salesorder['salesorder_id']       = $this->salesorderNumber();
            $salesorder['name']                = $request->name;
            $salesorder['quote']               = $request->quote;
            $salesorder['opportunity']         = $request->opportunity;
            $salesorder['status']              = $request->status;
            $salesorder['account']             = $request->account_id;
            $salesorder['date_quoted']         = $request->date_quoted;
            $salesorder['quote_number']        = $request->quote_number;
            $salesorder['billing_address']     = $request->billing_address;
            $salesorder['billing_city']        = $request->billing_city;
            $salesorder['billing_state']       = $request->billing_state;
            $salesorder['billing_country']     = $request->billing_country;
            $salesorder['billing_postalcode']  = $request->billing_postalcode;
            $salesorder['shipping_address']    = $request->shipping_address;
            $salesorder['shipping_city']       = $request->shipping_city;
            $salesorder['shipping_state']      = $request->shipping_state;
            $salesorder['shipping_country']    = $request->shipping_country;
            $salesorder['shipping_postalcode'] = $request->shipping_postalcode;
            $salesorder['billing_contact']     = $request->billing_contact;
            $salesorder['shipping_contact']    = $request->shipping_contact;
            // $salesorder['tax']                 = implode(',', $request->tax);
            $salesorder['shipping_provider']   = $request->shipping_provider;
            $salesorder['description']         = $request->description;

            $salesorder['created_by']          = \Auth::user()->creatorId();
            $salesorder->save();

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

            $Assign_user_phone = User::where('id', $request->user)->first();
            $setting  = Utility::settings(\Auth::user()->creatorId());

            $uArr = [
                'quote_number' => $request->quote_number,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'description' => $request->description,
                'date_quoted' => $request->date_quoted,
                // 'salesorder_assign_user' => $Assign_user_phone->name??'',
            ];
            $resp = Utility::sendEmailTemplate('new_sales_order', [$salesorder->id => $Assign_user_phone->email], $uArr);


            if (isset($setting['twilio_salesorder_create']) && $setting['twilio_salesorder_create'] == 1) {

                // $msg = "New Salesorder" . " " . \Auth::user()->salesorderNumberFormat($this->salesorderNumber()) . " created by " . \Auth::user()->name . '.';

                $uArr = [
                    'quote_number' => $request->quote_number,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                    'user_name' => \Auth::user()->name,
                ];
                Utility::send_twilio_msg($Assign_user_phone->phone, 'new_salesorder', $uArr);
            }
            //webhook
            $module = 'New Sales Order';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($salesorder);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status != true) {
                    $msg = "Webhook call failed.";
                }
            }
            if (\Auth::user()) {
                return redirect()->back()->with('success', __('Sales order successfully created!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\SalesOrder $salesOrder
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SalesOrder $salesOrder, $id)
    {
        if (\Auth::user()->can('Show SalesOrder')) {

            $salesOrder = SalesOrder::find($id);
            $settings   = Utility::settings();

            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach ($salesOrder->itemsdata as $item) {
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
            $salesOrder->items         = $items;
            $salesOrder->totalTaxPrice = $totalTaxPrice;
            $salesOrder->totalQuantity = $totalQuantity;
            $salesOrder->totalRate     = $totalRate;
            $salesOrder->totalDiscount = $totalDiscount;
            $salesOrder->taxesData     = $taxesData;

            $company_setting = Utility::settings();

            return view('salesorder.view', compact('salesOrder', 'company_setting'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SalesOrder $salesOrder
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SalesOrder $salesOrder, $id)
    {
        if (\Auth::user()->can('Edit SalesOrder')) {
            $salesOrder  = SalesOrder::find($id);
            $opportunity = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunity->prepend('--', 0);
            $quote = Quote::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $quote->prepend('--', '');
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account->prepend('--', 0);
            $billing_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $billing_contact->prepend('--', 0);
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            // $tax->prepend('No Tax', 0);
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $invoices = Invoice::where('salesorder', $salesOrder->id)->get();
            $status   = SalesOrder::$status;

            // get previous user id
            $previous = Quote::where('id', '<', $salesOrder->id)->max('id');
            // get next user id
            $next = Quote::where('id', '>', $salesOrder->id)->min('id');


            return view('salesorder.edit', compact('salesOrder', 'quote', 'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user', 'invoices', 'previous', 'next'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\SalesOrder $salesOrder
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('Edit SalesOrder')) {
            $salesOrder = SalesOrder::find($id);

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


            $salesOrder['user_id']             = $request->user;
            $salesOrder['salesorder_id']       = $this->salesorderNumber();
            $salesOrder['name']                = $request->name;
            $salesOrder['quote']               = $request->quote;
            $salesOrder['opportunity']         = $request->opportunity;
            $salesOrder['status']              = $request->status;
            $salesOrder['account']             = $request->account;
            $salesOrder['date_quoted']         = $request->date_quoted;
            $salesOrder['quote_number']        = $request->quote_number;
            $salesOrder['billing_address']     = $request->billing_address;
            $salesOrder['billing_city']        = $request->billing_city;
            $salesOrder['billing_state']       = $request->billing_state;
            $salesOrder['billing_country']     = $request->billing_country;
            $salesOrder['billing_postalcode']  = $request->billing_postalcode;
            $salesOrder['shipping_address']    = $request->shipping_address;
            $salesOrder['shipping_city']       = $request->shipping_city;
            $salesOrder['shipping_state']      = $request->shipping_state;
            $salesOrder['shipping_country']    = $request->shipping_country;
            $salesOrder['shipping_postalcode'] = $request->shipping_postalcode;
            $salesOrder['billing_contact']     = $request->billing_contact;
            $salesOrder['shipping_contact']    = $request->shipping_contact;
            // $salesOrder['tax']                 = implode(',', $request->tax);
            $salesOrder['shipping_provider']   = $request->shipping_provider;
            $salesOrder['description']         = $request->description;
            $salesOrder['created_by']          = \Auth::user()->creatorId();


            $salesOrder->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'salesOrder',
                            'stream_comment' => '',
                            'user_name' => $salesOrder->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Salesorder Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SalesOrder $salesOrder
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('Delete SalesOrder')) {

            $salesOrder = SalesOrder::find($id);
            $salesOrder->delete();

            return redirect()->back()->with('success', __('Sales Order Successfully delete.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $salesorders = SalesOrder::where('created_by', \Auth::user()->creatorId())->get();

        return view('salesorder.grid', compact('salesorders'));
    }

    function salesorderNumber()
    {
        $latest = SalesOrder::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();

        if (!$latest) {
            return 1;
        }

        return $latest->salesorder_id + 1;
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

    public function salesorderitem($id)
    {
        $salesorder = SalesOrder::find($id);

        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('salesorder.salesorderitem', compact('items', 'salesorder', 'tax_rate'));
    }

    public function salesorderItemEdit($id)
    {
        $salesorderItem = SalesOrderItem::find($id);
        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('salesorder.salesorderitemEdit', compact('items', 'salesorderItem', 'tax_rate'));
    }

    public function items(Request $request)
    {

        $items = Product::where('id', $request->item_id)->first();

        $items->taxes = $items->tax($items->tax);

        return json_encode($items);
    }

    public function itemsDestroy($id)
    {
        $item = SalesOrderItem::find($id);
        $item->delete();

        return redirect()->back()->with('success', __('Item Successfully delete.'));
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
        $salesorderitem                  = new SalesOrderItem();
        $salesorderitem['salesorder_id'] = $request->id;
        $salesorderitem['item']          = $request->item;
        $salesorderitem['quantity']      = $request->quantity;
        $salesorderitem['price']         = $request->price;
        $salesorderitem['discount']      = $request->discount;
        $salesorderitem['tax']           = $request->tax;
        $salesorderitem['description']   = $request->description;
        $salesorderitem['created_by']    = \Auth::user()->creatorId();
        $salesorderitem->save();

        return redirect()->back()->with('success', __('Sales Order Item Successfully Created.'));
    }

    public function salesorderItemUpdate(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            []
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $salesorderitem                = SalesOrderItem::find($id);
        $salesorderitem['item']        = $request->item;
        $salesorderitem['quantity']    = $request->quantity;
        $salesorderitem['price']       = $request->price;
        $salesorderitem['discount']    = $request->discount;
        $salesorderitem['tax']         = $request->tax;
        $salesorderitem['description'] = $request->description;
        $salesorderitem->save();

        return redirect()->back()->with('success', __('Sales Order Item Successfully Updated.'));
    }

    public function previewInvoice($template, $color)
    {
        $objUser    = \Auth::user();
        $settings   = Utility::settings();
        //     qr
        if (isset($settings['salesorder_qr_enabled']) && $settings['salesorder_qr_enabled'] == 'on') {
            $qr_salesorder = 'on';
        } else {
            $qr_salesorder = 'off';
        }
        $salesorder = new SalesOrder();

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

        $salesorder->invoice_id = 1;
        $salesorder->issue_date = date('Y-m-d H:i:s');
        $salesorder->due_date   = date('Y-m-d H:i:s');
        $salesorder->items      = $items;

        $salesorder->totalTaxPrice = 60;
        $salesorder->totalQuantity = 3;
        $salesorder->totalRate     = 300;
        $salesorder->totalDiscount = 10;
        $salesorder->taxesData     = $taxesData;


        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $logo = \App\Models\Utility::get_file('uploads/logo/');
        $dark_logo    = Utility::getValByName('company_logo_dark');
        $salesorder_logo = Utility::getValByName('salesorder_logo');
        if (isset($salesorder_logo) && !empty($salesorder_logo)) {
            $img = \App\Models\Utility::get_file('/') . $salesorder_logo;
        } else {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }

        return view('salesorder.templates.' . $template, compact('salesorder', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }

    public function saveSalesorderTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);
        // qr
        if (isset($post['salesorder_template']) && isset($request->salesorder_qr_enabled) && $request->salesorder_qr_enabled == 'on') {
            $post['salesorder_qr_enabled'] = 'on';
        } else {
            $post['salesorder_qr_enabled'] = 'off';
        }
        if (isset($post['salesorder_template']) && (!isset($post['salesorder_color']) || empty($post['salesorder_color']))) {
            $post['salesorder_color'] = "ffffff";
        }

        if ($request->salesorder_logo) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'salesorder_logo' => 'image|mimes:png|max:2048',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $salesorder_logo         = $user->id . '_salesorder_logo.png';
            $dir = 'salesorder_logo/';

            $validation = [
                'mimes:' . 'png',
                'max:' . '20480',
            ];


            $path = Utility::upload_file($request, 'salesorder_logo', $salesorder_logo, $dir, $validation);
            if ($path['flag'] == 1) {
                $salesorder_logo = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            // $path                 = $request->file('salesorder_logo')->storeAs('salesorder_logo', $salesorder_logo);
            $post['salesorder_logo'] = $salesorder_logo;
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

        $salesorderId = Crypt::decrypt($id);
        $salesorder   = SalesOrder::where('id', $salesorderId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('created_by', '=', $salesorder->created_by);
        $data1 = $data->get();

        foreach ($data1 as $row) {
            $settings[$row->name] = $row->value;
        }

        $user         = new User();
        $user->name   = $salesorder->name;
        $user->email  = $salesorder->contacts->email ?? '';
        $user->mobile = $salesorder->contacts->phone ?? '';

        $user->bill_address = $salesorder->billing_address;
        $user->bill_zip     = $salesorder->billing_postalcode;
        $user->bill_city    = $salesorder->billing_city;
        $user->bill_country = $salesorder->billing_country;
        $user->bill_state   = $salesorder->billing_state;

        $user->address = $salesorder->shipping_address;
        $user->zip     = $salesorder->shipping_postalcode;
        $user->city    = $salesorder->shipping_city;
        $user->country = $salesorder->shipping_country;
        $user->state   = $salesorder->shipping_state;


        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach ($salesorder->itemsdata as $product) {
            $item           = new \stdClass();
            $item->name     = $product->item;
            $item->quantity = $product->quantity;
            $item->tax      = !empty($product->taxs) ? $product->taxs->rate : '';
            $item->discount = $product->discount;
            $item->price    = $product->price;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            //     $taxes = \App\Models\Utility::tax($product->tax);

            //     $itemTaxes = [];
            //     foreach ($taxes as $tax) {
            //         $taxPrice      = \App\Models\Utility::taxRate($tax->rate, $item->price, $item->quantity);
            //         $totalTaxPrice += $taxPrice;

            //         $itemTax['name']  = $tax->tax_name;
            //         $itemTax['rate']  = $tax->rate . '%';
            //         $itemTax['price'] = \App\Models\Utility::priceFormat($settings, $taxPrice);
            //         $itemTaxes[]      = $itemTax;


            //         if (array_key_exists($tax->tax_name, $taxesData)) {
            //             $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
            //         } else {
            //             $taxesData[$tax->tax_name] = $taxPrice;
            //         }
            //     }
            //     $item->itemTax = $itemTaxes;
            //     $items[]       = $item;
        }
        $salesorder->issue_date    = $salesorder->date_quoted;
        $salesorder->items         = $items;
        $salesorder->totalTaxPrice = $totalTaxPrice;
        $salesorder->totalQuantity = $totalQuantity;
        $salesorder->totalRate     = $totalRate;
        $salesorder->totalDiscount = $totalDiscount;
        $salesorder->taxesData     = $taxesData;

        //Set your logo
        $logo = \App\Models\Utility::get_file('uploads/logo/');
        $dark_logo    = Utility::getValByName('company_logo_dark');
        $salesorder_logo = Utility::getValByName('salesorder_logo');
        if (isset($salesorder_logo) && !empty($salesorder_logo)) {
            $img = \App\Models\Utility::get_file('/') . $salesorder_logo;
        } else {
            $img = asset($logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png'));
        }

        if ($salesorder) {
            $color      = '#' . $settings['salesorder_color'];
            $font_color = Utility::getFontColor($color);

            return view('salesorder.templates.' . $settings['salesorder_template'], compact('salesorder', 'user', 'color', 'settings', 'img', 'font_color'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function duplicate($id)
    {
        if (\Auth::user()->can('Create Quote')) {
            $salesorder = SalesOrder::find($id);

            $duplicate                        = new SalesOrder();
            $duplicate['user_id']             = $salesorder->user_id;
            $duplicate['salesorder_id']       = $this->salesorderNumber();
            $duplicate['name']                = $salesorder->name;
            $duplicate['quote']               = $salesorder->quote;
            $duplicate['opportunity']         = $salesorder->opportunity;
            $duplicate['status']              = $salesorder->status;
            $duplicate['account']             = $salesorder->account;
            $duplicate['amount']              = $salesorder->amount;
            $duplicate['date_quoted']         = $salesorder->date_quoted;
            $duplicate['quote_number']        = $salesorder->quote_number;
            $duplicate['billing_address']     = $salesorder->billing_address;
            $duplicate['billing_city']        = $salesorder->billing_city;
            $duplicate['billing_state']       = $salesorder->billing_state;
            $duplicate['billing_country']     = $salesorder->billing_country;
            $duplicate['billing_postalcode']  = $salesorder->billing_postalcode;
            $duplicate['shipping_address']    = $salesorder->shipping_address;
            $duplicate['shipping_city']       = $salesorder->shipping_city;
            $duplicate['shipping_state']      = $salesorder->shipping_state;
            $duplicate['shipping_country']    = $salesorder->shipping_country;
            $duplicate['shipping_postalcode'] = $salesorder->shipping_postalcode;
            $duplicate['billing_contact']     = $salesorder->billing_contact;
            $duplicate['shipping_contact']    = $salesorder->shipping_contact;
            $duplicate['tax']                 = $salesorder->tax;
            $duplicate['shipping_provider']   = $salesorder->shipping_provider;
            $duplicate['description']         = $salesorder->description;
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
                            'title' => 'salesorder',
                            'stream_comment' => '',
                            'user_name' => $salesorder->name,
                        ]
                    ),
                ]
            );

            if ($duplicate) {
                $salesorderItem = SalesOrderItem::where('salesorder_id', $salesorder->id)->get();

                foreach ($salesorderItem as $item) {

                    $salesorderitem                  = new SalesOrderItem();
                    $salesorderitem['salesorder_id'] = $duplicate->id;
                    $salesorderitem['item']          = $item->item;
                    $salesorderitem['quantity']      = $item->quantity;
                    $salesorderitem['price']         = $item->price;
                    $salesorderitem['discount']      = $item->discount;
                    $salesorderitem['tax']           = $item->tax;
                    $salesorderitem['description']   = $item->description;
                    $salesorderitem['created_by']    = \Auth::user()->creatorId();
                    $salesorderitem->save();
                }
            }

            return redirect()->back()->with('success', __('Salesorder duplicate successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function paysalesorder($salesorder_id)
    {

        if (!empty($salesorder_id)) {
            try {
                $id  = \Illuminate\Support\Facades\Crypt::decrypt($salesorder_id);
            } catch (\RuntimeException $e) {
                return redirect()->back()->with('error', __('Sales Order not avaliable'));
            }
            // $id = \Illuminate\Support\Facades\Crypt::decrypt($salesorder_id);

            $salesorder = SalesOrder::where('id', $id)->first();

            if (!is_null($salesorder)) {

                $settings = Utility::settings();

                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach ($salesorder->itemsdata as $item) {
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

                            // if (array_key_exists('No Tax', $taxesData)) {
                            //     $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                            // } else {
                            //     $taxesData['No Tax'] = $taxPrice;
                            // }
                        }
                    }
                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $salesorder->items         = $items;
                $salesorder->totalTaxPrice = $totalTaxPrice;
                $salesorder->totalQuantity = $totalQuantity;
                $salesorder->totalRate     = $totalRate;
                $salesorder->totalDiscount = $totalDiscount;
                $salesorder->taxesData     = $taxesData;

                $company_setting = Utility::settings();

                $ownerId = Utility::ownerIdforSalesorder($salesorder->created_by);

                $payment_setting = Utility::invoice_payment_settings($ownerId);
                $site_setting = Utility::settingsById($ownerId);


                $users = User::where('id', $salesorder->created_by)->first();

                if (!is_null($users)) {
                    \App::setLocale($users->lang);
                } else {
                    $users = User::where('type', 'owner')->first();
                    \App::setLocale($users->lang);
                }

                return view('salesorder.salesorderpay', compact('salesorder', 'company_setting', 'users', 'payment_setting', 'site_setting'));
            } else {
                return abort('404', 'The Link You Followed Has Expired');
            }
        } else {
            return abort('404', 'The Link You Followed Has Expired');
        }
    }


    public function fileExport()
    {
        $name = 'Salesorder_' . date('Y-m-d i:h:s');
        $data = Excel::download(new SalesOrderExport(), $name . '.xlsx');
        ob_end_clean();


        return $data;
    }
}
