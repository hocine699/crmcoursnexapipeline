@extends('layouts.admin')
@section('page-title')
{{ __('Referral Program') }}
@endsection
@section('title')
{{ __('Referral Program') }}
@endsection
@php
$payment = App\Models\Utility::set_payment_settings();
@endphp
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Referral Program') }}</li>
@endsection
@section('content')
<div class="row">
    <div class="col-3">
        @include('layouts.referral_setup')
    </div>
    <div class="col-9">

        <div class="card">
            <div class="card-header">
                <h5>{{ __('Transaction') }}</h5>
            </div>
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('#') }}</th>
                                <th scope="col">{{ __('Company Name') }}</th>
                                <th scope="col">{{ __('Referral Company Name') }}</th>
                                <th scope="col">{{ __('Plan Name') }}</th>
                                <th scope="col">{{ __('Plan Price') }}</th>
                                <th scope="col">{{ __('Commission(%)') }}</th>
                                <th scope="col">{{ __('Commission Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php 
                        $key = 1 
                        @endphp
                            @foreach($transactions as $transaction)

                            <tr>
                                <td>{{ $key++ }}</td>
                                <td>{{ isset($transaction->ref_company_name) ? $transaction->ref_company_name->name : ''}}</td>
                                <td>{{ isset($transaction->company_name) ? $transaction->company_name->name : '' }}</td>
                                <td>{{ isset($transaction->plan_name) ? $transaction->plan_name->name : '' }}</td>
                                <td>{{isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$'}} {{ isset($transaction->plan_price) ? $transaction->plan_price : '' }}</td>
                                <td>{{ isset($transaction->commission) ? $transaction->commission : '' }}</td>
                                <td>{{isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$'}} {{ isset($transaction->amount) ? $transaction->amount : '' }}</td>

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