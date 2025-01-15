@extends('layouts.admin')

@section('page-title')
    {{ __('Quote') }}
@endsection
@section('title')
    {{ __('Quote') }} {{ '(' . $quote->name . ')' }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('quote.index') }}">{{ __('Quote') }}</a></li>
    <li class="breadcrumb-item">{{ __('show') }}</li>
@endsection
@section('action-btn')
    <div class="action-btn bg-warning ms-2">
        <a href="{{ route('quote.pdf', \Crypt::encrypt($quote->id)) }}" target="_blank" class="btn btn-sm btn-primary btn-icon"
            data-bs-toggle="tooltip" title="{{ __('Print') }}">
            <span class="btn-inner--icon text-white"><i class="ti ti-printer"></i></span>
        </a>
    </div>
    @if (Auth::user()->type == 'owner')
        <div class="action-btn ms-2">
            <a href="#" class="btn btn-sm btn-warning btn-icon m-1 cp_link"
                data-link="{{ route('pay.quote', \Illuminate\Support\Facades\Crypt::encrypt($quote->id)) }}"
                data-bs-toggle="tooltip"
                data-title="{{ __('Click to copy Quote link') }}"title="{{ __('copy quote') }}"><span
                    class="btn-inner--icon text-white"><i class="ti ti-file"></i></span></a>
        </div>
    @endif
    @can('Edit Quote')
        <div class="action-btn ms-2">
            <a href="{{ route('quote.edit', $quote->id) }}" class="btn btn-sm btn-info btn-icon m-1" data-bs-toggle="tooltip" data-title="{{ __('Edit Quote Item') }}"
                title="{{ __('Edit') }}" data-title="{{ __('Edit Quote') }}"><i class="ti ti-pencil"></i></a>
        </div>
    @endcan
    <div class="action-btn ms-2">
        <a href="#" data-url="{{ route('quote.quoteitem', $quote->id) }}" data-size="md" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Quote Item') }}"title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon m-1">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection
@php
        $setting = \App\Models\Utility::settings();

