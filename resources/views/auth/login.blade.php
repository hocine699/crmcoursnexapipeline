@extends('layouts.auth')
@section('page-title')
    {{ __('Login') }}
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
                    <a href="{{ route('login', $code) }}" tabindex="0"
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
        <div>
            <h2 class="mb-3 f-w-600">{{ __('login') }}</h2>
        </div>
        @if (session('status'))
            <div>
                <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                    {{ __('Your Account is disable,please contact your Administrator') }}
                </div>
            </div>
        @endif
        <div class="custom-login-form">
            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror"
                        name="email" placeholder="{{ __('Enter your email') }}" required="required">
                    @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3 pss-field">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror"
                        name="password" placeholder="{{ __('Password') }}" required="required">
                    @error('password')
                        <span class="error invalid-password text-danger" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        @if (Route::has('password.request'))
                            <span>
                                <a href="{{ route('password.request', $lang) }}"
                                    tabindex="0">{{ __('Forgot Your Password?') }}</a>
                            </span>
                        @endif
                    </div>
                </div>
                {{-- @if ($settings['recaptcha_module'] == 'yes')
                    <div class="form-group mb-4">
                        {!! NoCaptcha::display() !!}
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif --}}
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
                            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"
                                class="form-control">
                            @error('g-recaptcha-response')
                                <span class="error small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif
                @endif
                <div class="d-grid">
                    <button class="btn btn-primary mt-2" type="submit">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
            @if (Utility::getValByName('signup_button') == 'on')
                <p class="my-4 text-center">{{ __("Don't have an account?") }}
                    <a href="{{ route('register', $lang) }}" tabindex="0">{{ __('Register') }}</a>
                </p>
            @endif
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script src="{{ asset('public/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".form_data").submit(function(e) {
                $(".login_button").attr("disabled", true);
                return true;
            });
        });
    </script>
    {{--
    @if ($settings['recaptcha_module'] == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif --}}
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
@endpush
