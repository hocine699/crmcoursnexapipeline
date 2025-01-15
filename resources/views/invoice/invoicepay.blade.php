@extends('layouts.invoicepayheader')
@php
    $setting = Utility::settings();
    $settings = Utility::settingsById($invoice->created_by);

    $total_storage = $users->storage_limit;
@endphp
@section('page-title')
    {{ __('Invoice') }}
@endsection
@section('title')
    {{ __('Invoice') }} {{ '(' . $invoice->name . ')' }}
@endsection

@section('action-btn')
    <a href="{{ route('invoice.pdf', \Crypt::encrypt($invoice->id)) }}" target="_blank" class="btn btn-sm btn-primary btn-icon"
        data-bs-toggle="tooltip" title="{{ __('Print') }}">
        <span class="btn-inner--icon text-white"><i class="ti ti-printer"></i>{{ __('Print') }}</span>
    </a>

    @if ($invoice->getDue() > 0)
        {{-- @if ($invoice->getDue() > 0 && count($payment_setting) > 0) --}}
        <a href="#" data-bs-toggle="modal" data-bs-target="#paymentModal" class="btn btn-sm btn-primary">
            <span class="btn-inner--icon text-white"><i class="ti ti-credit-card"></i></span>
            <span class="btn-inner--text text-white">{{ __(' Pay Now') }}</span>
        </a>
    @endif
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
                                    <h6 class="d-inline-block m-0 d-print-none">{{ __('Invoice') }}</h6>

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
                                <div class="col-md-2  qr_code">
                                    @if ($settings['is_qr_enabled'] == 'on')
                                    <div class="text-end" style="margin: 26px 0px 0px 0px;">
                                            {!! DNS2D::getBarcodeHTML(
                                                route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)),
                                                'QRCODE',
                                                2,
                                                2,
                                            ) !!}
                                    </div>
                                    @else
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{ __('Invoice ID') }}</h6>
                                    <span class="col-sm-8"><span
                                            class="text-sm">{{ $users->invoiceNumberFormat($invoice->id) }}</span></span>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-lg-6 col-md-8">
                                    <h6 class="d-inline-block m-0 d-print-none">{{ __('Invoice Date') }}</h6>
                                    <span class="col-sm-8"><span
                                            class="text-sm">{{ $users->dateFormat($invoice->created_at) }}</span></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 ">
                                    <h5 class="px-2 py-2"><b>{{ __('Item List') }}</b></h5>
                                    <div class="table-responsive mt-4">
                                        <table class="table invoice-detail-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Item') }}</th>
                                                    <th>{{ __('Quantity') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('Tax') }}</th>
                                                    <th>{{ __('Discount') }}</th>
                                                    <th>{{ __('Description') }}</th>
                                                    <th class="text-end">{{ __('Price') }}</th>

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
                                                @endphp

                                                @foreach ($invoice->items as $invoiceitem)
                                                    @php
                                                        $taxes = \Utility::tax($invoiceitem->tax);
                                                        $totalQuantity += $invoiceitem->quantity;
                                                        $totalRate += $invoiceitem->price;
                                                        $totalDiscount += $invoiceitem->discount;
                                                        if (!empty($taxes[0])) {
                                                            foreach ($taxes as $taxe) {
                                                                $taxDataPrice = \Utility::taxRate(
                                                                    $taxe->rate,
                                                                    $invoiceitem->price,
                                                                    $invoiceitem->quantity,
                                                                );
                                                                if (array_key_exists($taxe->tax_name, $taxesData)) {
                                                                    $taxesData[$taxe->tax_name] =
                                                                        $taxesData[$taxe->tax_name] + $taxDataPrice;
                                                                } else {
                                                                    $taxesData[$taxe->tax_name] = $taxDataPrice;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $invoiceitem->items->name }} </td>
                                                        <td>{{ $invoiceitem->quantity }} </td>
                                                        <td>{{ $users->priceFormat($invoiceitem->price) }} </td>
                                                        <td>
                                                            <div class="col">
                                                                @if (!empty($invoiceitem->tax))
                                                                    @foreach ($invoiceitem->tax($invoiceitem->tax) as $tax)
                                                                        @php
                                                                            $taxPrice = \Utility::taxRate(
                                                                                $tax->rate,
                                                                                $invoiceitem->price,
                                                                                $invoiceitem->quantity,
                                                                            );
                                                                            $totalTaxPrice += $taxPrice;
                                                                        @endphp
                                                                        <a href="#!"
                                                                            class="d-block text-sm text-muted">{{ $tax->tax_name . ' (' . $tax->rate . '%)' }}
                                                                            &nbsp;&nbsp;{{ $users->priceFormat($taxPrice) }}</a>
                                                                    @endforeach
                                                                @else
                                                                    <a href="#!"
                                                                        class="d-block text-sm text-muted">{{ __('No Tax') }}</a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>{{ $users->priceFormat($invoiceitem->discount) }} </td>
                                                        <td>{{ $invoiceitem->description }} </td>
                                                        <td class="text-end">
                                                            {{ $users->priceFormat($invoiceitem->price * $invoiceitem->quantity) }}
                                                        </td>

                                                        @php
                                                            $totalQuantity += $invoiceitem->quantity;
                                                            $totalRate += $invoiceitem->price;
                                                            $totalDiscount += $invoiceitem->discount;
                                                            $totalAmount +=
                                                                $invoiceitem->price * $invoiceitem->quantity;
                                                        @endphp
                                                    </tr>
                                                @endforeach
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4">&nbsp;</td>
                                                    <td></td>
                                                    <td class="text-end"><strong>{{ __('Sub Total') }}</strong></td>
                                                    <td class="text-end subTotal">
                                                        {{ $users->priceFormat($invoice->getSubTotal()) }}</td>

                                                </tr>

                                                <tr>
                                                    <td colspan="4">&nbsp;</td>
                                                    <td></td>
                                                    <td class="text-end"><strong>{{ __('Discount') }}</strong></td>
                                                    <td class="text-end subTotal">
                                                        {{ $users->priceFormat($invoice->getTotalDiscount()) }}</td>

                                                </tr>
                                                @if (!empty($taxesData))
                                                    @foreach ($taxesData as $taxName => $taxPrice)
                                                        @if ($taxName != 'No Tax')
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td></td>
                                                                <td class="text-end"><b>{{ $taxName }}</b></td>
                                                                <td class="text-end">{{ $users->priceFormat($taxPrice) }}
                                                                </td>

                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="4">&nbsp;</td>
                                                    <td></td>
                                                    <td class="text-end"><strong>{{ __('Total') }}</strong></td>
                                                    <td class="text-end subTotal">
                                                        {{ $users->priceFormat($invoice->getTotal()) }}</td>

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
                                                        <span
                                                            class="h6 text-muted d-inline-block mr-3 mb-0">{{ __('Total value') }}:</span>
                                                        <span
                                                            class="h4 mb-0">{{ $users->priceFormat($invoice->getTotal()) }}</span>
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
                                    <h5>{{ __('From') }}</h5>
                                    <dl class="row mt-4 align-items-center">
                                        <dt class="col-sm-6"><span
                                                class="h6 text-sm mb-0">{{ __('Company Address') }}</span></dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_address'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{ __('Company City') }}</span>
                                        </dt>
                                        <dd class="col-sm-6"><span
                                                class="text-sm">{{ $company_setting['company_city'] }}</span></dd>

                                        <dt class="col-sm-6"><span class="h6 text-sm mb-0">{{ __('Zip Code') }}</span></dt>
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
                                        <dd class="col-sm-6"><span class="text-sm">{{ $invoice->billing_address }}</span>
                                        </dd>

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
                                        <dd class="col-sm-6"><span class="text-sm">{{ $invoice->billing_country }}</span>
                                        </dd>

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
                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Assigned User') }}</span></dt>
                                <dd class="col-sm-12"><span
                                        class="text-sm">{{ !empty($invoice->assign_user) ? $invoice->assign_user->name : '' }}</span>
                                </dd>

                                <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Created') }}</span></dt>
                                <dd class="col-sm-12"><span
                                        class="text-sm">{{ $users->dateFormat($invoice->created_at) }}</span></dd>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @if ($plan->storage_limit <= $total_storage && $plan->storage_limit != -1)
            <small
                class="text-danger">{{ __('Note : Your plan storage limit is over , so you can not see customer uploaded payment receipt.') }}</small>
        @endif
        <div class="col-lg-10">
            <div class="row">
                <div class="col-sm-12">
                    <h5 class="px-2 py-2"><b>{{ __('Payments History') }}</b></h5>
                    <div class="table-responsive mt-3">
                        <table class="table invoice-detail-table">
                            <thead>
                                <tr class="thead-default">
                                    <th>{{ __('Transaction Id ') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    {{-- <th>{{ __('Payment Method') }}</th> --}}
                                    <th>{{ __('Payment Type') }}</th>
                                    <th>{{ __('Note') }}</th>
                                    <th>{{ __('Recipt') }}</th>
                                    <th class="text-right">{{ __('Amount') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach ($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ sprintf('%05d', $payment->transaction_id) }}</td>
                                        <td>{{ $users->dateFormat($payment->date) }}</td>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ !empty($payment->notes) ? $payment->notes : '-' }}</td>
                                        <td>
                                            @if ($payment->payment_type == 'Bank transfer')
                                                <a href="{{ \App\Models\Utility::get_file($payment->receipt) }}"
                                                    class="btn  btn-outline-primary" target="_blank"><i
                                                        class="fas fa-file-invoice"></i>
                                                    {{ __('Receipt') }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $users->priceFormat($payment->amount) }}</td>
                                @endforeach

                                @foreach ($bankPayments as $bankPayment)
                                    <tr>
                                        <td>{{ sprintf('%05d', $bankPayment->transaction_id) }}</td>
                                        <td>{{ $users->dateFormat($bankPayment->date) }}</td>
                                        <td>{{ 'Bank Transfer' }}</td>
                                        <td>{{ !empty($bankPayment->notes) ? $bankPayment->notes : '-' }}</td>
                                        <td>

                                            @if ($plan->storage_limit <= $total_storage && $plan->storage_limit != -1)
                                                -
                                            @else
                                                <a href="{{ \App\Models\Utility::get_file($bankPayment->receipt) }}"
                                                    class="btn  btn-outline-primary" target="_blank"><i
                                                        class="fas fa-file-invoice"></i>
                                                    {{ __('Receipt') }}</a>
                                            @endif
                                        </td>

                                        <td class="text-right">
                                            {{ $users->priceFormat($bankPayment->amount) }}</td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($invoice->getDue() > 0)
        <div class="modal fade bd-example-modal-lg" id="paymentModal" tabindex="-1" data-backdrop="true"
            role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row pb-3 px-2">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                @if (isset($payment_setting['is_bank_enabled']) && $payment_setting['is_bank_enabled'] == 'on')
                                    @if (isset($payment_setting['bank_details']))
                                        <li class="nav-item">
                                            <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                                href="#bank-payment" role="tab" aria-controls="pills-home"
                                                aria-selected="true">{{ __('Bank Transfer') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on')
                                    @if (isset($payment_setting['stripe_key']) &&
                                            !empty($payment_setting['stripe_key']) &&
                                            (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-home-tab" data-bs-toggle="pill"
                                                href="#stripe-payment" role="tab" aria-controls="pills-home"
                                                aria-selected="true">{{ __('Stripe') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_paypal_enabled']) && $payment_setting['is_paypal_enabled'] == 'on')
                                    @if (isset($payment_setting['paypal_client_id']) &&
                                            !empty($payment_setting['paypal_client_id']) &&
                                            (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paypal-tab" data-bs-toggle="pill"
                                                href="#paypal-payment" role="tab" aria-controls="paypal"
                                                aria-selected="false">{{ __('Paypal') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')
                                    @if (isset($payment_setting['paystack_public_key']) &&
                                            !empty($payment_setting['paystack_public_key']) &&
                                            (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paystack-tab" data-bs-toggle="pill"
                                                href="#paystack-payment" role="tab" aria-controls="paystack"
                                                aria-selected="false">{{ __('Paystack') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')
                                    @if (isset($payment_setting['flutterwave_secret_key']) &&
                                            !empty($payment_setting['flutterwave_secret_key']) &&
                                            (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-flutterwave-tab" data-bs-toggle="pill"
                                                href="#flutterwave-payment" role="tab" aria-controls="flutterwave"
                                                aria-selected="false">{{ __('Flutterwave') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
                                    @if (isset($payment_setting['razorpay_public_key']) &&
                                            !empty($payment_setting['razorpay_public_key']) &&
                                            (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-razorpay-tab" data-bs-toggle="pill"
                                                href="#razorpay-payment" role="tab" aria-controls="razorpay"
                                                aria-selected="false">{{ __('Razorpay') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on')
                                    @if (isset($payment_setting['mercado_access_token']) && !empty($payment_setting['mercado_access_token']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-mercado-tab" data-bs-toggle="pill"
                                                href="#mercado-payment" role="tab" aria-controls="mercado"
                                                aria-selected="false">{{ __('Mercado Pago') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on')
                                    @if (isset($payment_setting['paytm_merchant_id']) &&
                                            !empty($payment_setting['paytm_merchant_id']) &&
                                            (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paytm-tab" data-bs-toggle="pill"
                                                href="#paytm-payment" role="tab" aria-controls="paytm"
                                                aria-selected="false">{{ __('Paytm') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on')
                                    @if (isset($payment_setting['mollie_api_key']) &&
                                            !empty($payment_setting['mollie_api_key']) &&
                                            (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-mollie-tab" data-bs-toggle="pill"
                                                href="#mollie-payment" role="tab" aria-controls="mollie"
                                                aria-selected="false">{{ __('Mollie') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on')
                                    @if (isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-skrill-tab" data-bs-toggle="pill"
                                                href="#skrill-payment" role="tab" aria-controls="skrill"
                                                aria-selected="false">{{ __('Skrill') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on')
                                    @if (isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-coingate-tab" data-bs-toggle="pill"
                                                href="#coingate-payment" role="tab" aria-controls="coingate"
                                                aria-selected="false">{{ __('CoinGate') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_paymentwall_enabled']) && $payment_setting['is_paymentwall_enabled'] == 'on')
                                    @if (isset($payment_setting['paymentwall_public_key']) &&
                                            !empty($payment_setting['paymentwall_public_key']) &&
                                            (isset($payment_setting['paymentwall_private_key']) && !empty($payment_setting['paymentwall_private_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paymentwall-tab" data-bs-toggle="pill"
                                                href="#paymentwall-payment" role="tab" aria-controls="paymentwall"
                                                aria-selected="false">{{ __('PaymentWall') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_toyyibpay_enabled']) && $payment_setting['is_toyyibpay_enabled'] == 'on')
                                    @if (isset($payment_setting['toyyibpay_secret_key']) &&
                                            !empty($payment_setting['toyyibpay_secret_key']) &&
                                            (isset($payment_setting['category_code']) && !empty($payment_setting['category_code'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-toyyibpay-tab" data-bs-toggle="pill"
                                                href="#toyyibpay-payment" role="tab" aria-controls="toyyibpay"
                                                aria-selected="false">{{ __('Toyyibpay') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_payfast_enabled']) && $payment_setting['is_payfast_enabled'] == 'on')
                                    @if (isset($payment_setting['payfast_merchant_id']) &&
                                            !empty($payment_setting['payfast_merchant_id']) &&
                                            (isset($payment_setting['payfast_merchant_key']) && !empty($payment_setting['payfast_merchant_key'])) &&
                                            (isset($payment_setting['payfast_signature']) && !empty($payment_setting['payfast_signature'])) &&
                                            (isset($payment_setting['payfast_mode']) && !empty($payment_setting['payfast_mode'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-payfast-tab" data-bs-toggle="pill"
                                                href="#payfast-payment" role="tab" aria-controls="payfast"
                                                aria-selected="false">{{ __('Payfast') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_iyzipay_enabled']) && $payment_setting['is_iyzipay_enabled'] == 'on')
                                    @if (isset($payment_setting['iyzipay_key']) &&
                                            !empty($payment_setting['iyzipay_key']) &&
                                            (isset($payment_setting['iyzipay_secret']) && !empty($payment_setting['iyzipay_secret'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-iyzipay-tab" data-bs-toggle="pill"
                                                href="#iyzipay-payment" role="tab" aria-controls="iyzipay"
                                                aria-selected="false">{{ __('IyziPay') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_sspay_enabled']) && $payment_setting['is_sspay_enabled'] == 'on')
                                    @if (isset($payment_setting['sspay_secret_key']) &&
                                            !empty($payment_setting['sspay_secret_key']) &&
                                            (isset($payment_setting['sspay_category_code']) && !empty($payment_setting['sspay_category_code'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-sspay-tab" data-bs-toggle="pill"
                                                href="#sspay-payment" role="tab" aria-controls="sspay"
                                                aria-selected="false">{{ __('SSPay') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_paytab_enabled']) && $payment_setting['is_paytab_enabled'] == 'on')
                                    @if (isset($payment_setting['paytab_profile_id']) &&
                                            !empty($payment_setting['paytab_profile_id']) &&
                                            (isset($payment_setting['paytab_server_key']) && !empty($payment_setting['paytab_server_key'])) &&
                                            (isset($payment_setting['paytab_region']) && !empty($payment_setting['paytab_region'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paytab-tab" data-bs-toggle="pill"
                                                href="#paytab-payment" role="tab" aria-controls="paytab"
                                                aria-selected="false">{{ __('PayTab') }}</a>
                                        </li>
                                    @endif
                                @endif


                                @if (isset($payment_setting['is_benefit_enabled']) && $payment_setting['is_benefit_enabled'] == 'on')
                                    @if (isset($payment_setting['benefit_api_key']) &&
                                            !empty($payment_setting['benefit_api_key']) &&
                                            (isset($payment_setting['benefit_secret_key']) && !empty($payment_setting['benefit_secret_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-benefit-tab" data-bs-toggle="pill"
                                                href="#benefit-payment" role="tab" aria-controls="benefit"
                                                aria-selected="false">{{ __('Benefit') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_cashfree_enabled']) && $payment_setting['is_cashfree_enabled'] == 'on')
                                    @if (isset($payment_setting['cashfree_api_key']) &&
                                            !empty($payment_setting['cashfree_api_key']) &&
                                            (isset($payment_setting['cashfree_secret_key']) && !empty($payment_setting['cashfree_secret_key'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-cashfree-tab" data-bs-toggle="pill"
                                                href="#cashfree-payment" role="tab" aria-controls="cashfree"
                                                aria-selected="false">{{ __('Cashfree') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_aamarpay_enabled']) && $payment_setting['is_aamarpay_enabled'] == 'on')
                                    @if (isset($payment_setting['aamarpay_store_id']) &&
                                            !empty($payment_setting['aamarpay_store_id']) &&
                                            (isset($payment_setting['aamarpay_signature_key']) && !empty($payment_setting['aamarpay_signature_key'])) &&
                                            (isset($payment_setting['aamarpay_description']) && !empty($payment_setting['aamarpay_description'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-aamarpay-tab" data-bs-toggle="pill"
                                                href="#aamarpay-payment" role="tab" aria-controls="aamarpay"
                                                aria-selected="false">{{ __('Aamarpay') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_paytr_enabled']) && $payment_setting['is_paytr_enabled'] == 'on')
                                    @if (isset($payment_setting['paytr_merchant_id']) &&
                                            !empty($payment_setting['paytr_merchant_id']) &&
                                            (isset($payment_setting['paytr_merchant_key']) && !empty($payment_setting['paytr_merchant_key'])) &&
                                            (isset($payment_setting['paytr_merchant_salt']) && !empty($payment_setting['paytr_merchant_salt'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paytr-tab" data-bs-toggle="pill"
                                                href="#paytr-payment" role="tab" aria-controls="paytr"
                                                aria-selected="false">{{ __('Pay TR') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_yookassa_enabled']) && $payment_setting['is_yookassa_enabled'] == 'on')
                                    @if (isset($payment_setting['yookassa_shop_id']) &&
                                            !empty($payment_setting['yookassa_shop_id']) &&
                                            (isset($payment_setting['yookassa_secret']) && !empty($payment_setting['yookassa_secret'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-yookassa-tab" data-bs-toggle="pill"
                                                href="#yookassa-payment" role="tab" aria-controls="yookassa"
                                                aria-selected="false">{{ __('Yookassa') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_midtrans_enabled']) && $payment_setting['is_midtrans_enabled'] == 'on')
                                    @if (isset($payment_setting['midtrans_secret']) && !empty($payment_setting['midtrans_secret']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-midtrans-tab" data-bs-toggle="pill"
                                                href="#midtrans-payment" role="tab" aria-controls="midtrans"
                                                aria-selected="false">{{ __('Midtrans') }}</a>
                                        </li>
                                    @endif
                                @endif


                                @if (isset($payment_setting['is_xendit_enabled']) && $payment_setting['is_xendit_enabled'] == 'on')
                                    @if (isset($payment_setting['xendit_api']) && !empty($payment_setting['xendit_api']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-xendit-tab" data-bs-toggle="pill"
                                                href="#xendit-payment" role="tab" aria-controls="xendit"
                                                aria-selected="false">{{ __('Xendit') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_nepalste_enabled']) && $payment_setting['is_nepalste_enabled'] == 'on')
                                    @if (isset($payment_setting['nepalste_public_key']) && !empty($payment_setting['nepalste_public_key']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-nepalste-tab" data-bs-toggle="pill"
                                                href="#nepalste-payment" role="tab" aria-controls="nepalste"
                                                aria-selected="false">{{ __('Nepalste') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_paiementpro_enabled']) && $payment_setting['is_paiementpro_enabled'] == 'on')
                                    @if (isset($payment_setting['paiementpro_merchant_id']) && !empty($payment_setting['paiementpro_merchant_id']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-paiementpro-tab" data-bs-toggle="pill"
                                                href="#paiementpro-payment" role="tab" aria-controls="paiementpro"
                                                aria-selected="false">{{ __('Paiement Pro') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_fedapay_enabled']) && $payment_setting['is_fedapay_enabled'] == 'on')
                                    @if (isset($payment_setting['fedapay_public_key']) && !empty($payment_setting['fedapay_public_key']))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-fedapay-tab" data-bs-toggle="pill"
                                                href="#fedapay-payment" role="tab" aria-controls="fedapay"
                                                aria-selected="false">{{ __('Fedapay') }}</a>
                                        </li>
                                    @endif
                                @endif

                                @if (isset($payment_setting['is_payhere_enabled']) && $payment_setting['is_payhere_enabled'] == 'on')
                                    @if (isset($payment_setting['payhere_merchant_id']) &&
                                            !empty($payment_setting['payhere_merchant_id']) &&
                                            (isset($payment_setting['payhere_merchant_secret']) && !empty($payment_setting['payhere_merchant_secret'])) &&
                                            (isset($payment_setting['payhere_app_id']) && !empty($payment_setting['payhere_app_id'])) &&
                                            (isset($payment_setting['payhere_app_secret']) && !empty($payment_setting['payhere_app_secret'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-payhere-tab" data-bs-toggle="pill"
                                                href="#payhere-payment" role="tab" aria-controls="payhere"
                                                aria-selected="false">{{ __('PayHere') }}</a>
                                        </li>
                                    @endif
                                @endif
                                @if (isset($payment_setting['is_cinetpay_enabled']) && $payment_setting['is_cinetpay_enabled'] == 'on')
                                    @if (isset($payment_setting['cinetpay_api_key']) &&
                                            !empty($payment_setting['cinetpay_api_key']) &&
                                            (isset($payment_setting['cinetpay_site_id']) && !empty($payment_setting['cinetpay_site_id'])))
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-cinetpay-tab" data-bs-toggle="pill"
                                                href="#cinetpay-payment" role="tab" aria-controls="cinetpay"
                                                aria-selected="false">{{ __('CinetPay') }}</a>
                                        </li>
                                    @endif
                                @endif


                            </ul>
                        </div>

                        <div class="tab-content">

                            @if (isset($payment_setting['is_bank_enabled']) && $payment_setting['is_bank_enabled'] == 'on')
                                @if (isset($payment_setting['bank_details']))
                                    <div class="tab-pane fade {{ isset($payment_setting['is_bank_enabled']) && $payment_setting['is_bank_enabled'] == 'on' ? 'show active' : '' }}"
                                        id="bank-payment" role="tabpanel" aria-labelledby="bank-payment">
                                        <form class="w3-container w3-display-middle w3-card-4 "
                                            action="{{ route('invoice.pay.with.bank') }}" method="POST"
                                            enctype="multipart/form-data" id="bank-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label"><b>{{ __('Bank Details:') }}</b></label>
                                                    <div class="form-group">
                                                        @if (isset($payment_setting['bank_details']) && !empty($payment_setting['bank_details']))
                                                            {!! $payment_setting['bank_details'] !!}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label"> {{ __('Payment Receipt') }}</label>
                                                    <div class="form-group">
                                                        <input type="file" name="payment_receipt"
                                                            class="form-control mb-3" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="amount"
                                                            class="col-form-label">{{ __('Amount') }}</label>
                                                        <div class="input-group col-md-12">
                                                            <div class="input-group-text">
                                                                {{ $payment_setting['currency_symbol'] }}</div>
                                                            <input class="form-control" required="required"
                                                                min="0" name="amount" type="number"
                                                                value="{{ $invoice->getDue() }}" min="0"
                                                                step="0.01" max="{{ $invoice->getDue() }}"
                                                                id="amount" placeholder="Enter Amount">
                                                            <input type="hidden" value="{{ $invoice->id }}"
                                                                name="invoice_id">
                                                        </div>
                                                        @error('amount')
                                                            <span class="invalid-amount text-danger text-xs"
                                                                role="alert">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 form-group mt-3 text-end">
                                                        <input type="submit" value="{{ __('Make Payment') }}"
                                                            class="btn btn-print-invoice  btn-primary m-r-10">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on')
                                @if (isset($payment_setting['stripe_key']) &&
                                        !empty($payment_setting['stripe_key']) &&
                                        (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret'])))
                                    <div class="tab-pane fade {{ isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on' }}"
                                        id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form" action="{{ route('invoice.pay.with.stripe') }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ $payment_setting['currency_symbol'] }}</div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_paypal_enabled']) && $payment_setting['is_paypal_enabled'] == 'on')
                                @if (isset($payment_setting['paypal_client_id']) &&
                                        !empty($payment_setting['paypal_client_id']) &&
                                        (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key'])))
                                    <div class="tab-pane fade" id="paypal-payment" role="tabpanel"
                                        aria-labelledby="paypal-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('client.pay.with.paypal', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')
                                @if (isset($payment_setting['paystack_public_key']) &&
                                        !empty($payment_setting['paystack_public_key']) &&
                                        (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key'])))
                                    <div class="tab-pane fade" id="paystack-payment" role="tabpanel"
                                        aria-labelledby="paystack-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.paystack') }}"
                                            class="require-validation" id="paystack-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="Email" class="form-label">{{ __('Email') }}</label>
                                                    <input class="form-control" required="required" id="paystack_email"
                                                        name="email" type="email" placeholder="Enter Email">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="button" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10"
                                                    id="pay_with_paystack">
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')
                                @if (isset($payment_setting['flutterwave_secret_key']) &&
                                        !empty($payment_setting['flutterwave_secret_key']) &&
                                        (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key'])))
                                    <div class="tab-pane fade " id="flutterwave-payment" role="tabpanel"
                                        aria-labelledby="flutterwave-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.flaterwave') }}"
                                            class="require-validation" id="flaterwave-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="Email" class="form-label">{{ __('Email') }}</label>
                                                    <input class="form-control" required="required"
                                                        id="flutterwave_email" name="email" type="email"
                                                        placeholder="Enter Email">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="button" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10"
                                                    id="pay_with_flaterwave">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
                                @if (isset($payment_setting['razorpay_public_key']) &&
                                        !empty($payment_setting['razorpay_public_key']) &&
                                        (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key'])))
                                    <div class="tab-pane fade " id="razorpay-payment" role="tabpanel"
                                        aria-labelledby="flutterwave-payment">
                                        <form method="post" action="{{ route('invoice.pay.with.razorpay') }}"
                                            class="require-validation" id="razorpay-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="Email" class="form-label">{{ __('Email') }}</label>
                                                    <input class="form-control" required="required" id="razorpay_email"
                                                        name="email" type="email" placeholder="Enter Email">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="button" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10"
                                                    id="pay_with_razorpay">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on')
                                @if (isset($payment_setting['mollie_api_key']) &&
                                        !empty($payment_setting['mollie_api_key']) &&
                                        (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id'])))
                                    <div class="tab-pane fade " id="mollie-payment" role="tabpanel"
                                        aria-labelledby="mollie-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.mollie') }}"
                                            class="require-validation" id="mollie-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on')
                                @if (isset($payment_setting['mercado_access_token']) && !empty($payment_setting['mercado_access_token']))
                                    <div class="tab-pane fade " id="mercado-payment" role="tabpanel"
                                        aria-labelledby="mercado-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.mercado') }}"
                                            class="require-validation" id="mercado-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on')
                                @if (isset($payment_setting['paytm_merchant_id']) &&
                                        !empty($payment_setting['paytm_merchant_id']) &&
                                        (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key'])))
                                    <div class="tab-pane fade " id="paytm-payment" role="tabpanel"
                                        aria-labelledby="paytm-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.paytm') }}"
                                            class="require-validation" id="paytm-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">

                                                    <label for="Email" class="form-label">{{ __('Email') }}</label>
                                                    <input class="form-control" required="required" id="paytm_email"
                                                        name="email" type="email" placeholder="Enter Email">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <div class="form-group">
                                                        <label for="mobile"
                                                            class="col-form-label">{{ __('Mobile Number') }}</label>
                                                        <input type="text" id="mobile" name="mobile"
                                                            class="form-control mobile" data-from="mobile"
                                                            placeholder="{{ __('Enter Mobile Number') }}" required>
                                                    </div>
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on')
                                @if (isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email']))
                                    <div class="tab-pane fade " id="skrill-payment" role="tabpanel"
                                        aria-labelledby="skrill-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.skrill') }}"
                                            class="require-validation" id="skrill-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="Name" class="form-label">{{ __('Name') }}</label>
                                                    <input class="form-control" required="required" id="skrill_name"
                                                        name="name" type="text" placeholder="Enter your name">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="Email" class="form-label">{{ __('Email') }}</label>
                                                    <input class="form-control" required="required" id="skrill_email"
                                                        name="email" type="email" placeholder="Enter Email">
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-primary">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on')
                                @if (isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token']))
                                    <div class="tab-pane fade " id="coingate-payment" role="tabpanel"
                                        aria-labelledby="coingate-payment">

                                        <form method="post" action="{{ route('invoice.pay.with.coingate') }}"
                                            class="require-validation" id="coingate-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif

                            <div class="tab-pane fade" id="paymentwall-payment" role="tabpanel"
                                aria-labelledby="paymentwall-payment-tab">

                                @if (isset($payment_setting['is_paymentwall_enabled']) && $payment_setting['is_paymentwall_enabled'] == 'on')
                                    @if (isset($payment_setting['paymentwall_public_key']) &&
                                            !empty($payment_setting['paymentwall_public_key']) &&
                                            (isset($payment_setting['paymentwall_private_key']) && !empty($payment_setting['paymentwall_private_key'])))
                                        <form method="post" action="{{ route('paymentwall.invoice') }}"
                                            class="require-validation" id="paymentwall-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10"
                                                    id="pay_with_paymentwall">
                                            </div>
                                        </form>
                                    @endif
                                @endif
                            </div>

                            <div class="tab-pane fade" id="toyyibpay-payment" role="tabpanel"
                                aria-labelledby="toyyibpay-payment-tab">

                                @if (isset($payment_setting['is_toyyibpay_enabled']) && $payment_setting['is_toyyibpay_enabled'] == 'on')
                                    @if (isset($payment_setting['toyyibpay_secret_key']) &&
                                            !empty($payment_setting['toyyibpay_secret_key']) &&
                                            (isset($payment_setting['category_code']) && !empty($payment_setting['category_code'])))
                                        <form method="post" action="{{ route('invoice.with.toyyibpay') }}"
                                            class="require-validation" id="paymentwall-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 form-group mt-3 text-end">
                                                <input type="submit" value="{{ __('Make Payment') }}"
                                                    class="btn btn-print-invoice  btn-primary m-r-10"
                                                    id="pay_with_toyyibpay">
                                            </div>
                                        </form>
                                    @endif
                                @endif
                            </div>

                            <div class="tab-pane fade" id="payfast-payment" role="tabpanel"
                                aria-labelledby="payfast-payment-tab">

                                @if (isset($payment_setting['is_payfast_enabled']) && $payment_setting['is_payfast_enabled'] == 'on')
                                    @if (isset($payment_setting['payfast_merchant_id']) &&
                                            !empty($payment_setting['payfast_merchant_id']) &&
                                            (isset($payment_setting['payfast_merchant_key']) && !empty($payment_setting['payfast_merchant_key'])) &&
                                            (isset($payment_setting['payfast_signature']) && !empty($payment_setting['payfast_signature'])) &&
                                            (isset($payment_setting['payfast_mode']) && !empty($payment_setting['payfast_mode'])))
                                        @php
                                            $pfHost =
                                                $payment_setting['payfast_mode'] == 'sandbox'
                                                    ? 'sandbox.payfast.co.za'
                                                    : 'www.payfast.co.za';
                                        @endphp
                                        <form method="post" action={{ 'https://' . $pfHost . '/eng/process' }}
                                            class="require-validation" id="payfast-payment-form">
                                            @csrf
                                            <div class="row">

                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control input_payfast" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">

                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <input type="hidden" name="invoice_id" id="invoice_id" class=""
                                                    value="{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}">
                                                <div id="get-payfast-inputs"></div>
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    @endif
                                @endif
                            </div>

                            @if (isset($payment_setting['is_iyzipay_enabled']) && $payment_setting['is_iyzipay_enabled'] == 'on')
                                @if (isset($payment_setting['iyzipay_key']) &&
                                        !empty($payment_setting['iyzipay_key']) &&
                                        (isset($payment_setting['iyzipay_secret']) && !empty($payment_setting['iyzipay_secret'])))
                                    <div class="tab-pane fade" id="iyzipay-payment" role="tabpanel"
                                        aria-labelledby="iyzipay-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form" action="{{ route('invoice.with.iyzipay') }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_sspay_enabled']) && $payment_setting['is_sspay_enabled'] == 'on')
                                @if (isset($payment_setting['sspay_secret_key']) &&
                                        !empty($payment_setting['sspay_secret_key']) &&
                                        (isset($payment_setting['sspay_category_code']) && !empty($payment_setting['sspay_category_code'])))
                                    <div class="tab-pane fade" id="sspay-payment" role="tabpanel"
                                        aria-labelledby="sspay-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('customer.pay.with.sspay', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_paytab_enabled']) && $payment_setting['is_paytab_enabled'] == 'on')
                                @if (isset($payment_setting['paytab_profile_id']) &&
                                        !empty($payment_setting['paytab_profile_id']) &&
                                        (isset($payment_setting['paytab_server_key']) && !empty($payment_setting['paytab_server_key'])) &&
                                        (isset($payment_setting['paytab_region']) && !empty($payment_setting['paytab_region'])))
                                    <div class="tab-pane fade" id="paytab-payment" role="tabpanel"
                                        aria-labelledby="paytab-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form" action="{{ route('pay.with.paytab', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_benefit_enabled']) && $payment_setting['is_benefit_enabled'] == 'on')
                                @if (isset($payment_setting['benefit_api_key']) &&
                                        !empty($payment_setting['benefit_api_key']) &&
                                        (isset($payment_setting['benefit_secret_key']) && !empty($payment_setting['benefit_secret_key'])))
                                    <div class="tab-pane fade" id="benefit-payment" role="tabpanel"
                                        aria-labelledby="benefit-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form" action="{{ route('pay.with.benefit', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_cashfree_enabled']) && $payment_setting['is_cashfree_enabled'] == 'on')
                                @if (isset($payment_setting['cashfree_api_key']) &&
                                        !empty($payment_setting['cashfree_api_key']) &&
                                        (isset($payment_setting['cashfree_secret_key']) && !empty($payment_setting['cashfree_secret_key'])))
                                    <div class="tab-pane fade" id="cashfree-payment" role="tabpanel"
                                        aria-labelledby="cashfree-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('invoice.with.cashfree', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_aamarpay_enabled']) && $payment_setting['is_aamarpay_enabled'] == 'on')
                                @if (isset($payment_setting['aamarpay_store_id']) &&
                                        !empty($payment_setting['aamarpay_store_id']) &&
                                        (isset($payment_setting['aamarpay_signature_key']) && !empty($payment_setting['aamarpay_signature_key'])) &&
                                        (isset($payment_setting['aamarpay_description']) && !empty($payment_setting['aamarpay_description'])))
                                    <div class="tab-pane fade" id="aamarpay-payment" role="tabpanel"
                                        aria-labelledby="aamarpay-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form" action="{{ route('pay.with.aamarpay', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_paytr_enabled']) && $payment_setting['is_paytr_enabled'] == 'on')
                                @if (isset($payment_setting['paytr_merchant_id']) &&
                                        !empty($payment_setting['paytr_merchant_id']) &&
                                        (isset($payment_setting['paytr_merchant_key']) && !empty($payment_setting['paytr_merchant_key'])) &&
                                        (isset($payment_setting['paytr_merchant_salt']) && !empty($payment_setting['paytr_merchant_salt'])))
                                    <div class="tab-pane fade" id="paytr-payment" role="tabpanel"
                                        aria-labelledby="paytr-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('invoice.with.paytr', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_yookassa_enabled']) && $payment_setting['is_yookassa_enabled'] == 'on')
                                @if (isset($payment_setting['yookassa_shop_id']) &&
                                        !empty($payment_setting['yookassa_shop_id']) &&
                                        (isset($payment_setting['yookassa_secret']) && !empty($payment_setting['yookassa_secret'])))
                                    <div class="tab-pane fade" id="yookassa-payment" role="tabpanel"
                                        aria-labelledby="yookassa-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('invoice.with.yookassa', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_midtrans_enabled']) && $payment_setting['is_midtrans_enabled'] == 'on')
                                @if (isset($payment_setting['midtrans_secret']) && !empty($payment_setting['midtrans_secret']))
                                    <div class="tab-pane fade" id="midtrans-payment" role="tabpanel"
                                        aria-labelledby="midtrans-payment">
                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('invoice.with.midtrans', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif

                            @if (isset($payment_setting['is_xendit_enabled']) && $payment_setting['is_xendit_enabled'] == 'on')
                                @if (isset($payment_setting['xendit_api']) && !empty($payment_setting['xendit_api']))
                                    <div class="tab-pane fade" id="xendit-payment" role="tabpanel"
                                        aria-labelledby="xendit-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('invoice.with.xendit', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif


                            @if (isset($payment_setting['is_nepalste_enabled']) && $payment_setting['is_nepalste_enabled'] == 'on')
                                @if (isset($payment_setting['nepalste_public_key']) && !empty($payment_setting['nepalste_public_key']))
                                    <div class="tab-pane fade" id="nepalste-payment" role="tabpanel"
                                        aria-labelledby="nepalste-payment">

                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('invoice.with.nepalste', $invoice->id) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="input-group col-md-12">
                                                        <div class="input-group-text">
                                                            {{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}
                                                        </div>
                                                        <input class="form-control" required="required" min="0"
                                                            name="amount" type="number"
                                                            value="{{ $invoice->getDue() }}" min="0"
                                                            step="0.01" max="{{ $invoice->getDue() }}"
                                                            id="amount">
                                                        <input type="hidden" value="{{ $invoice->id }}"
                                                            name="invoice_id">
                                                    </div>
                                                    @error('amount')
                                                        <span class="invalid-amount text-danger text-xs"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 form-group mt-3 text-end">
                                                    <input type="submit" value="{{ __('Make Payment') }}"
                                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                                </div>
                                            </div>
                                        </form>


                                    </div>
                                @endif
                            @endif
                            @if (isset($payment_setting['is_paiementpro_enabled']) && $payment_setting['is_paiementpro_enabled'] == 'on')
                                <div class="tab-pane fade" id="paiementpro-payment" role="tabpanel"
                                    aria-labelledby="paiementpro-payment">
                                    <form method="post" action="{{ route('invoice.with.paiementpro') }}"
                                        class="require-validation" id="paiementpro-payment-form">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="amount">{{ __('Amount') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend"><span
                                                            class="input-group-text">{{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}</span></span>


                                                    <input class="form-control" required="required" min="0"
                                                        name="amount" type="number"
                                                        value="{{ $invoice->getDue() }}" min="0"
                                                        step="0.01" max="{{ $invoice->getDue() }}"
                                                        id="amount">
                                                    <input type="hidden" value="{{ $invoice->id }}"
                                                        name="invoice_id">

                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="mobile_number">{{ __('Mobile Number') }}</label>
                                                    <input type="text" id="paiementpro_mobile_number"
                                                        name="mobile_number" class="form-control mobile_number"
                                                        data-from="mobile_number"
                                                        placeholder="{{ __('Enter Mobile Number') }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="channel">{{ __('Channel') }}</label>
                                                    <input type="text" id="paiementpro_channel" name="channel"
                                                        class="form-control channel" data-from="channel"
                                                        placeholder="{{ __('Enter Channel') }}" required>
                                                    <small
                                                        class="text-danger">{{ __('Example : OMCIV2,MOMO,CARD,FLOOZ ,PAYPAL') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">

                                            <button class="btn btn-primary"
                                                type="submit">{{ __('Make Payment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            @if (isset($payment_setting['is_fedapay_enabled']) && $payment_setting['is_fedapay_enabled'] == 'on')
                                <div class="tab-pane fade " id="fedapay-payment" role="tabpanel"
                                    aria-labelledby="fedapay-payment">
                                    <form method="post" action="{{ route('invoice.pay.fedapay') }}"
                                        class="require-validation" id="fedapay-payment-form">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="amount">{{ __('Amount') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend"><span
                                                            class="input-group-text">{{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}</span></span>
                                                    <input class="form-control" required="required" min="0"
                                                        name="amount" type="number"
                                                        value="{{ $invoice->getDue() }}" min=""
                                                        step="0.01" max="{{ $invoice->getDue() }}"
                                                        id="amount">
                                                    <input type="hidden" value="{{ $invoice->id }}"
                                                        name="invoice_id">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">

                                            <button class="btn btn-primary"
                                                type="submit">{{ __('Make Payment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if (isset($payment_setting['is_payhere_enabled']) && $payment_setting['is_payhere_enabled'] == 'on')
                                <div class="tab-pane fade " id="payhere-payment" role="tabpanel"
                                    aria-labelledby="payhere-payment">
                                    <form method="post" action="{{ route('invoice.pay.payhere') }}"
                                        class="require-validation" id="payhere-payment-form">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="amount">{{ __('Amount') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend"><span
                                                            class="input-group-text">{{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}</span></span>
                                                    <input class="form-control" required="required" min="0"
                                                        name="amount" type="number"
                                                        value="{{ $invoice->getDue() }}" min="0"
                                                        step="0.01" max="{{ $invoice->getDue() }}"
                                                        id="amount">
                                                    <input type="hidden" value="{{ $invoice->id }}"
                                                        name="invoice_id">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">

                                            <button class="btn btn-primary"
                                                type="submit">{{ __('Make Payment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if (isset($payment_setting['is_cinetpay_enabled']) && $payment_setting['is_cinetpay_enabled'] == 'on')
                                <div class="tab-pane fade " id="cinetpay-payment" role="tabpanel"
                                    aria-labelledby="cinetpay-payment">
                                    <form method="post" action="{{ route('invoice.pay.cinetpay') }}"
                                        class="require-validation" id="cinetpay-payment-form">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="amount">{{ __('Amount') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-prepend"><span
                                                            class="input-group-text">{{ isset($payment_setting['currency_symbol']) ? $payment_setting['currency_symbol'] : '$' }}</span></span>
                                                    <input class="form-control" required="required" min="0"
                                                        name="amount" type="number"
                                                        value="{{ $invoice->getDue() }}" min="0"
                                                        step="0.01" max="{{ $invoice->getDue() }}"
                                                        id="amount">
                                                    <input type="hidden" value="{{ $invoice->id }}"
                                                        name="invoice_id">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">

                                            <button class="btn btn-primary"
                                                type="submit">{{ __('Make Payment') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            
    @endif

    </div>
    </div>
    </div>
    </div>
    </div>

@endsection
@push('script-page')
    @if (
        $invoice->getDue() > 0 &&
            isset($payment_setting['is_stripe_enabled']) &&
            $payment_setting['is_stripe_enabled'] == 'on')
        <?php $stripe_session = Session::get('stripe_session'); ?>
        <?php if(isset($stripe_session) && $stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            var stripe = Stripe('{{ $payment_setting['stripe_key'] }}');
            stripe.redirectToCheckout({
                sessionId: '{{ $stripe_session->id }}',
            }).then((result) => {
                console.log(result);
            });
        </script>
        <?php endif ?>
    @endif

    @if (
        $invoice->getDue() > 0 &&
            isset($payment_setting['is_paystack_enabled']) &&
            $payment_setting['is_paystack_enabled'] == 'on')
        <script src="https://js.paystack.co/v1/inline.js"></script>

        <script type="text/javascript">
            $(document).on("click", "#pay_with_paystack", function() {

                $('#paystack-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;

                        var paystack_callback = "{{ url('/invoice-pay-with-paystack') }}";
                        var order_id = '{{ time() }}';
                        var handler = PaystackPop.setup({
                            key: '{{ $payment_setting['paystack_public_key'] }}',
                            email: res.email,
                            amount: res.total_price * 100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {
                                console.log(response.reference, order_id);
                                window.location.href = "{{ url('/invoice/paystack') }}/" +
                                    response.reference + "/{{ encrypt($invoice->id) }}";
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    @endif

    @if (
        $invoice->getDue() > 0 &&
            isset($payment_setting['is_flutterwave_enabled']) &&
            $payment_setting['is_flutterwave_enabled'] == 'on')
        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

        <script type="text/javascript">
            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function() {

                $('#flaterwave-payment-form').ajaxForm(function(res) {
                    if (res.flag == 1) {
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if ("{{ isset($payment_setting['flutterwave_public_key']) }}") {
                            API_publicKey = "{{ $payment_setting['flutterwave_public_key'] }}";
                        }
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/invoice-pay-with-flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: res.email,
                            amount: res.total_price,
                            currency: '{{ $payment_setting['currency'] }}',
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                'fluttpay_online-' +
                                {{ date('Y-m-d') }},
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function() {},
                            callback: function(response) {
                                var txref = response.tx.txRef;
                                if (response.tx.chargeResponseCode == "00" || response.tx
                                    .chargeResponseCode == "0") {
                                    window.location.href = "{{ url('/invoice/flaterwave') }}/" +
                                        txref + "/{{ encrypt($invoice->id) }}";
                                } else {
                                    // redirect to a failure page.
                                }
                                x.close(); // use this to close the modal immediately after payment.
                            }
                        });
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    @endif

    @if (
        $invoice->getDue() > 0 &&
            isset($payment_setting['is_razorpay_enabled']) &&
            $payment_setting['is_razorpay_enabled'] == 'on')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <script type="text/javascript">
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function() {
                $('#razorpay-payment-form').ajaxForm(function(res) {

                    if (res.flag == 1) {

                        var razorPay_callback = "{{ url('/invoice-pay-with-razorpay') }}";
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if ("{{ isset($payment_setting['razorpay_public_key']) }}") {
                            API_publicKey = "{{ $payment_setting['razorpay_public_key'] }}";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Invoice Payment',
                            "currency": '{{ $payment_setting['currency'] }}',
                            "description": "",
                            "handler": function(response) {
                                window.location.href = "{{ url('/invoice/razorpay') }}/" + response
                                    .razorpay_payment_id + "/{{ encrypt($invoice->id) }}";
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    } else if (res.flag == 2) {

                    } else {
                        show_toastr('Error', data.message, 'msg');
                    }
                }).submit();
            });
        </script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
            integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
        </script>
    @endif
    @if (
        $invoice->getDue() > 0 &&
            isset($payment_setting['is_payfast_enabled']) &&
            $payment_setting['is_payfast_enabled'] == 'on')
        <script>
            $(".input_payfast").keyup(function() {
                var invoice_amount = $('#amount').val();
                get_payfast_status(invoice_amount);
            });
            $(document).ready(function() {
                get_payfast_status(amount = 0);
            })

            function get_payfast_status(amount) {

                var invoice_id = $('#invoice_id').val();
                var invoice_amount = $('#amount').val();
                $.ajax({
                    url: '{{ route('invoice.with.payfast') }}',
                    method: 'POST',
                    data: {
                        'invoice_id': invoice_id,
                        'amount': invoice_amount,

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        if (data.success == true) {
                            $('#get-payfast-inputs').append(data.inputs);

                        } else {
                            show_toastr('Error', data.inputs, 'error')
                        }
                    }
                });
            }
        </script>
    @endif
    @if ($setting['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif
    <!-- Khalti Payment -->
@if (isset($payment_setting['is_khalti_enabled']) && $payment_setting['is_khalti_enabled'] == 'on')
<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

<script>
    var config = {
        "publicKey"         : "{{ isset($payment_setting['khalti_public_key']) ? $payment_setting['khalti_public_key'] : '' }}",
        "productIdentity"   : "1234567890",
        "productName"       : "demo",
        "productUrl"        : "{{ env('APP_URL') }}",
        "paymentPreference" : [
            "KHALTI",
            "EBANKING",
            "MOBILE_BANKING",
            "CONNECT_IPS",
            "SCT",
        ],
        "eventHandler": {
            onSuccess(payload) {
                if (payload.status == 200) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}'
                        }
                    });
                    $.ajax({
                        url: "{{ route('invoice.get.khalti.status') }}",
                        method: 'POST',
                        data: {
                            'payload': payload,
                            'invoice_id': $('.khalti_invoice_id').val(),
                            'amount': $('.khalti_amount').val(),
                        },
                        beforeSend: function() {
                            $(".loader-wrapper").removeClass('d-none');
                        },
                        success: function(data) {
                            $(".loader-wrapper").addClass('d-none');
                            if (data.status_code === 200) {
                                show_toastr('Success', 'Payment Done Successfully', 'success');
                                setTimeout(() => {
                                    window.location.href = "{{ route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)) }}";
                                }, 1000);
                            } else {
                                show_toastr('Error', 'Payment Failed', 'msg');
                            }
                        },
                        error: function(err) {
                            show_toastr('Error', err.response, 'msg');
                        },
                    });
                }
            },
            onError(error) {
                show_toastr('Error', error, 'msg')
            },
            onClose() {}
        }

    };

    var checkout    = new KhaltiCheckout(config);
    var btn         = document.getElementsByClassName("payment-btn")[0];

</script>
<script>
    $(document).on("click", "#pay_with_khalti", function(event) {
        event.preventDefault()
        var invoice_id = $('.khalti_invoice_id').val();
        var amount     = $('.khalti_amount').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url     : "{{ route('invoice.with.khalti') }}",
            method  : 'POST',
            data    : {
                'invoice_id': invoice_id,
                'amount': amount,
            },

            beforeSend: function() {
                $(".loader-wrapper").removeClass('d-none');
            },
            success : function(data) {
                $(".loader-wrapper").addClass('d-none');
                if (data == 0) {
                    show_toastr('Error', 'Enter valid amount', 'danger');
                    setTimeout(() => {
                        window.location.href = '{{ route("pay.invoice", \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)) }}';
                    }, 1000);
                } else {
                    let price = data * 100;
                    checkout.show({
                        amount: price
                    });
                }
            }
        });
    })


</script>
@endif
<!-- Khalti Payment End -->
@endpush