@endphp
@section('content')

    <div class="row">
        <div class="col-lg-10">
            <!-- [ Invoice ] start -->
            <div class="container">
                <div>
                    <div class="card" id="printTable">
                        <div class="card-body">
                            <div class="row align-items-center mb-4">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <div class="col-lg-6 col-md-8 pl-0">
                                        <h6 class="d-inline-block m-0 d-print-none">{{ __('Quote ID') }}</h6>
                                        <span class="col-sm-8"><span
                                                class="text-sm">{{ \Auth::user()->quoteNumberFormat($quote->id) }}</span></span>
                                    </div>
                                    <div class="col-lg-6 col-md-8 pl-0 mt-3">
                                        <h6 class="d-inline-block m-0 d-print-none">{{ __('Quote Date') }}</h6>
                                        <span class="col-sm-8"><span
                                                class="text-sm">{{ \Auth::user()->dateFormat($quote->created_at) }}</span></span>
                                    </div>
                                    <h6 class="d-inline-block m-0 d-print-none mt-3">{{ __('Quote ') }}</h6>

                                    @if ($quote->status == 0)
                                        <span
                                            class="badge bg-primary p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 1)
                                        <span
                                            class="badge bg-danger p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 2)
                                        <span
                                            class="badge bg-warning p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 3)
                                        <span
                                            class="badge bg-success p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 4)
                                        <span
                                            class="badge bg-info p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @endif
                                </div>
                                <div class="col-sm-6 text-sm-end">

                                    <div>

                                        <div class="float-end mt-3">
                                            @if ($setting['quote_qr_enabled'] == 'on')
                                            {!! DNS2D::getBarcodeHTML(
                                                route('pay.quote', \Illuminate\Support\Facades\Crypt::encrypt($quote->id)),
                                                'QRCODE',
                                                2,
                                                2,
                                            ) !!}
                                            @else
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <h5 class="px-2 py-2"><b>{{ __('Item List') }}</b></h5>
                                    <div class="table-responsive mt-4">
                                        <table class="table invoice-detail-table">
                                            <thead>
                                                <tr class="thead-default">
                                                    <th>{{ __('Item') }}</th>
                                                    <th>{{ __('Quantity') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('Tax') }}</th>
                                                    <th>{{ __('Discount') }}</th>
                                                    <th>{{ __('Description') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalQuantity = 0;
                                                    $totalRate = 0;
                                                    $totalAmount = 0;
                                                    $totalTaxPrice = 0;
                                                    $totalDiscount = 0;
                                                    $taxesData = [];
                                                    $TaxPrice_array=[];
                                                @endphp
                                                @foreach ($quote->items as $key => $quoteitem)
                                                    @php
                                                        $taxes = \Utility::tax($quoteitem->tax);
                                                        $totalQuantity += $quoteitem->quantity;
                                                        $totalRate += $quoteitem->price;
                                                        $totalDiscount += $quoteitem->discount;

                                                        if (!empty($taxes[0])) {
                                                            foreach ($taxes as $taxe) {
                                                                $taxDataPrice = \Utility::taxRate($taxe->rate, $quoteitem->price, $quoteitem->quantity);
                                                                if (array_key_exists($taxe->tax_name, $taxesData)) {
                                                                    $taxesData[$taxe->tax_name] = $taxesData[$taxe->tax_name] + $taxDataPrice;
                                                                } else {
                                                                    $taxesData[$taxe->tax_name] = $taxDataPrice;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ !empty($quoteitem->items->name) ? $quoteitem->items->name : '' }}
                                                        </td>
                                                        <td>{{ $quoteitem->quantity }} </td>
                                                        <td>{{ \Auth::user()->priceFormat($quoteitem->price) }} </td>
                                                        <td>
                                                            <div class="col">
                                                                @php
                                                                    $totalTaxPrice = 0;
                                                                    $data = 0;
                                                                @endphp
                                                                @if (!empty($quoteitem->tax))
                                                                    @foreach ($quoteitem->tax($quoteitem->tax) as $tax)
                                                                        @php
                                                                            $taxPrice = \Utility::taxRate($tax->rate,$quoteitem->price,$quoteitem->quantity,$quoteitem->discount);
                                                                            $totalTaxPrice += $taxPrice;
                                                                            $data+=$taxPrice;
                                                                        @endphp
                                                                        <a href="#!" class="d-block text-sm text-muted">{{$tax->tax_name .' ('.$tax->rate .'%)'}} &nbsp;&nbsp;{{\Auth::user()->priceFormat($taxPrice)}}</a>
                                                                    @endforeach
                                                                    @php
                                                                    array_push($TaxPrice_array,$data);
                                                                @endphp
                                                                @else
                                                                    <a href="#!"
                                                                        class="d-block text-sm text-muted">{{ __('No Tax') }}</a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="px-0">
                                                            {{ \Auth::user()->priceFormat($quoteitem->discount) }} </td>
                                                        <td class="px-0">
                                                            {{ !empty($quoteitem->description) ? $quoteitem->description : '--' }}
                                                        </td>
                                                        <td class="text-right">
                                                            @php
                                                            $tr_tex = (array_key_exists($key,$TaxPrice_array) == true) ? $TaxPrice_array[$key] : 0;
                                                        @endphp
                                                            {{ \Auth::user()->priceFormat($quoteitem->price * $quoteitem->quantity - $quoteitem->discount + $tr_tex) }}
                                                        </td>
                                                        <td>
                                                            @can('Edit Quote')
                                                                <div class="action-btn me-2">
                                                                    <a href="#"
                                                                        data-url="{{ route('quote.quoteitem.edit', $quoteitem->id) }}"
                                                                        data-ajax-popup="true"
                                                                        class="mx-3 bg-info btn btn-sm align-items-center text-white"
                                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('Edit Quote') }}"><i
                                                                            class="ti ti-pencil"></i></a>
                                                                </div>
                                                            @endcan
                                                            @can('Delete Quote')
                                                                <div class="action-btn">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['quote.items.delete', $quoteitem->id]]) !!}
                                                                    <a href="#!"
                                                                        class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
                                                                        data-bs-toggle="tooltip" title='Delete'>
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        </td>
                                                        @php
                                                            $totalQuantity += $quoteitem->quantity;
                                                            $totalRate += $quoteitem->price;
                                                            $totalDiscount += $quoteitem->discount;
                                                            $totalAmount += $quoteitem->price * $quoteitem->quantity;
                                                        @endphp

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="invoice-total">
                                        <table class="table invoice-table ">
                                            <tbody>
                                                <tr>
                                                    <th>{{__('Sub Total :')}}</th>
                                                    <td>{{ \Auth::user()->priceFormat($quote->getSubTotal()) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{__('Discount :')}}</th>
                                                    <td>{{ \Auth::user()->priceFormat($quote->getTotalDiscount()) }}</td>
                                                </tr>
                                                    @if (!empty($taxesData))
                                                        @foreach ($taxesData as $taxName => $taxPrice)
                                                            @if ($taxName != 'No Tax')
                                                            <tr>
                                                                <th>{{ $taxName }} :</th>
                                                                <td>{{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                <tr>
                                                    <td>
                                                        <hr />
                                                        <h5 class="text-primary m-r-10">Total :</h5>
                                                    </td>

                                                    <td>
                                                        <hr />
                                                        <h5 class="text-primary subTotal">
                                                            {{ \Auth::user()->priceFormat($quote->getTotal()) }}</h5>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <h5>{{ __('From') }}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Company Address') }}</span></dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_address'] }}</span></dd>

                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Company City') }}</span></dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_city'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{ __('Zip Code') }}</span>
                                        </dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_zipcode'] }}</span></dd>

                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Company Country') }}</span></dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_country'] }}</span></dd>

                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Company Contact') }}</span></dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_telephone'] }}</span></dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h5>{{ __('Billing Address') }}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Billing Address') }}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_address }}</span>
                                        </dd>

                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Billing City') }}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_city }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{ __('Zip Code') }}</span>
                                        </dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $quote->billing_postalcode }}</span></dd>

                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Billing Country') }}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_country }}</span>
                                        </dd>

                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Billing Contact') }}</span></dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ !empty($quote->contacts->name) ? $quote->contacts->name : '--' }}</span>
                                        </dd>
                                    </dl>
                                </div>
                                @if (\Utility::getValByName('shipping_display') == 'on')
                                    <div class="col-12 col-md-4">
                                        <h5>{{ __('Shipping Address') }}</h5>
                                        <dl class="row mt-4 align-items-center">
                                            <dt class="col-sm-6"><span
                                                    class="h6 text-sm mb-0">{{ __('Shipping Address') }}</span></dt>
                                            <dd class="col-sm-6"><span
                                                    class="text-sm">{{ $quote->shipping_address }}</span></dd>

                                            <dt class="col-sm-6"><span
                                                    class="h6 text-sm mb-0">{{ __('Shipping City') }}</span></dt>
                                            <dd class="col-sm-6"><span class="text-sm">{{ $quote->shipping_city }}</span>
                                            </dd>

                                            <dt class="col-sm-6"><span
                                                    class="h6 text-sm mb-0">{{ __('Zip Code') }}</span></dt>
                                            <dd class="col-sm-6"><span
                                                    class="text-sm">{{ $quote->shipping_postalcode }}</span></dd>

                                            <dt class="col-sm-6"><span
                                                    class="h6 text-sm mb-0">{{ __('Shipping Country') }}</span></dt>
                                            <dd class="col-sm-6"><span
                                                    class="text-sm">{{ $quote->shipping_country }}</span></dd>

                                            <dt class="col-sm-6"><span
                                                    class="h6 text-sm mb-0">{{ __('Shipping Contact') }}</span></dt>
                                            <dd class="col-sm-6"><span
                                                    class="text-sm">{{ !empty($quote->contacts->name) ? $quote->contacts->name : '--' }}</span>
                                            </dd>
                                        </dl>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Invoice ] end -->
        <div class="col-sm-2">
            <div class="card">
                <div class="card-footer py-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Assigned User') }}</span></dt>
                                <dd class="col-sm-12"><span
                                        class="text-sm">{{ !empty($quote->assign_user) ? $quote->assign_user->name : '' }}</span>
                                </dd>

                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Created') }}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{ $quote->created_at }}</span></dd>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script-page')
<script src="{{ asset('js/custom.js') }}"></script>

    <script>
        document.querySelector('.btn-print-invoice').addEventListener('click', function() {
            var link2 = document.createElement('link');
            link2.innerHTML =
                '<style>@media print{*,::after,::before{text-shadow:none!important;box-shadow:none!important}a:not(.btn){text-decoration:none}abbr[title]::after{content:" ("attr(title) ")"}pre{white-space:pre-wrap!important}blockquote,pre{border:1px solid #adb5bd;page-break-inside:avoid}thead{display:table-header-group}img,tr{page-break-inside:avoid}table,thead,tr,td{background:transparent}h2,h3,p{orphans:3;widows:3}h2,h3{page-break-after:avoid}@page{size:a3}body{min-width:992px!important}.container{min-width:992px!important}.page-header,.pc-sidebar,.pc-mob-header,.pc-header,.pct-customizer,.modal,.navbar{display:none}.pc-container{top:0;}.invoice-contact{padding-top:0;}@page,.card-body,.card-header,body,.pcoded-content{padding:0;margin:0}.badge{border:1px solid #000}.table{border-collapse:collapse!important}.table td,.table th{background-color:#fff!important}.table-bordered td,.table-bordered th{border:1px solid #dee2e6!important}.table-dark{color:inherit}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#dee2e6}.table .thead-dark th{color:inherit;border-color:#dee2e6}}</style>';

            document.getElementsByTagName('head')[0].appendChild(link2);
            window.print();
        })
    </script>
    <script>
        $(document).on('change', 'select[name=item]', function() {
            var item_id = $(this).val();
            $.ajax({
                url: '{{ route('quote.items') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'item_id': item_id,
                },
                cache: false,
                success: function(data) {
                    var invoiceItems = JSON.parse(data);
                    $('.taxId').val('');
                    $('.tax').html('');

                    $('.price').val(invoiceItems.price);
                    $('.quantity').val(1);
                    $('.discount').val(0);
                    var taxes = '';
                    var tax = [];

                    for (var i = 0; i < invoiceItems.taxes.length; i++) {
                        taxes += '<span class="badge bg-primary ms-1 mt-1 ">' + invoiceItems.taxes[i]
                            .tax_name + ' ' + '(' + invoiceItems.taxes[i].rate + '%)' + '</span>';
                    }
                    $('.taxId').val(invoiceItems.tax);
                    $('.tax').html(taxes);
                }
            });
        });
        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
            show_toastr('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    </script>
@endpush
