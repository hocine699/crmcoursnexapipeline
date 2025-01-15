@extends('layouts.auth')

@section('page-title')
    {{ __('Register') }}
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
    $user = \Auth::user();
    $landingPageSettings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
    $keyArray = [];
    if (
        is_array(json_decode($landingPageSettings['menubar_page'])) ||
        is_object(json_decode($landingPageSettings['menubar_page']))
    ) {
        foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value) {
            if (
                in_array($value->menubar_page_name, ['Terms and Conditions']) ||
                in_array($value->menubar_page_name, ['Privacy Policy'])
            ) {
                $keyArray[] = $value->menubar_page_name;
            }
        }
    }

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
                    <a href="{{ route('register', [$refId, $code]) }}" tabindex="0"
                        class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                        <span>{{ ucFirst($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="card-body">
        <div class="">
            <h2 class="mb-3 f-w-600">{{ __('Register') }}</h2>
        </div>
        {{ Form::open(['route' => ['register', 'plan' => $plan], 'method' => 'post', 'id' => 'loginForm', 'class' => 'needs-validation', 'novalidate']) }}

        @if (session('status'))
            <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                {{ __('Email SMTP settings does not configured so please contact to your site admin.') }}
            </div>
        @endif
        <div class="custom-login-form">
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Name') }}</label>
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Name'),'required'=>'required']) }}
                @error('name')
                    <span class="invalid-name text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email'),'required'=>'required']) }}
                @error('email')
                    <span class="invalid-email text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Password') }}</label>
                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Your Password'),'required'=>'required']) }}
                @error('password')
                    <span class="invalid-password text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Confirm Password') }}</label>
                {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('Enter Your Confirm Password'),'required'=>'required']) }}
                @error('password_confirmation')
                    <span class="invalid-password_confirmation text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            @if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes')
                @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2')
                    <div class="form-group mb-4">
                        {!! NoCaptcha::display($settings['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []) !!}
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @else
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif
            @endif
            @if (count($keyArray) > 0)
                <div class="form-check custom-checkbox">
                    <input type="checkbox" class="form-check-input @error('terms_condition_check') is-invalid @enderror"
                        id="termsCheckbox" name="terms_condition_check">
                    <input type="hidden" name="terms_condition" id="terms_condition" value="off">

                    <label class="text-sm" for="terms_condition_check">{{ __('I agree to the ') }}
                        @foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value)
                            @if (in_array($value->menubar_page_name, ['Terms and Conditions']) && isset($value->template_name))
                                <a href="{{ $value->template_name == 'page_content' ? route('custom.page', $value->page_slug) : $value->page_url }}"
                                    target="_blank">{{ $value->menubar_page_name }}</a>
                            @endif
                        @endforeach
                        @if (count($keyArray) == 2)
                            {{ __('and the ') }}
                        @endif
                        @foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value)
                            @if (in_array($value->menubar_page_name, ['Privacy Policy']) && isset($value->template_name))
                                <a href="{{ $value->template_name == 'page_content' ? route('custom.page', $value->page_slug) : $value->page_url }}"
                                    target="_blank">{{ $value->menubar_page_name }}</a>
                            @endif
                        @endforeach
                    </label>
                </div>
                @error('terms_condition_check')
                    <span class="error invalid-terms_condition_check text-danger" role="alert">
                        <strong>{{ __('Please check this box if you want to proceed.') }}</strong>
                    </span>
                @enderror
            @endif
            <input type="hidden" class="form-control" name="ref_id" value="{{ $refId }}">

            <div class="d-grid">
                {{ Form::submit(__('Register'), ['class' => 'btn btn-primary btn-block mt-2', 'id' => 'saveBtn']) }}
            </div>
            <p class="my-4 text-center">{{ __('Already have an account?') }} <a href="{{ route('login') }}"
                    class="my-4 text-center text-primary"> {{ __('Login') }}</a></p>
        </div>
        {{ Form::close() }}
    </div>
@endsection
@push('custom-scripts')
    {{-- @if ($settings['recaptcha_module'] == 'yes')
        {!! NoCaptcha::renderJs() !!}

    @endif --}}
    @if (count($keyArray) > 0)
        <script>
            $('#loginForm').on('submit', function() {
                if ($('#termsCheckbox').prop('checked')) {
                    $('#terms_condition').val('on');
                }
            });
        </script>
        @if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes')
            @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2')
                {!! NoCaptcha::renderJs() !!}
            @else
                <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['google_recaptcha_key'] }}"></script>
                <script>
                    $(document).ready(function() {
                        grecaptcha.ready(function() {
                            grecaptcha.execute('{{ $settings['google_recaptcha_key'] }}', {
                                action: 'submit'
                            }).then(function(token) {
                                $('#g-recaptcha-response').val(token);
                            });
                        });
                    });
                </script>
            @endif
        @endif
    @endif
@endpush
