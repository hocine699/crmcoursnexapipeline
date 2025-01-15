@extends('layouts.auth')

@section('page-title')
    {{ __('Reset Password') }}
@endsection

@php
    if ($lang == 'ar' || $lang == 'he') {
        $setting['SITE_RTL'] = 'on';
    }
    $lang = \App::getLocale('lang');
    $LangName = \App\Models\Languages::where('code', $lang)->first();
    if (empty($LangName)) {
        $LangName = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
    $settings = \App\Models\Utility::settings();

@endphp
@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text">
                    {{-- @if (array_key_exists($LangName->fullName, App\Models\Utility::flagOfCountryLogin()))
                    {{ App\Models\Utility::flagOfCountryLogin()[ucfirst($LangName->fullName)] }}
                @endif --}}
                {{ ucfirst($LangName->fullName) }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach (Utility::languages() as $code => $language)
                <a href="{{ route('password.request', $code) }}" tabindex="0"
                    class="dropdown-item @if ($lang == $code) text-primary @endif">
                    <span>{{ ucfirst($language) }}</span>
                </a>
            @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="card-body">
        <div class="">
            <h2 class="mb-3 f-w-600">{{ __('Forgot Password') }}
            </h2>
        </div>
        @if (session('status'))
            <small class="text-muted">{{ session('status') }}</small>
        @endif
        {{ Form::open(['route' => 'password.email', 'method' => 'post', 'id' => 'loginForm','class' => 'needs-validation', 'novalidate']) }}
        <div class="custom-login-form">
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email'),'required'=>'required']) }}
                @error('email')
                    <span class="invalid-email text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="d-grid">
                {{ Form::submit(__('Forgot Password'), ['class' => 'btn btn-primary btn-block mt-2', 'id' => 'saveBtn']) }}
            </div>
            <p class="my-4 text-center">{{ __('Back to?') }} <a href="{{ url('login', $lang) }}"
                    class="my-4 text-center text-primary">{{ __('Login') }}</a></p>
        </div>
        {{ Form::close() }}
</div>
@endsection
@push('custom-scripts')
    @if ($settings['recaptcha_module'] == 'yes')
    {!! NoCaptcha::renderJs() !!}

    @endif
@endpush
