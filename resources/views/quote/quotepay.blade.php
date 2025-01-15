@extends('layouts.invoicepayheader')
@php
    $setting = Utility::settings();
    $settings = Utility::settingsById($quote->created_by);

@endphp
@section('page-title')
    {{__('Quote')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Quote')}} {{ '('. $quote->name .')' }}</h5>
    </div>
@endsection

@section('action-btn')
    <a href="{{route('quote.pdf',\Crypt::encrypt($quote->id))}}" target="_blank" class="btn btn-sm btn-primary btn-icon ">
        <span class="btn-inner--icon text-white"><i class="fa fa-print"></i></span>
        <span class="btn-inner--text text-white">{{__('Print')}}</span>
    </a>

@endsection
@section('content')
    <div class="row">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        <div class="col-12">
                            <div class="row align-items-center mb-5">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Quote')}}</h6>

                                    @if($quote->status == 0)
                                        <span class="badge bg-primary p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 1)
                                        <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 2)
                                        <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 3)
                                        <span class="badge bg-success p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @elseif($quote->status == 4)
                                        <span class="badge bg-info p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                    @endif
                                </div>
                                <div class="col-md-2  qr_code">
                                    <div class="text-end" style="margin: 26px 0px 0px 0px;">
                                        @if ($settings['quote_qr_enabled'] == 'on')

                                        {!! DNS2D::getBarcodeHTML(route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($quote->id)), "QRCODE",2,2) !!}
                                    @else
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Quote ID')}}</h6>
                                    <span class="col-sm-8"><span class="text-sm">{{ $users->quoteNumberFormat($quote->id) }}</span></span>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{__('Quote Date')}}</h6>
                                    <span class="col-sm-8"><span class="text-sm">{{$users->dateFormat($quote->date_quoted)}}</span></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h5>{{__('Item List')}}</h5>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th>{{__('Item')}}</th>
                                                <th>{{__('Quantity')}}</th>
                                                <th>{{__('Price')}}</th>
                                                <th>{{__('Tax')}}</th>
                                                <th>{{__('Discount')}}</th>
                                                <th>{{__('Description')}}</th>
                                                <th class="text-end" >{{__('Price')}}</th>

                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $totalQuantity=0;
                                                $totalRate=0;
                                                $totalAmount=0;
                                                $totalTaxPrice=0;
                                                $totalDiscount=0;
                                                $taxesData=[];
                                            @endphp
                                            @foreach($quote->items as $quoteitem)
                                                @php
                                                    $taxes=\Utility::tax($quoteitem->tax);
                                                    $totalQuantity+=$quoteitem->quantity;
                                                    $totalRate+=$quoteitem->price;
                                                    $totalDiscount+=$quoteitem->discount;
                                                    if(!empty($taxes[0]))
                                                    {
                                                        foreach($taxes as $taxe)
                                                        {
                                                            $taxDataPrice=\Utility::taxRate($taxe->rate,$quoteitem->price,$quoteitem->quantity);
                                                            if (array_key_exists($taxe->tax_name,$taxesData))
                                                            {
                                                                $taxesData[$taxe->tax_name] = $taxesData[$taxe->tax_name]+$taxDataPrice;
                                                            }
                                                            else
                                                            {
                                                                $taxesData[$taxe->tax_name] = $taxDataPrice;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{$quoteitem->items->name}} </td>
                                                    <td>{{$quoteitem->quantity}} </td>
                                                    <td>{{$users->priceFormat($quoteitem->price)}} </td>
                                                    <td>
                                                        <div class="col">
                                                            @if(!empty($quoteitem->tax))
                                                                @foreach($quoteitem->tax($quoteitem->tax) as $tax)
                                                                    @php
                                                                        $taxPrice=\Utility::taxRate($tax->rate,$quoteitem->price,$quoteitem->quantity);
                                                                        $totalTaxPrice+=$taxPrice;
                                                                    @endphp
                                                                    <a href="#!" class="d-block text-sm text-muted">{{$tax->tax_name .' ('.$tax->rate .'%)'}} &nbsp;&nbsp;{{$users->priceFormat($taxPrice)}}</a>
                                                                @endforeach
                                                            @else
                                                                <a href="#!" class="d-block text-sm text-muted">{{__('No Tax')}}</a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{$users->priceFormat($quoteitem->discount)}} </td>
                                                    <td>{{$quoteitem->description}} </td>
                                                    <td class="text-"> {{$users->priceFormat($quoteitem->price*$quoteitem->quantity)}}</td>

                                                    @php
                                                        $totalQuantity+=$quoteitem->quantity;
                                                        $totalRate+=$quoteitem->price;
                                                        $totalDiscount+=$quoteitem->discount;
                                                        $totalAmount+=($quoteitem->price*$quoteitem->quantity);
                                                    @endphp
                                                </tr>
                                            @endforeach
                                            <tfoot>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td></td>
                                                <td class="text-"><strong>{{__('Sub Total')}}</strong></td>
                                                <td class="text- subTotal">{{$users->priceFormat($quote->getSubTotal())}}</td>

                                            </tr>

                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td></td>
                                                <td class="text-"><strong>{{__('Discount')}}</strong></td>
                                                <td class="text- subTotal">{{$users->priceFormat($quote->getTotalDiscount())}}</td>

                                            </tr>
                                            @if(!empty($taxesData))
                                                @foreach($taxesData as $taxName => $taxPrice)
                                                    @if($taxName != 'No Tax')
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td></td>
                                                            <td class="text-"><b>{{$taxName}}</b></td>
                                                            <td class="text-">{{ $users->priceFormat($taxPrice) }}</td>

                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                                <td></td>
                                                <td class="text-"><strong>{{__('Total')}}</strong></td>
                                                <td class="text- subTotal">{{$users->priceFormat( $quote->getTotal())}}</td>

                                            </tr>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card my-5 bg-secondary">
                                        <div class="card-body">
                                            <div class="row justify-content-between align-items-center">
                                                <div class="col-md-6 order-md-2 mb-4 mb-md-0">
                                                    <div class="d-flex align-items-center justify-content-md-end">
                                                        <span class="h6 text-muted d-inline-block mr-3 mb-0">{{__('Total value')}}:</span>
                                                        <span class="h4 mb-0">{{$users->priceFormat($quote->getTotal())}}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 order-md-1">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <h5>{{__('From')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Company Address')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $company_setting['company_address'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Company City')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $company_setting['company_city'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Zip Code')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $company_setting['company_zipcode'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Company Country')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $company_setting['company_country'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Company Contact')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $company_setting['company_telephone']}}</span></dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h5>{{__('Billing Address')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Billing Address')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_address }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Billing City')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_city }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Zip Code') }}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_postalcode }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Billing Country')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->billing_country }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Billing Contact')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ !empty($quote->contacts->name)?$quote->contacts->name:'--'}}</span></dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h5>{{__('Shipping Address')}}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Shipping Address')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->shipping_address }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Shipping City')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->shipping_city }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Zip Code')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->shipping_postalcode }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Shipping Country')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ $quote->shipping_country }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{__('Shipping Contact')}}</span></dt>
                                        <dd class="col-sm-6"><span class="text-sm">{{ !empty($quote->contacts->name)?$quote->contacts->name:'--'}}</span></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="card mt-2">
                <div class="card-footer py-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="row align-items-center">
                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{ !empty($quote->assign_user)?$quote->assign_user->name:''}}</span></dd>

                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                                <dd class="col-sm-12"><span class="text-sm">{{$users->dateFormat($quote->created_at)}}</span></dd>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    @if ($setting['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
@endsection
