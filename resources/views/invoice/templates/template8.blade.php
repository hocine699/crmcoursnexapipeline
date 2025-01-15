@php
    $setting = App\Models\Utility::colorset();

@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    @if (env('SITE_RTL') == 'on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    <style type="text/css">
        :root {
            --theme-color: #f64f81;
            --white: #ffffff;
            --black: #000000;
        }

        body {
            font-family: 'Lato', sans-serif;
        }

        p,
        li,
        ul,
        ol {
            margin: 0;
            padding: 0;
            list-style: none;
            line-height: 1.5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr th {
            padding: 0.75rem;
            text-align: left;
        }

        table tr td {
            padding: 0.75rem;
            text-align: left;
        }

        table th small {
            display: block;
            font-size: 12px;
        }

        .invoice-preview-main {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            background: #ffff;
            box-shadow: 0 0 10px #ddd;
        }

        .invoice-logo {
            max-width: 200px;
            width: 100%;
        }

        .invoice-header table td {
            padding: 15px 30px;
        }

        .text-right {
            text-align: right;
        }

        .no-space tr td {
            padding: 0;
        }

        .vertical-align-top td {
            vertical-align: top;
        }

        .view-qrcode {
            max-width: 114px;
            height: 114px;
            margin-left: auto;
            margin-top: 15px;
            background: var(--white);
        }

        .view-qrcode img {
            width: 100%;
            height: 100%;
        }

        .invoice-body {
            padding: 30px 25px 0;
        }

        table.add-border tr {
            border-top: 1px solid var(--theme-color);
        }

        tfoot tr:first-of-type {
            border-bottom: 1px solid var(--theme-color);
        }

        .total-table tr:first-of-type td {
            padding-top: 0;
        }

        .total-table tr:first-of-type {
            border-top: 0;
        }

        .sub-total {
            padding-right: 0;
            padding-left: 0;
        }

        .border-0 {
            border: none !important;
        }

        .invoice-summary td,
        .invoice-summary th {
            font-size: 13px;
            font-weight: 600;
        }

        .total-table td:last-of-type {
            width: 146px;
        }

        .invoice-footer {
            padding: 15px 20px;
        }

        .itm-description td {
            padding-top: 0;
        }

        html[dir="rtl"] table tr td,
        html[dir="rtl"] table tr th {
            text-align: right;
        }

        html[dir="rtl"] .text-right {
            text-align: left;
        }

        html[dir="rtl"] .view-qrcode {
            margin-left: 0;
            margin-right: auto;
        }

        p:not(:last-of-type) {
            margin-bottom: 15px;
        }

        .invoice-summary p {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="invoice-preview-main" id="boxes">
        <div class="invoice-header">
            <table>
                <tbody>
                    <tr style="border-bottom:1px solid  {{$color}};">
                        <td>
                            <img data-v-136bf9b5="" src="{{ $img }}" class="d-logo" style="max-width: 250px;">
                        </td>
                        <td class="text-right">
                            <h3
                                style="text-transform: uppercase; font-size: 40px; font-weight: bold; color: {{ $color }};">
                                INVOICE</h3>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="vertical-align-top">
                <tbody>
                    <tr>
                        <td>
                            <strong data-v-f2a183a6="">{{ __('From') }}:</strong>
                            <p>
                                @if ($settings['company_name'])
                                    {{ $settings['company_name'] }}
                                @endif
                                <br>
                                @if ($settings['company_address'])
                                    {{ $settings['company_address'] }}
                                @endif
                                @if ($settings['company_city'])
                                    <br> {{ $settings['company_city'] }},
                                @endif
                                @if ($settings['company_state'])
                                    {{ $settings['company_state'] }}
                                @endif
                                @if ($settings['company_zipcode'])
                                    - {{ $settings['company_zipcode'] }}
                                @endif
                                @if ($settings['company_country'])
                                    <br>{{ $settings['company_country'] }}
                                @endif
                            </p>
                        </td>
                        <td>
                            <table class="no-space">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <div class="view-qrcode" style="margin-top: 0; margin-bottom: 15px;">
                                    @if ($settings['is_qr_enabled'] == 'on')

                                                {!! DNS2D::getBarcodeHTML(
                                                    route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                                    'QRCODE',
                                                    2,
                                                    2,
                                                ) !!}
                                                @else
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Number: </td>
                                        <td class="text-right">
                                            {{ \App\Models\Utility::invoiceNumberFormat($settings, $invoice->invoice) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Issue Date:</td>
                                        <td class="text-right">
                                            {{ \App\Models\Utility::dateFormat($settings, $invoice->issue_date) }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="invoice-body">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <strong style="margin-bottom: 10px; display:block;">Bill To:</strong>
                            <p>
                                {{ !empty($user->name) ? $user->name : '' }}<br>
                                {{ !empty($user->email) ? $user->email : '' }}<br>
                                {{ !empty($user->mobile) ? $user->mobile : '' }}<br>
                                {{ !empty($user->bill_address) ? $user->bill_address : '' }}<br>
                                {{ !empty($user->bill_zip) ? $user->bill_zip : '' }}<br>
                                {{ !empty($user->bill_city) ? $user->bill_city : '' . '' }}
                                {{ !empty($user->bill_state) ? $user->bill_state : '' }} {{ !empty($user->bill_country) ? $user->bill_country : '' }}
                            </p>
                        </td>
                        <td class="text-right">
                            <strong style="margin-bottom: 10px; display:block;">Ship To:</strong>
                            <p>
                                {{ !empty($user->name) ? $user->name : '' }}<br>
                                {{ !empty($user->email) ? $user->email : '' }}<br>
                                {{ !empty($user->mobile) ? $user->mobile : '' }}<br>
                                {{ !empty($user->address) ? $user->address : '' }}<br>
                                {{ !empty($user->zip) ? $user->zip : '' }}<br>
                                {{ !empty($user->city) ? $user->city : '' . ', ' }},{{ !empty($user->state) ? $user->state : '' }},{{ !empty($user->country) ? $user->country : '' }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="add-border invoice-summary" style="margin-top: 30px;">
                <thead style="background: {{$color}};color:{{$font_color}}">
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Tax (%)</th>
                        <th>Discount</th>
                        <th>Price <small>before tax & discount</small></th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($invoice->items) && count($invoice->items) > 0)
                        @foreach ($invoice->items as $key => $item)
                            <tr style="border-bottom:1px solid {{ $color }};">
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Models\Utility::priceFormat($settings, $item->price) }}</td>
                                <td>
                                    @foreach ($item->itemTax as $taxes)
                                        <span>{{ $taxes['name'] }}</span> <span>({{ $taxes['rate'] }})</span>
                                        <span>{{ $taxes['price'] }}</span>
                                    @endforeach
                                </td>
                                @if ($item->discount != 0)
                                    <td>{{ \App\Models\Utility::priceFormat($settings, $item->discount) }}</td>
                                @else
                                    <td>-</td>
                                @endif
                                <td>{{ \App\Models\Utility::priceFormat($settings, $item->price * $item->quantity) }}
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>{{ $invoice->totalQuantity }}</td>
                        <td>{{ \App\Models\Utility::priceFormat($settings, $invoice->totalRate) }}</td>
                        <td>{{ \App\Models\Utility::priceFormat($settings, $invoice->totalTaxPrice) }}</td>
                        @if ($invoice->discount_apply == 1)
                            <td>{{ \App\Models\Utility::priceFormat($settings, $invoice->totalDiscount) }}</td>
                        @else
                            <td>-</td>
                        @endif
                        <td>{{ \App\Models\Utility::priceFormat($settings, $invoice->getSubTotal()) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td colspan="2" class="sub-total">
                            <table class="total-table">
                                @if ($invoice->discount_apply == 1)
                                    @if ($invoice->getTotalDiscount())
                                        <tr>
                                            <td>Discount :</td>
                                            <td>{{ \App\Models\Utility::priceFormat($settings, $invoice->getTotalDiscount()) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                @if (!empty($invoice->taxesData))
                                    @foreach ($invoice->taxesData as $taxName => $taxPrice)
                                        <tr>
                                            <td>{{ $taxName }} :</td>
                                            <td>{{ \App\Models\Utility::priceFormat($settings, $taxPrice) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>Total:</td>
                                    <td>{{ \App\Models\Utility::priceFormat($settings, $invoice->getSubTotal() - $invoice->getTotalDiscount() + $invoice->getTotalTax()) }}
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>
                </tfoot>
                <div class="d-header-50">
                    <p>
                        {{ $settings['footer_title'] }}<br>
                        {{ $settings['footer_notes'] }}
                    </p>
                </div>
        </div>
    </div>
    @if (!isset($preview))
        @include('invoice.script')
    @endif
</body>

</html>
