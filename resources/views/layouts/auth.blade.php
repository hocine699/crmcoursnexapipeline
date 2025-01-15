@php
    $setting = Utility::settings();
    $settings = \App\Models\Utility::colorset();

    $color = !empty($settings['color']) ? $settings['color'] : 'theme-3';

    if (isset($settings['color_flag']) && $settings['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = $setting['company_favicon'] ?? '';
    $company_logo = \App\Models\Utility::GetLogo();
    $users = \Auth::user();

    $lang = \App::getLocale('lang');
    if ($lang == 'ar' || $lang == 'he') {
        $setting['SITE_RTL'] = 'on';
    }
    $LangName = \App\Models\Languages::where('code', $lang)->first();
    if (empty($LangName)) {
        $LangName = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">


<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Salesy Saas- Business Sales CRM" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />
    <title>
        {{ Utility::getValByName('header_text') ? Utility::getValByName('header_text') : config('app.name', 'Salesy SaaS') }}
        - @yield('page-title')</title>
    <!-- Primary Meta Tags -->

    <meta name="title" content="{{ $setting['meta_keywords'] }}">
    <meta name="description" content="{{ $setting['meta_description'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $setting['meta_keywords'] }}">
    <meta property="og:description" content="{{ $setting['meta_description'] }}">
    <meta property="og:image" content="{{ asset('uploads/metaevent/' . $setting['meta_image']) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $setting['meta_keywords'] }}">
    <meta property="twitter:description" content="{{ $setting['meta_description'] }}">
    <meta property="twitter:image" content="{{ asset('uploads/metaevent/' . $setting['meta_image']) }}">
    <link rel="icon" href="{{ $logo . '/favicon.png' }}" type="image/png">

    @if ($setting['cust_darklayout'] == 'on')
        <style>
            .g-recaptcha {
                filter: invert(1) hue-rotate(180deg) !important;
            }
        </style>
    @endif
    <style>
        :root {
            --color-customColor: <?= $color ?>;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    @if ($setting['cust_darklayout'] == 'on')
        @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @endif
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        @endif
    @endif

    @if (isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-rtl.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth.css') }}" id="main-style-link">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-dark.css') }}" id="main-style-link">
    @endif

</head>

<body class="{{ $themeColor }}">
    <div class="custom-login">
        <div class="login-bg-img">
            <img src="{{ isset($setting['color_flag']) && $setting['color_flag'] == 'false' ? asset('assets/images/auth/' . $color . '.svg') : asset('assets/images/auth/theme-3.svg')  }}" class="login-bg-1">
            <img src="{{ asset('assets/images/auth/common.svg') }}" class="login-bg-2">
        </div>
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">
            <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">
                        <div class="navbar-brand">
                            <a href="#">
                                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') . '?' . time() }}"
                                    alt="{{ config('app.name', 'Salesy Saas') }}" alt="logo" loading="lazy"
                                    class="logo" />
                            </a>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarlogin">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                @include('landingpage::layouts.buttons')
                                @yield('language-bar')
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        @yield('content')
                    </div>
                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <span>&copy; {{ date('Y') }}
                                    {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'Salesy Saas') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>

<script src="{{ asset('assets/js/vendor-all.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

@stack('custom-scripts')

@if ($setting['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif


</html>
