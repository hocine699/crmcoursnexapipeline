@php

$logo=\App\Models\Utility::get_file('uploads/logo/');
$seo_setting = App\Models\Utility::getSeoSetting();

    // $currantLang = $users->currentLanguage();
    $languages=\App\Models\Utility::languages();
    $footer_text=isset(\App\Models\Utility::settings()['footer_text']) ? \App\Models\Utility::settings()['footer_text'] : '';
    $setting = App\Models\Utility::colorset();
    $header_text = (!empty(\App\Models\Utility::settings()['company_name'])) ? \App\Models\Utility::settings()['company_name'] : env('APP_NAME');
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$setting['SITE_RTL'] == 'on'?'rtl':''}}">

<head>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
    />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->

    <meta name="title" content="{{$seo_setting['meta_keywords']}}">
    <meta name="description" content="{{$seo_setting['meta_description']}}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{env('APP_URL')}}">
    <meta property="og:title" content="{{$seo_setting['meta_keywords']}}">
    <meta property="og:description" content="{{$seo_setting['meta_description']}}">
    <meta property="og:image" content="{{asset('uploads/metaevent/'.$seo_setting['meta_image'])}}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{env('APP_URL')}}">
    <meta property="twitter:title" content="{{$seo_setting['meta_keywords']}}">
    <meta property="twitter:description" content="{{$seo_setting['meta_description']}}">
    <meta property="twitter:image" content="{{asset('uploads/metaevent/'.$seo_setting['meta_image'])}}">

    <!-- Favicon icon -->
    <link rel="icon" href="{{$logo.'/'.(isset($favicon) && !empty($favicon)?$favicon:'favicon.png')}}" type="image/x-icon" />
     @stack('head')
     <link rel="stylesheet" href="{{asset ('assets/css/plugins/animate.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
     <link rel="stylesheet" href="{{asset ('assets/fonts/tabler-icons.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

     <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">

     <!-- Dragulla -->
     <link rel="stylesheet" href="{{ asset('assets/css/plugins/dragula.min.css') }}">


     <link rel="stylesheet" href="{{asset('assets/css/plugins/flatpickr.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/dropzone.min.css')}}">
     <link rel="stylesheet" href="{{ asset('public/libs/select2/dist/css/select2.min.css') }}">
     <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
     <link rel="stylesheet" href="{{ asset('css/custom.css') }}">


     @if(Auth::user())
         <meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">
     @endif


     {{-- scripts --}}
     <script src="{{ asset('js/chatify/autosize.js') }}"></script>
     {{--<script src="{{ asset('js/app.js') }}"></script>--}}
     <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>

     {{-- styles --}}
     <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>

     @if ($setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif
    @if( isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @endif

</head>
<body class="{{$themeColor}}">
  <!-- [ Pre-loader ] start -->
  <!-- [ Mobile header ] End -->



<!-- [ Main Content ] start -->
<div class="container">
    <div class="dash-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header" >
              <div class="page-block">
                  <div class="row align-items-center">
                      <div class="col-md-12 mt-5 mb-4">
                          <div class="d-block d-sm-flex align-items-center justify-content-between">
                              <div>
                                  {{-- <div class="page-header-title">
                                      <h4 class="m-b-10">@yield('title')</h4>
                                  </div> --}}
                                  {{-- <ul class="breadcrumb">
                                      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('Home')}}</a></li>
                                          @yield('breadcrumb')
                                  </ul> --}}

                                 <!--  <ul class="breadcrumb">
                                      <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                      <li class="breadcrumb-item">General Statistics</li>
                                  </ul> -->
                              </div>
                              <div>
                                @yield('action-btn')
                              </div>

                          </div>
                      </div>
                  </div>
              </div>
          </div>

        <!-- <div class="row"> -->
               @yield('content')

        <!-- </div> -->

    </div>
</div>

<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="commonModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelCommanModelLabel"></h5>
                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/site.core.js')}}"></script>
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{asset ('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/dash.js') }}"></script>
<script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>

<script src="{{asset ('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{asset ('js/plugins/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('js/letter.avatar.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.min.js')}}"></script>

<script src="{{ asset('assets/js/plugins/bootstrap-switch-button.min.js') }}"></script>
<script src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script src="{{ asset('libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
{{-- <script src="{{asset ('assets/js/pages/ac-alert.js') }}"></script> --}}
<script src="{{asset('assets/js/plugins/datepicker-full.min.js')}}"></script>
{{-- <script type="text/javascript" src="{{ asset('js/jquery-1.11.1.min.js')}}"></script> --}}
<script>
(function () {
  const d_week = new Datepicker(document.querySelector('#pc-datepicker-2_modal'), {
    buttonClass: 'btn',
  });
})();
</script>

<script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>

<script src="{{ asset('libs/select2/dist/js/select2.min.js')}}"></script>


<script src="{{asset ('assets/js/plugins/simple-datatables.js') }}"></script>

<script src="{{ asset('js/custom.js') }}"></script>

@stack('script-page')

<!-- <script>
  if ($(".pc-dt-simple").length) {
    const dataTable = new simpleDatatables.DataTable(".pc-dt-simple");
  }
</script> -->



</body>

</html>
