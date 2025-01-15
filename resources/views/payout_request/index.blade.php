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
@endphp
@section('content')
<div class="row">
    <div class="col-3">
        @include('layouts.referral_setup')
    </div>
    <div class="col-9">

        <div class="card">
            <div class="card-header">
                <h5>{{ __('Payout Request') }}</h5>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Company Name') }}</th>
                                <th>{{ __('Requested Date')}}</th>
                                <th>{{ __('Requested Amount') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payoutRequests as $key => $payoutRequest)
                            <tr>
                                <td> {{( ++ $key)}} </td>
                                <td>{{ !empty( $payoutRequest->company_name) ? $payoutRequest->company_name->name : '-'}}</td>
                                <td>{{ $payoutRequest->request_date }}</td>
                                <td>{{ isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$' }}{{ $payoutRequest->requested_amount }}</td>
                                <td>
                                    <div class="d-flex">

                                    <a href="{{route('request.amount',[$payoutRequest->id,1])}}" class="btn btn-success btn-sm me-2"  data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('Accept') }}">
                                        <i class="ti ti-check"></i>
                                    </a>
                                    <a href="{{route('request.amount',[$payoutRequest->id,0])}}" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('Reject') }}">
                                        <i class="ti ti-x"></i>
                                    </a>
                                    </div>
                                </td>
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