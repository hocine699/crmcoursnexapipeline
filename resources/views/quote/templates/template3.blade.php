@php
    $setting = App\Models\Utility::colorset();

@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style type="text/css">
        :root {
            --theme-color: #f50102;
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
            <table class="vertical-align-top">
                <tbody>
                    <tr>
                        <td>
                            <h3 style="text-transform: uppercase; font-size: 20px; font-weight: bold;">QUOTE</h3>
                            <div class="view-qrcode" style="margin-left: 0; margin-right: 0;">
                                @if ($settings['quote_qr_enabled'] == 'on')

                                {!! DNS2D::getBarcodeHTML(
                                    route('pay.quote', \Illuminate\Support\Facades\Crypt::encrypt($quote->id)),
                                    'QRCODE',
                                    2,
                                    2,
                                ) !!}
                                @else
                                @endif 
                            </div>
                        </td>

                        <td class="text-right">
                            <img data-v-e95a8a8c="" src="{{ $img }}" style="max-width: 250px" class="d-logo">
        </div>
        </td>
        </tr>
        </tbody>
        </table>
        <table class="vertical-align-top">
            <tbody>
                <tr>
                    <td>
                        <strong>{{ __('From') }}:</strong>
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
                                    <td>Number: </td>
                                    <td class="text-right">
                                        {{ \App\Models\Utility::quoteNumberFormat($settings, $quote->quote) }}</td>
                                </tr>
                                <tr>
                                    <td>Issue Date:</td>
                                    <td class="text-right">
                                        {{ \App\Models\Utility::dateFormat($settings, $quote->date_quoted) }}</td>
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
                            {{ !empty($user->bill_city) ? $user->bill_city : '' . ', ' }}
                            {{ !empty($user->bill_state) ? $user->bill_state : '' }},{{ !empty($user->bill_country) ? $user->bill_country : '' }}
                        </p>
                    </td>
                    @if ($settings['shipping_display'] == 'on')
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
                    @endif
                </tr>
            </tbody>
        </table>
        <table class="add-border invoice-summary" style="margin-top: 30px;">
            <thead style="background: {{ $color }};color:{{ $font_color }}">
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
                @if (isset($quote->items) && count($quote->items) > 0)
                    @foreach ($quote->items as $key => $item)
                        <tr>
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
                    <td>{{ $quote->totalQuantity }}</td>
                    <td>{{ \App\Models\Utility::priceFormat($settings, $quote->totalRate) }}</td>
                    <td>{{ \App\Models\Utility::priceFormat($settings, $quote->totalTaxPrice) }}</td>
                    @if ($quote->discount_apply == 1)
                        <td>{{ \App\Models\Utility::priceFormat($settings, $quote->totalDiscount) }}</td>
                    @else
                        <td>-</td>
                    @endif
                    <td>{{ \App\Models\Utility::priceFormat($settings, $quote->getSubTotal()) }}</td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="2" class="sub-total">
                        <table class="total-table">
                            @if ($quote->discount_apply == 1)
                                @if ($quote->getTotalDiscount())
                                    <tr>
                                        <td>Discount :</td>
                                        <td>{{ \App\Models\Utility::priceFormat($settings, $quote->getTotalDiscount()) }}
                                        </td>
                                    </tr>
                                @endif
                            @endif
                            @if (!empty($quote->taxesData))
                                @foreach ($quote->taxesData as $taxName => $taxPrice)
                                    <tr>
                                        <td>{{ $taxName }} :</td>
                                        <td>{{ \App\Models\Utility::priceFormat($settings, $taxPrice) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td>Total:</td>
                                <td>{{ \App\Models\Utility::priceFormat($settings, $quote->getSubTotal() - $quote->getTotalDiscount() + $quote->getTotalTax()) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    </div>
    @if (!isset($preview))
        @include('quote.script')
    @endif
</body>

</html>
