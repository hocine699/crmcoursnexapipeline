@extends('layouts.admin')
@php
$settings = Utility::settings();
@endphp

@section('title')
{{ __('Referral Program') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Referral Program') }}</li>
@endsection

@push('script-page')
<script>

    $(document).ready(function() {
        // Attach a click event handler to an element with id "myButton"    
        $('.cp_link').on('click', function() {
            var value = $(this).attr('data-link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $temp.remove();
                show_toastr('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
        });
    });
</script>
@endpush

@section('content')
<div class="row">
    <div class="col-3">
        @include('layouts.referral_setup')
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-10 col-md-10 col-sm-10">
                        <h5>{{ __('Guideline') }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="border p-3">
                                <h4>{{ __('Refer ') }}{{ env('APP_NAME') }}{{ __(' and earn') }} {{ isset($settings['site_currency_symbol']) ? $settings['site_currency_symbol'] : '$' }} {{ isset($referralSettings->minimum_amount) ? $referralSettings->minimum_amount : '' }} {{ __('per paid signup!') }}</h4>
                                <p>{!! isset($referralSettings->guidelines) ? $referralSettings->guidelines : '' !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-md-6 mt-5">
                        <h4 class="text-center">{{ __('Share Your Link') }}</h4>
                        <div class="d-flex justify-content-between">
                            <a href="#!" class="btn btn-sm btn-light-primary w-100 cp_link" data-link="{{ route('register', ['ref_id' => \Auth::user()->referral_code]) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Click to copy business link">
                                {{ route('register', ['ref' => \Auth::user()->referral_code]) }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy ms-1">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @if(isset($referralSettings) && $referralSettings->is_referral_enabled == 'off' || (empty($referralSettings)))
                    <h6 class="text-end text-danger text-md mt-2">{{ __('Note : super admin has disabled the referral program.') }}</h6>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection