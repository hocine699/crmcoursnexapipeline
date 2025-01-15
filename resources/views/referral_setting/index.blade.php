@extends('layouts.admin')
@section('page-title')
{{ __('Referral Program') }}
@endsection
@section('title')
{{ __('Referral Program') }}
@endsection
@push('css-page')
<link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')
<script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
        });
    });
</script>
<script type="text/javascript">
    function enable() {
        const element = $('#customswitchv1-2').is(':checked');
        $('.referralDiv').addClass('disabledCookie');
        if (element==true) {
            $('.referralDiv').removeClass('disabledCookie');
        } else {
            $('.referralDiv').addClass('disabledCookie');
        }
    }
</script>
@endpush
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
            {{ Form::open(['route' => 'referral_setting.store', 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-10 col-md-10 col-sm-10">
                        <h5>{{ __('Settings') }}</h5>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 text-right">
                        <div class="form-check form-switch custom-switch-v1" onclick="enable()">
                            <input type="hidden" name="is_referral_enabled" value="off">
                            <input type="checkbox" class="form-check-input input-primary" id="customswitchv1-2" name="is_referral_enabled" {{ isset($referralSettings->is_referral_enabled) && $referralSettings->is_referral_enabled == 'on' ? 'checked' : 'off' }}>
                            <label class="form-check-label" style="padding-left: 10px;" for="customswitchv1-2"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body referralDiv {{  isset($referralSettings) && $referralSettings->is_referral_enabled == 'off' ? 'disabledCookie ' : '' }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('commission_per', __('Commission Percentage(%)'), ['class' => 'form-label']) }}<x-required></x-required>
                            {{ Form::text('commission_per',  isset($referralSettings) ? $referralSettings->commission_per : '', ['class' => 'form-control','required' => 'required','placeholder'=>__('Enter Commission Percentage')]) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ Form::label('minimum_amount', __('Minimum Threshold Amount'), ['class' => 'form-label']) }}<x-required></x-required>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ isset($payment['currency_symbol']) ? $payment['currency_symbol'] : '$' }}</span>
                                </div>
                                <input type="text" name="minimum_amount" class="form-control" required=required value="{{isset($referralSettings->minimum_amount) ? $referralSettings->minimum_amount : ''}}" placeholder="Minimum Threshold Amount">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {{ Form::label('guidelines', __('GuideLines'), ['class' => 'form-label']) }}
                            {{ Form::textarea('guidelines', isset($referralSettings) ? $referralSettings->guidelines : '', ['class' => 'form-control summernote', 'id' => 'mytextarea','placeholder'=>__('Write Here..')]) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <input class="btn btn-print-invoice btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
