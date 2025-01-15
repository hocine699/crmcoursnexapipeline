@extends('layouts.admin')
@section('page-title')
{{ __('Referral Program') }}
@endsection
@section('title')
{{ __('Referral Program') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Referral Program') }}</li>
@endsection
@php
$payment = App\Models\Utility::set_payment_settings();
$totalAmount = 0;
@endphp
@foreach($transactions as $transaction)
@php
$totalAmount += $transaction->amount;

@endphp
@endforeach
@section('content')
<div class="row">
    <div class="col-3">
        @include('layouts.referral_setup')
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ __('Payout') }}</h5>
                <div>
                    @if($totalAmount != $paidAmount)
                    @if($paymentRequest == null)
                    <a href="#" data-url="{{ route('payoutsend.request', \Auth::user()->id) }}" data-size="md" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{ __('Send Request') }}" title="{{ __('Send Request') }}" class="btn btn-sm btn-primary btn-icon m-1">
                        <span class="btn-inner--icon"><i class="ti ti-arrow-forward-up"></i></span>
                    </a>
                    @else
                    <a href="{{ route('payoutrequest.cancel', \Auth::user()->id) }}" data-size="md" class="btn btn-danger btn-sm btn-icon m-1" data-title="{{ __('Cancel Request') }}" data-bs-toggle="tooltip" title="{{ __('Cancel Request') }}">
                        <span class="btn-inner--icon"><i class="ti ti-x"></i></span>
                    </a>
                    @endif
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="border p-3 d-flex align-items-center">
                                <div class="theme-avtar bg-primary me-3">
                                    <i class="ti ti-report-money"></i>
                                </div>
                                <div>
                                    <p class="text-muted text-sm mb-2">Total</p>
                                    <h5>Commission Amount</h5>
                                </div>
                                <div class="ms-auto">
                                    <h4>{{ isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$' }} {{$totalAmount}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="border p-3 d-flex align-items-center">
                                <div class="theme-avtar bg-primary me-3">
                                    <i class="ti ti-report-money"></i>
                                </div>
                                <div>
                                    <p class="text-muted text-sm mb-2">Paid</p>
                                    <h5>Paid Amount</h5>
                                </div>
                                <div class="ms-auto">
                                    <h4>{{ isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$' }} {{$paidAmount}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <h5>{{ __('Payout History') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{__('#')}}</th>
                                <th>{{__('Company Name')}}</th>
                                <th>{{__('Request Date')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Requested Amount')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $key = 1
                            @endphp
                            @foreach($payouts as $payout)
                            <tr>
                                <td>{{ $key++ }}</td>
                                <td>{{ isset($payout->company_name) ? $payout->company_name->name : '' }}</td>
                                <td>{{ isset($payout->request_date) ? $payout->request_date : ''}}</td>
                                <td>

                                    @if ($payout->status == 0)
                                    <span class="badge bg-warning p-2 px-3" style="width: 91px;">{{ __(\App\Models\Payout::$status[$payout->status]) }}</span>
                                    @elseif($payout->status == 1)
                                    <span class="badge bg-danger p-2 px-3" style="width: 91px;">{{ __(\App\Models\Payout::$status[$payout->status]) }}</span>
                                    @elseif($payout->status == 2)
                                    <span class="badge bg-success p-2 px-3" style="width: 91px;">{{ __(\App\Models\Payout::$status[$payout->status]) }}</span>
                                    @endif
                                </td>
                                <td>{{ isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$' }}{{ isset($payout->requested_amount) ? $payout->requested_amount : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
