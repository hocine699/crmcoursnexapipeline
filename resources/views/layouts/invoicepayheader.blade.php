@php

    $logo = \App\Models\Utility::get_file('uploads/logo/');
    // $currantLang = $users->currentLanguage();
    $languages = \App\Models\Utility::languages();
    $seo_setting = App\Models\Utility::getSeoSetting();
    $footer_text = isset(\App\Models\Utility::settings()['footer_text']) ? \App\Models\Utility::settings()['footer_text'] : '';
    $setting = App\Models\Utility::colorset();
    $header_text = !empty(\App\Models\Utility::settings()['company_name']) ? \App\Models\Utility::settings()['company_name'] : env('APP_NAME');
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $site_setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Primary Meta Tags -->

<meta name="title" content="{{ $seo_setting['meta_keywords'] }}">
<meta name="description" content="{{ $seo_setting['meta_description'] }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ env('APP_URL') }}">
<meta property="og:title" content="{{ $seo_setting['meta_keywords'] }}">
<meta property="og:description" content="{{ $seo_setting['meta_description'] }}">
<meta property="og:image" content="{{ asset('uploads/metaevent/' . $seo_setting['meta_image']) }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ env('APP_URL') }}">
<meta property="twitter:title" content="{{ $seo_setting['meta_keywords'] }}">
<meta property="twitter:description" content="{{ $seo_setting['meta_description'] }}">
<meta property="twitter:image" content="{{ asset('uploads/metaevent/' . $seo_setting['meta_image']) }}">

@include('partials.admin.head')
@if (isset($site_setting['cust_darklayout']) && $site_setting['cust_darklayout'] == 'on')
    <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
@else
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
@endif

<body class="{{ $themeColor }}">

    <div class="container">
        <div class="main-content position-relative">
            <nav class="navbar navbar-main navbar-expand-lg navbar-border n-top-header">
                <div class="container align-items-lg-center">
                    <h4>{{ $header_text }}</h4>
                </div>
            </nav>
            <div class="page-content">
                @include('partials.admin.content')
            </div>
        </div>
    </div>
    @include('partials.admin.footer')
</body>

</html>
