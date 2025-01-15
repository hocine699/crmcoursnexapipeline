@php
    $settings = \App\Models\Utility::settings();
    $setting = \App\Models\Utility::colorset();

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = Utility::getValByName('company_favicon');

    $footer_text = isset($settings['footer_text']) ? $settings['footer_text'] : '';
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Salesy Saas- Business Sales CRM">
    <meta name="author" content="Rajodiya Infotech">
    <title>
        {{ Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'Salesy SaaS') }}
        - @yield('page-title')</title>

    <!-- Primary Meta Tags -->

    <meta name="title" content="{{ $settings['meta_keywords'] }}">
    <meta name="description" content="{{ $settings['meta_description'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $settings['meta_keywords'] }}">
    <meta property="og:description" content="{{ $settings['meta_description'] }}">
    <meta property="og:image" content="{{ asset('uploads/metaevent/' . $settings['meta_image']) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $settings['meta_keywords'] }}">
    <meta property="twitter:description" content="{{ $settings['meta_description'] }}">
    <meta property="twitter:image" content="{{ asset('uploads/metaevent/' . $settings['meta_image']) }}">


    {{-- <link rel="shortcut icon" href="{{ asset(Storage::url('logo/favicon.png')) }}"> --}}

    <link rel="icon"
        href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}"
        type="image/png" sizes="16x16">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">

    <!-- Dragulla -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">

    <!-- vendor css -->
    @if ($settings['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    @endif
    @if (isset($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}" id="main-style-link">
    @endif


    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @if (isset($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('css/custom-dark.css') }}">
    @endif

    @if (Auth::user())
        <meta name="url" content="{{ url('') . '/' . config('chatify.routes.prefix') }}"
            data-user="{{ Auth::user()->id }}">
    @endif

    <style>
        :root {
            --color-customColor: <?= $color ?>;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
    {{-- scripts --}}
    <script src="{{ asset('js/chatify/autosize.js') }}"></script>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

    {{-- styles --}}
    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css' />
    @stack('css-page')

</head>
