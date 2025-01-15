 @extends('layouts.admin')
 @section('page-title')
     {{ __('Invoice') }}
 @endsection
 @section('title')
     {{ __('Invoice') }}
 @endsection
 @section('breadcrumb')
     <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
     <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a></li>
     <li class="breadcrumb-item">{{ __('Show') }}</li>
 @endsection
 @php
     $total_storage = $user->storage_limit;
     $settings = \App\Models\Utility::settings();
 @endphp
 @section('action-btn')
     <div class="action-btn bg-warning ms-2">
         <a href="{{ route('invoice.pdf', \Crypt::encrypt($invoice->id)) }}" target="_blank"
             class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" title="{{ __('Print') }}">
             <span class="btn-inner--icon text-white"><i class="ti ti-printer"></i></span>
         </a>
     </div>

     @if (Auth::user()->type == 'owner')
         <div class="action-btn bg-warning ms-2">
             <a href="#" class="btn btn-sm btn-warning btn-icon m-1 cp_link"
                 data-link="{{ route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)) }}"
                 data-bs-toggle="tooltip" data-title="{{ __('Click to copy invoice link') }}"
                 title="{{ __('Copy invoice') }}"><span class="btn-inner--icon text-white"><i
                         class="ti ti-file"></i></span></a>
         </div>
     @endif
     <div class="action-btn bg-success ms-2">
         <a href="#" data-size="md" data-url="{{ route('invoice.link', $invoice->id) }}" data-ajax-popup="true"
             data-bs-toggle="tooltip" data-title="{{ __('Send Invoice Link') }}" title="{{ __('Send Link') }}"
             class="btn btn-sm btn-secondary btn-icon m-1">
             <i class="ti ti-brand-telegram"></i>
         </a>
     </div>
     @can('Edit Invoice')
         <div class="action-btn ms-2">
             <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-sm btn-info btn-icon m-1"
                 data-bs-toggle="tooltip" data-title="{{ __('invoice Edit') }}" title="{{ __('Edit') }}"><i
                     class="ti ti-pencil"></i>
             </a>
         </div>
     @endcan
     <div class="action-btn ms-2">
         <a href="#" data-size="md" data-url="{{ route('invoice.invoiceitem', $invoice->id) }}"
             data-ajax-popup="true" data-title="{{ __('Create New Invoice Item') }}" title="{{ __('Create') }}"
             data-bs-toggle="tooltip" class="btn btn-sm btn-primary btn-icon m-1">
             <i class="ti ti-plus"></i>
         </a>
     </div>
 @endsection
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
                                     <div class="col-lg-6 col-md-8">
                                         <h6 class="d-inline-block m-0 d-print-none">{{ __('Invoice ID') }}</h6>
                                         <span class="col-sm-8"><span
                                                 class="text-sm">{{ \Auth::user()->invoiceNumberFormat($invoice->id) }}</span></span>
                                     </div>
                                     <div class="col-lg-6 col-md-8 mt-3">
                                         <h6 class="d-inline-block m-0 d-print-none">{{ __('Invoice Date') }}</h6>
                                         <span class="col-sm-8"><span
                                                 class="text-sm">{{ \Auth::user()->dateFormat($invoice->created_at) }}</span></span>
                                     </div>
                                     <h6 class="d-inline-block m-0 d-print-none mt-3">{{ __('Invoice') }}</h6>
                                     @if ($invoice->status == 0)
                                         <span
                                             class="badge bg-primary p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                     @elseif($invoice->status == 1)
                                         <span
                                             class="badge bg-danger p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                     @elseif($invoice->status == 2)
                                         <span
                                             class="badge bg-warning p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                     @elseif($invoice->status == 3)
                                         <span
                                             class="badge bg-success p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                     @elseif($invoice->status == 4)
                                         <span
                                             class="badge bg-info p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                     @endif
                                 </div>
                                 <div class="col-sm-6 text-sm-end">
                                     <div>
                                         <div class="float-end mt-3">
                                            @if($settings['is_qr_enabled']=='on')
                                             {!! DNS2D::getBarcodeHTML(
                                                 route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
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
                                                 @foreach ($invoice->items as $key => $invoiceitem)
                                                     @php
                                                         $taxes = \Utility::tax($invoiceitem->tax);
                                                         $totalQuantity += $invoiceitem->quantity;
                                                         $totalRate += $invoiceitem->price;
                                                         $totalDiscount += $invoiceitem->discount;
                                                         if (!empty($taxes[0])) {
                                                             foreach ($taxes as $taxe) {
                                                                 $taxDataPrice = \Utility::taxRate($taxe->rate, $invoiceitem->price, $invoiceitem->quantity);
                                                                 if (array_key_exists($taxe->tax_name, $taxesData)) {
                                                                     $taxesData[$taxe->tax_name] = $taxesData[$taxe->tax_name] + $taxDataPrice;
                                                                 } else {
                                                                     $taxesData[$taxe->tax_name] = $taxDataPrice;
                                                                 }
                                                             }
                                                         }
                                                     @endphp
                                                     <tr>
                                                         <td>{{ $invoiceitem->items->name ?? '' }} </td>
                                                         <td>{{ $invoiceitem->quantity }} </td>
                                                         <td>{{ \Auth::user()->priceFormat($invoiceitem->price) }} </td>
                                                         <td>
                                                             <div class="col">
                                                                    @php
                                                                        $totalTaxPrice = 0;
                                                                        $data = 0;
                                                                    @endphp
                                                                 @if (!empty($invoiceitem->tax))
                                                                     @foreach ($invoiceitem->tax($invoiceitem->tax) as $tax)
                                                                         @php
                                                                             $taxPrice = \Utility::taxRate($tax->rate, $invoiceitem->price, $invoiceitem->quantity);
                                                                             $totalTaxPrice += $taxPrice;
                                                                            $data+=$taxPrice;
                                                                         @endphp
                                                                         <a href="#!"
                                                                             class="d-block text-sm text-muted">{{ $tax->tax_name . ' (' . $tax->rate . '%)' }}
                                                                             &nbsp;&nbsp;{{ \Auth::user()->priceFormat($taxPrice) }}</a>
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
                                                         <td>{{ \Auth::user()->priceFormat($invoiceitem->discount) }} </td>
                                                         <td>{{ $invoiceitem->description }} </td>
                                                         <td class="text-right">
                                                            @php
                                                            $tr_tex = (array_key_exists($key,$TaxPrice_array) == true) ? $TaxPrice_array[$key] : 0;
                                                        @endphp
                                                             {{ \Auth::user()->priceFormat($invoiceitem->price * $invoiceitem->quantity- $invoiceitem->discount + $tr_tex) }}
                                                         </td>
                                                         <td class="text-right">
                                                             @can('Edit Invoice')
                                                                 <div class="action-btn me-2">
                                                                     <a href="#"
                                                                         data-url="{{ route('invoice.item.edit', $invoiceitem->id) }}"
                                                                         data-ajax-popup="true"
                                                                         class="mx-3 btn btn-sm bg-infoalign-items-center text-white "
                                                                         data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                         data-title="{{ __('Edit Item') }}"><i
                                                                             class="ti ti-pencil"></i></a>
                                                                 </div>
                                                             @endcan
                                                             @can('Delete Invoice')
                                                                 <div class="action-btn">
                                                                     {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.items.delete', $invoiceitem->id]]) !!}
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
                                                             $totalQuantity += $invoiceitem->quantity;
                                                             $totalRate += $invoiceitem->price;
                                                             $totalDiscount += $invoiceitem->discount;
                                                             $totalAmount += $invoiceitem->price * $invoiceitem->quantity;
                                                         @endphp
                                                     </tr>
                                                 @endforeach
                                             </tbody>
                                         </table>
                                     </div>
                                 </div>
                             </div>

                             <div class="row">
                                 <div class="col-sm-12">
                                     <div class="invoice-total">
                                         <table class="table invoice-table ">
                                             <tbody>
                                                 <tr>
                                                     <th>Sub Total :</th>
                                                     <td>{{ \Auth::user()->priceFormat($invoice->getSubTotal()) }}</td>
                                                 </tr>
                                                 {{-- <tr>
                                                @if (!empty($taxesData))
                                                    @foreach ($taxesData as $taxName => $taxPrice)
                                                        @if ($taxName != 'No Tax')
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td class="px-0"></td>
                                                                <td class="text-right"><b>{{$taxName}}</b></td>
                                                                <td class="text-right">{{ \Auth::user()->priceFormat($taxPrice) }}</td>
                                                                <td></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tr> --}}
                                            @if (!empty($taxesData))
                                                    @foreach ($taxesData as $key => $item)
                                                        <tr>
                                                            <th>{{ $key }}</th>
                                                            <td>{{ \Auth::user()->priceFormat($item) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                 <tr>
                                                     <th>Discount :</th>
                                                     <td>{{ \Auth::user()->priceFormat($invoice->getTotalDiscount()) }}
                                                     </td>
                                                 </tr>
                                                 <tr>
                                                     <td>
                                                         <hr />
                                                         <h5 class="text-primary m-r-10">Total :</h5>
                                                     </td>

                                                     <td>
                                                         <hr />
                                                         <h5 class="text-primary subTotal">
                                                             {{ \Auth::user()->priceFormat($invoice->getTotal()) }}</h5>
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
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ $invoice->billing_address }}</span></dd>

                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Billing City') }}</span></dt>
                                         <dd class="col-sm-6"><span class="text-sm">{{ $invoice->billing_city }}</span>
                                         </dd>

                                         <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{ __('Zip Code') }}</span>
                                         </dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ $invoice->billing_postalcode }}</span></dd>

                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Billing Country') }}</span></dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ $invoice->billing_country }}</span></dd>


                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Billing Contact') }}</span></dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ !empty($invoice->contacts->name) ? $invoice->contacts->name : '--' }}</span>
                                         </dd>
                                     </dl>
                                 </div>
                                 <div class="col-12 col-md-4">
                                     <h5>{{ __('Shipping Address') }}</h5>
                                     <dl class="row mt-4 align-items-center">
                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Shipping Address') }}</span></dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ $invoice->shipping_address }}</span></dd>

                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Shipping City') }}</span></dt>
                                         <dd class="col-sm-6"><span class="text-sm">{{ $invoice->shipping_city }}</span>
                                         </dd>

                                         <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{ __('Zip Code') }}</span>
                                         </dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ $invoice->shipping_postalcode }}</span></dd>

                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Shipping Country') }}</span></dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ $invoice->shipping_country }}</span></dd>

                                         <dt class="col-sm-6"><span
                                                 class="h6 text-sm mb-0">{{ __('Shipping Contact') }}</span></dt>
                                         <dd class="col-sm-6"><span
                                                 class="text-sm">{{ !empty($invoice->contacts->name) ? $invoice->contacts->name : '--' }}</span>
                                         </dd>
                                     </dl>
                                 </div>
                             </div>



                             <div class="row mt-4">
                                 <div class="col-sm-12">
                                     <h3 class="px-2 py-2"><b>{{ __('Payment History') }}</b></h3>
                                     <div class="table-responsive mt-3">
                                         <table class="table invoice-detail-table">
                                             <thead>
                                                 <tr class="thead-default">
                                                     <th>{{ __('Transaction ID') }}</th>
                                                     <th>{{ __('Payment Date') }}</th>
                                                     {{-- <th>{{__('Payment Method')}}</th> --}}
                                                     <th>{{ __('Payment Type') }}</th>
                                                     <th>{{ __('Note') }}</th>
                                                     <th>{{ __('Receipt') }}</th>
                                                     <th class="text-right">{{ __('Amount') }}</th>
                                                     <th>{{ __('Action') }}</th>
                                                 </tr>
                                             </thead>
                                             <tbody>
                                                 @php $i=0; @endphp
                                                 @if ($invoice->payments->count() || $invoicepayments->count())

                                                 @foreach ($invoice->payments as $payment)
                                                 {{-- @if ($payment->payment_type != 'Bank transfer') --}}
                                                             <tr>
                                                                 <td>{{ sprintf('%05d', $payment->transaction_id) }}</td>
                                                                 <td>{{ Auth::user()->dateFormat($payment->date) }}</td>
                                                                 {{-- <td>{{(!empty($payment->payment)?$payment->payment->name:'-')}}</td> --}}
                                                                 <td>{{ $payment->payment_type }}</td>
                                                                 <td>{{ !empty($payment->notes) ? $payment->notes : '-' }}
                                                                 </td>
                                                                 <td>
                                                                    @if($payment->payment_type == 'Bank transfer' )
                                                                    <a href="{{ \App\Models\Utility::get_file($payment->receipt) }}"
                                                                    class="btn  btn-outline-primary" target="_blank"><i
                                                                        class="fas fa-file-invoice"></i>
                                                                    {{ __('Receipt') }}</a>
                                                                    @else
                                                                    -
                                                                @endif
                                                                 </td>
                                                                 <td class="text-right">
                                                                     {{ \Auth::user()->priceFormat($payment->amount) }}
                                                                 </td>
                                                                 <td>
                                                                     <div class="action-btn">
                                                                         {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.payment.delete', $payment->id]]) !!}
                                                                         <a href="#!"
                                                                             class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
                                                                             data-bs-toggle="tooltip" title='Delete'>
                                                                             <i class="ti ti-trash"></i>
                                                                         </a>
                                                                         {!! Form::close() !!}
                                                                     </div>
                                                                 </td>
                                                                 {{-- @endif --}}
                                                         @endforeach
                                                     @foreach ($invoicepayments as $invoicepayment)
                                                         <tr>
                                                             <td>{{ sprintf('%05d', $invoicepayment->order_id) }}</td>
                                                             <td>{{ Auth::user()->dateFormat($invoicepayment->date) }}
                                                             </td>
                                                             {{-- <td>{{(!empty($payment->payment)?$payment->payment->name:'-')}}</td> --}}
                                                             <td>{{ 'Bank Transfer' }}</td>
                                                             <td>{{ !empty($invoicepayment->notes) ? $invoicepayment->notes : '-' }}
                                                             </td>
                                                             <td>
                                                                @if(($plan->storage_limit <= $total_storage && $plan->storage_limit != -1))
                                                                -
                                                                    @else
                                                                    <a href="{{ \App\Models\Utility::get_file($invoicepayment->receipt) }}"
                                                                        class="btn  btn-outline-primary" target="_blank"><i
                                                                            class="fas fa-file-invoice"></i>
                                                                        {{ __('Receipt') }}</a>
                                                                    @endif
                                                             </td>

                                                             <td class="text-right">
                                                                 {{ \Auth::user()->priceFormat($invoicepayment->amount) }}
                                                             </td>
                                                             <td>

                                                                 @if ($invoicepayment->status == 'Pending')
                                                                     <div class="action-btn  me-2">
                                                                         <a href="#" data-size="lg"
                                                                             data-url="{{ route('bankpayment.show', $invoicepayment->id) }}"
                                                                             data-bs-toggle="tooltip"
                                                                             title="{{ __('Details') }}"
                                                                             data-ajax-popup="true"
                                                                             data-title="{{ __('Payment Status') }}"
                                                                             class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                                             <i class="ti ti-caret-right text-white"></i>
                                                                         </a>
                                                                     </div>
                                                                 @endif

                                                                 <div class="action-btn ">
                                                                     {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.bankpayment.delete', $invoicepayment->id]]) !!}
                                                                     <a href="#!"
                                                                         class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
                                                                         data-bs-toggle="tooltip" title='Delete'>
                                                                         <i class="ti ti-trash"></i>
                                                                     </a>
                                                                     {!! Form::close() !!}
                                                                 </div>
                                                             </td>
                                                     @endforeach
                                                 @endif
                                             </tbody>
                                         </table>
                                     </div>
                                 </div>
                             </div>

                         </div>
                     </div>
                 </div>
             </div>
         </div>


         <div class="col-sm-2">
             <div class="card">
                 <div class="card-footer py-0">
                     <ul class="list-group list-group-flush">
                         <li class="list-group-item px-0">
                             <div class="row align-items-center">
                                 <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Assigned User') }}</span>
                                 </dt>
                                 <dd class="col-sm-12"><span
                                         class="text-sm">{{ !empty($invoice->assign_user) ? $invoice->assign_user->name : '' }}</span>
                                 </dd>

                                 <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Created') }}</span></dt>
                                 <dd class="col-sm-12"><span
                                         class="text-sm">{{ \Auth::user()->dateFormat($invoice->created_at) }}</span>
                                 </dd>
                             </div>
                         </li>
                     </ul>
                 </div>
             </div>
         </div>

         <!-- [ Invoice ] end -->
     </div>
 @endsection
 @push('script-page')
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
                 url: '{{ route('invoice.items') }}',
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
                     $('.price').val(invoiceItems.price);
                     $('.quantity').val(1);
                     $('.discount').val(0);
                     var taxes = '';
                     var tax = [];
                     for (var i = 0; i < invoiceItems.taxes.length; i++) {
                         taxes += '<span class="badge bg-primary ms-1 mt-1">' + invoiceItems.taxes[i]
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
