@extends('layouts.admin')
@php
    $settings = App\Models\Utility::settings();
    $company_setting = App\Models\Utility::settingsById(Auth::user()->creatorId());

    $setting = \App\Models\Utility::colorset();
    // $logo = asset(Storage::url('uploads/logo/'));
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-4';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }

    $company_favicon = \App\Models\Utility::getValByName('company_favicon');
    $dark_logo = Utility::getValByName('company_logo_dark');
    $light_logo = Utility::getValByName('company_logo_light');
    $company_logo = \App\Models\Utility::GetLogo();
    $lang = \App\Models\Utility::getValByName('default_language');
    $EmailTemplates = App\Models\EmailTemplate::all();
    $flag = !empty($setting['color_flag']) ? $setting['color_flag'] : '';

    $file_type = config('files_types');

    $local_storage_validation = $settings['local_storage_validation'];
    $local_storage_validations = explode(',', $local_storage_validation);

    $s3_storage_validation = $settings['s3_storage_validation'];
    $s3_storage_validations = explode(',', $s3_storage_validation);

    $wasabi_storage_validation = $settings['wasabi_storage_validation'];
    $wasabi_storage_validations = explode(',', $wasabi_storage_validation);

    $SITE_RTL = env('SITE_RTL');
    if ($SITE_RTL == '') {
        $SITE_RTL == 'off';
    }

    $meta_image = \App\Models\Utility::get_file('uploads/metaevent/');
    $google_recaptcha_version = ['v2' => __('v2'), 'v3' => __('v3')];

@endphp

@push('css-page')
    @if ($color == 'theme-1')
        <style>
            .btn-check:checked+.btn-outline-success,
            .btn-check:active+.btn-outline-success,
            .btn-outline-success:active,
            .btn-outline-success.active,
            .btn-outline-success.dropdown-toggle.show {
                color: #ffffff;
                background: linear-gradient(141.55deg, rgba(81, 69, 157, 0) 3.46%, rgba(255, 58, 110, 0.6) 99.86%), #51459d !important;
                border-color: #51459d !important;

            }

            .btn-outline-success:hover {
                color: #ffffff;
                background: linear-gradient(141.55deg, rgba(81, 69, 157, 0) 3.46%, rgba(255, 58, 110, 0.6) 99.86%), #51459d !important;
                border-color: #51459d !important;
            }

            .btn.btn-outline-success {
                color: #51459d;
                border-color: #51459d !important;
            }
        </style>
    @endif
    @if ($color == 'theme-2')
        <style>
            .btn-check:checked+.btn-outline-success,
            .btn-check:active+.btn-outline-success,
            .btn-outline-success:active,
            .btn-outline-success.active,
            .btn-outline-success.dropdown-toggle.show {
                color: #ffffff;
                background: linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #4ebbd3 99.86%)#1f3996 !important;
                border-color: #1F3996 !important;

            }

            .btn-outline-success:hover {
                color: #ffffff;
                background: linear-gradient(141.55deg, rgba(240, 244, 243, 0) 3.46%, #4ebbd3 99.86%)#1f3996 !important;
                border-color: #1F3996 !important;
            }

            .btn.btn-outline-success {
                color: #1F3996;
                border-color: #1F3996 !important;
            }
        </style>
    @endif

    @if ($color == 'theme-4')
        <style>
            .btn-check:checked+.btn-outline-success,
            .btn-check:active+.btn-outline-success,
            .btn-outline-success:active,
            .btn-outline-success.active,
            .btn-outline-success.dropdown-toggle.show {
                color: #ffffff;
                background-color: #584ed2 !important;
                border-color: #584ed2 !important;

            }

            .btn-outline-success:hover {
                color: #ffffff;
                background-color: #584ed2 !important;
                border-color: #584ed2 !important;
            }

            .btn.btn-outline-success {
                color: #584ed2;
                border-color: #584ed2 !important;
            }
        </style>
    @endif

    @if ($color == 'theme-3')
        <style>
            .btn-check:checked+.btn-outline-success,
            .btn-check:active+.btn-outline-success,
            .btn-outline-success:active,
            .btn-outline-success.active,
            .btn-outline-success.dropdown-toggle.show {
                color: #ffffff;
                background-color: #6fd943 !important;
                border-color: #6fd943 !important;

            }

            .btn-outline-success:hover {
                color: #ffffff;
                background-color: #6fd943 !important;
                border-color: #6fd943 !important;
            }

            .btn.btn-outline-success {
                color: #6fd943;
                border-color: #6fd943 !important;
            }
        </style>
    @endif
@endpush
@push('script-page')
    <script type="text/javascript">
        // $(document).on("click", ".email-template-checkbox", function() {
        //     var chbox = $(this);
        //     $.ajax({
        //         url: chbox.attr('data-url'),
        //         data: {
        //             _token: $('meta[name="csrf-token"]').attr('content'),
        //             status: chbox.val()
        //         },
        //         type: 'post',
        //         success: function(response) {
        //             if (response.is_success) {
        //                 toastr('Success', response.success, 'success');
        //                 if (chbox.val() == 1) {
        //                     $('#' + chbox.attr('id')).val(0);
        //                 } else {
        //                     $('#' + chbox.attr('id')).val(1);
        //                 }
        //             } else {
        //                 toastr('Error', response.error, 'error');
        //             }
        //         },
        //         error: function(response) {
        //             response = response.responseJSON;
        //             if (response.is_success) {
        //                 toastr('Error', response.error, 'error');
        //             } else {
        //                 toastr('Error', response, 'error');
        //             }
        //         }
        //     })
        // });
    </script>


    <script>
        function check_theme(color_val) {
            $('#theme_color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
        $(document).ready(function() {
            $('.list-group-item').on('click', function() {
                var href = $(this).attr('data-href');
                $('.tabs-card').addClass('d-none');
                $(href).removeClass('d-none');
                $('#tabs .list-group-item').removeClass('text-primary');
                // $(this).addClass('text-primary');
            });
        });

        function check_theme(color_val) {
            $('#theme_color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
    </script>
    <script>
        $(document).on("change", "select[name='quote_template'], input[name='quote_color']", function() {
            var template = $("select[name='quote_template']").val();
            var color = $("input[name='quote_color']:checked").val();
            $('#quote_frame').attr('src', '{{ url('/quote/preview') }}/' + template + '/' + color);
        });
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '{{ url('/invoice/preview') }}/' + template + '/' + color);
        });
        $(document).on("change", "select[name='salesorder_template'], input[name='salesorder_color']", function() {
            var template = $("select[name='salesorder_template']").val();
            var color = $("input[name='salesorder_color']:checked").val();
            $('#salesorder_frame').attr('src', '{{ url('/salesorder/preview') }}/' + template + '/' + color);
        });
    </script>

    <script>
        $(document).on("click", '.send_email', function(e) {
            e.preventDefault();
            var title = $(this).attr('data-title');

            var size = 'md';
            var url = $(this).attr('data-url');
            if (typeof url != 'undefined') {
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);
                $("#commonModal").modal('show');

                $.post(url, {
                    _token: '{{ csrf_token() }}',
                    mail_driver: $("#mail_driver").val(),
                    mail_host: $("#mail_host").val(),
                    mail_port: $("#mail_port").val(),
                    mail_username: $("#mail_username").val(),
                    mail_password: $("#mail_password").val(),
                    mail_encryption: $("#mail_encryption").val(),
                    mail_from_address: $("#mail_from_address").val(),
                    mail_from_name: $("#mail_from_name").val(),

                }, function(data) {
                    $('#commonModal .modal-body').html(data);
                });
            }
        });


        $(document).on('submit', '#test_email', function(e) {

            e.preventDefault();
            $("#email_sending").show();
            var post = $(this).serialize();
            var url = $(this).attr('action');
            $.ajax({
                type: "post",
                url: url,
                data: post,
                cache: false,

                success: function(data) {


                    if (data.is_success) {
                        show_toastr('Success', data.message, 'success');
                    } else {
                        show_toastr('Error', data.message, 'error');
                    }
                    $("#email_sending").hide();
                    $('#commonModal').modal('hide');
                },
                complete: function() {
                    $('#test_email .btn-create').removeAttr('disabled');
                },
            });
        });
    </script>

    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })
        $(".list-group-item").click(function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });

        $(document).on('change', '[name=storage_setting]', function() {
            if ($(this).val() == 's3') {
                $('.s3-setting').removeClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').addClass('d-none');
            } else if ($(this).val() == 'wasabi') {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').removeClass('d-none');
                $('.local-setting').addClass('d-none');
            } else {
                $('.s3-setting').addClass('d-none');
                $('.wasabi-setting').addClass('d-none');
                $('.local-setting').removeClass('d-none');
            }
        });
    </script>
    <script type="text/javascript">
        function enablecookie() {
            const element = $('#enable_cookie').is(':checked');
            $('.cookieDiv').addClass('disabledCookie');
            if (element == true) {
                $('.cookieDiv').removeClass('disabledCookie');
                $("#cookie_logging").attr('checked', true);
            } else {
                $('.cookieDiv').addClass('disabledCookie');
                $("#cookie_logging").attr('checked', false);
            }
        }
    </script>
    <script>
        $('.colorPicker').on('click', function(e) {
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass('custom-color');
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');
            const input = document.getElementById("color-picker");
            setColor();
            input.addEventListener("input", setColor);

            function setColor() {
                document.documentElement.style.setProperty('--color-customColor', input.value);
            }

            $(`input[name='color_flag`).val('true');
        });

        $('.themes-color-change').on('click', function() {

            $(`input[name='color_flag`).val('false');

            var color_val = $(this).data('value');
            $('body').removeClass('custom-color');
            if (/^theme-\d+$/) {
                $('body').removeClassRegex(/^theme-\d+$/);
            }
            $('body').addClass(color_val);
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $('.colorPicker').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);
        });

        $.fn.removeClassRegex = function(regex) {
            return $(this).removeClass(function(index, classes) {
                return classes.split(/\s+/).filter(function(c) {
                    return regex.test(c);
                }).join(' ');
            });
        };
    </script>
@endpush
@section('page-title')
    {{ __('Settings') }}
@endsection
@section('title')
    {{ __('Settings') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection
@section('action-btn')
@endsection


@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">

                            @if (\Auth::user()->type == 'super admin')
                                <a href="#brand-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (\Auth::user()->type == 'super admin')
                                <a href="#email-settings"
                                    class="list-group-item list-group-item-action dash-link border-0">{{ __('Email Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'super admin')
                                <a href="#pusher-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Pusher Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#site-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Site Settings') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#company-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#company-email-setting"
                                    class="list-group-item list-group-item-action border-0">{{ __('Email Settings') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#system-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('System Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#quote-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Quote Settings') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#invoice-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Invoice Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#sales-order-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Sales Order Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin')
                                <a href="#payment-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#twilio-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Twilio Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'super admin')
                                <a href="#recaptcha-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Recaptcha Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'owner')
                                <a href="#email-notification-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif

                            @if (\Auth::user()->type == 'super admin')
                                <a href="#storage-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Storage Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'owner')
                                <a href="#google-calender"
                                    class="list-group-item list-group-item-action border-0">{{ __('Google Calendar Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'owner')
                                <a href="#webhook-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'super admin')
                                <a href="#SEO"
                                    class="list-group-item list-group-item-action border-0">{{ __('SEO Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'super admin')
                                <a href="#cache"
                                    class="list-group-item list-group-item-action border-0">{{ __('Cache Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'super admin')
                                <a href="#cookie"
                                    class="list-group-item list-group-item-action border-0">{{ __('Cookie Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (\Auth::user()->type == 'super admin')
                                <a href="#pills-chatgpt-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Chat GPT Key Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    @if (\Auth::user()->type == 'super admin')
                        <div id="brand-settings" class="card">
                            <div class="card-header">
                                <h5>{{ __('Brand Settings') }}</h5>
                                <small class="text-muted">{{ __('Edit your brand details') }}</small>
                            </div>
                            {{ Form::model($settings, ['route' => 'business.setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="small-title">{{ __('Dark Logo') }}</h5>
                                            </div>
                                            <div class="card-body setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="logo-content logo-set-bg py-2" style="height:65px">
                                                            {{-- <a href="{{ asset(Storage::url('uploads/logo/logo-dark.png')) }}" target="_blank">
                                                                    <img id="blah4" alt="your image" src="{{ asset(Storage::url('uploads/logo/logo-dark.png')) }}" width="170px" class="">
                                                                </a> --}}
                                                            <a href="{{ $logo . 'logo-dark.png' . '?' . time() }}"
                                                                target="_blank" style="height: 50px; width:150px;">
                                                                <img id="blah4" alt="your image"
                                                                    src="{{ $logo . 'logo-dark.png' . '?' . time() }}"
                                                                    style="height: 50px; width:150px;" class="big-logo">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">

                                                        <div class="choose-files mt-5">
                                                            <label for="logo_dark">
                                                                <div class=" bg-primary"> <i
                                                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                <input type="file"name="logo_dark" id="logo_dark"
                                                                    class="form-control file"
                                                                    data-filename="company_logo_update"
                                                                    onchange="document.getElementById('blah4').src = window.URL.createObjectURL(this.files[0])">

                                                                {{-- <input type="file" name="logo_dark" id="logo_dark" class="form-control file" data-filename="company_logo_update"> --}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="small-title">{{ __('Light Logo') }}</h5>
                                            </div>
                                            <div class="card-body setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="logo-content logo-set-bg py-2" style="height:65px">
                                                            {{-- <a href="{{ asset(Storage::url('uploads/logo/logo-light.png')) }}" target="_blank">
                                                                    <img id="blah5" alt="your image" src="{{ asset(Storage::url('uploads/logo/logo-light.png')) }}" width="170px" class="img_setting">
                                                                </a> --}}
                                                            <a href="{{ $logo . 'logo-light.png' . '?' . time() }}"
                                                                target="_blank" style="height: 50px; width:150px;">
                                                                <img id="blah5" alt="your image"
                                                                    src="{{ $logo . 'logo-light.png' . '?' . time() }}"
                                                                    style="height: 50px; width:150px;"
                                                                    class="img_setting">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">

                                                        <div class="choose-files mt-5">
                                                            <label for="logo_light">
                                                                <div class=" bg-primary"> <i
                                                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                <input type="file"name="logo_light" id="logo_light"
                                                                    class="form-control file"
                                                                    data-filename="company_logo_update"
                                                                    onchange="document.getElementById('blah5').src = window.URL.createObjectURL(this.files[0])">

                                                                {{-- <input type="file" name="logo_light" id="logo_light" class="form-control file" data-filename="company_logo_update"> --}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="small-title">{{ __('Favicon') }}</h5>
                                            </div>
                                            <div class="card-body setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="logo-content logo-set-bg py-2" style="height:65px">
                                                            {{-- <a href="{{ asset(Storage::url('uploads/logo/favicon.png')) }}" target="_blank">
                                                                    <img id="blah6" alt="your image" src="{{ $logo . '/' . (isset($favicon) && !empty($favicon) ? $favicon : 'favicon.png') }}"  width="50px" class="img_setting">
                                                                </a> --}}
                                                            <a href="{{ $logo . 'favicon.png' . '?' . time() }}"
                                                                target="_blank">
                                                                <img id="blah6" alt="your image"
                                                                    src="{{ $logo . 'favicon.png' . '?' . time() }}"
                                                                    width="50px" class="img_setting">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="choose-files mt-5">
                                                            <label for="favicon">
                                                                <div class=" bg-primary"> <i
                                                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                </div>
                                                                <input type="file"name="favicon" id="favicon"
                                                                    class="form-control file"
                                                                    data-filename="company_logo_update"
                                                                    onchange="document.getElementById('blah6').src = window.URL.createObjectURL(this.files[0])">
                                                                {{-- <input type="file" name="favicon" id="favicon" class="form-control file" data-filename="company_logo_update"> --}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            {{ Form::label('title_text', __('Title Text'), ['class' => 'form-label']) }}
                                            {{ Form::text('title_text', null, ['class' => 'form-control', 'placeholder' => __('Title Text')]) }}
                                            @error('title_text')
                                                <span class="invalid-title_text" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        @if (\Auth::user()->type == 'super admin')
                                            <div class="form-group col-md-4">
                                                {{ Form::label('footer_text', __('Footer Text'), ['class' => 'form-label']) }}
                                                {{ Form::text('footer_text', null, ['class' => 'form-control', 'placeholder' => __('Footer Text')]) }}
                                                @error('footer_text')
                                                    <span class="invalid-footer_text" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                <div class="changeLanguage">
                                                    <select name="default_language" id="default_language"
                                                        class="form-control custom-select">
                                                        @foreach (\App\Models\Utility::languages() as $code => $language)
                                                            <option @if ($lang == $code) selected @endif
                                                                value="{{ $code }}">{{ ucfirst($language) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3">
                                                        <label
                                                            class="form-label mb-1">{{ __('Enable Landing Page') }}</label>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class=""
                                                                name="display_landing_page" id="display_landing_page"
                                                                {{ $settings['display_landing_page'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label mb-1"
                                                                for="display_landing_page"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">

                                                        {{ Form::label('verified_button', __('Email Verification'), ['class' => 'form-label']) }}
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class=""
                                                                name="verified_button" id="verified_button"
                                                                {{ Utility::getValByName('verified_button') == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="verified_button"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>




                                            <div class="col-4">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        {{ Form::label('SITE_RTL', __('Enable RTL'), ['class' => 'form-label']) }}
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class="" name="SITE_RTL"
                                                                id="SITE_RTL"{{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label" for="SITE_RTL"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-4">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        {{ Form::label('signup_button', __('Enable Sign-Up Page'), ['class' => 'form-label']) }}
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class=""
                                                                name="signup_button" id="signup_button"
                                                                {{ Utility::getValByName('signup_button') == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="signup_button"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                                    <div class="setting-card setting-logo-box p-3">
                                        <div class="row">
                                            <div class="col-lg-4 col-xl-4 col-md-4">
                                                <h6 class="mt-2">
                                                    <i data-feather="credit-card"
                                                        class="me-2"></i>{{ __('Primary color settings') }}
                                                </h6>

                                                <hr class="my-2" />
                                                <div class="color-wrp">
                                                    <div class="theme-color themes-color">
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                            data-value="theme-1"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-1"{{ $color == 'theme-1' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }}"
                                                            data-value="theme-2"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-2"{{ $color == 'theme-2' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                            data-value="theme-3"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-3"{{ $color == 'theme-3' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                            data-value="theme-4"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-4"{{ $color == 'theme-4' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                            data-value="theme-5"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-5"{{ $color == 'theme-5' ? 'checked' : '' }}>
                                                        <br>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                            data-value="theme-6"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-6"{{ $color == 'theme-6' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                            data-value="theme-7"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-7"{{ $color == 'theme-7' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                            data-value="theme-8"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-8"{{ $color == 'theme-8' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                            data-value="theme-9"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-9"{{ $color == 'theme-9' ? 'checked' : '' }}>
                                                        <a href="#!"
                                                            class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                            data-value="theme-10"></a>
                                                        <input type="radio" class="theme_color d-none" name="color"
                                                            value="theme-10"{{ $color == 'theme-10' ? 'checked' : '' }}>
                                                    </div>
                                                    <div class="color-picker-wrp ">
                                                        <input type="color" value="{{ $color ? $color : '' }}"
                                                            class="colorPicker {{ isset($flag) && $flag == 'true' ? 'active_color' : '' }}"
                                                            name="custom_color" id="color-picker">
                                                        <input type='hidden' name="color_flag"
                                                            value={{ isset($flag) && $flag == 'true' ? 'true' : 'false' }}>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-xl-4 col-md-4">
                                                <h6 class="mt-2">
                                                    <i data-feather="layout"
                                                        class="me-2"></i>{{ __('Sidebar settings') }}
                                                </h6>
                                                <hr class="my-2" />
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" id="cust-theme-bg"
                                                        name="cust_theme_bg"
                                                        {{ !empty($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on' ? 'checked' : '' }} />
                                                    <label class="form-check-label f-w-600 pl-1"
                                                        for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-xl-4 col-md-4">
                                                <h6 class="mt-2">
                                                    <i data-feather="sun" class="me-2"></i>{{ __('Layout settings') }}
                                                </h6>
                                                <hr class="my-2" />
                                                <div class="form-check form-switch mt-2">
                                                    <input type="checkbox" class="form-check-input" id="cust-darklayout"
                                                        name="cust_darklayout"{{ !empty($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on' ? 'checked' : '' }} />
                                                    <label class="form-check-label f-w-600 pl-1"
                                                        for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                            </div>
                            {{ Form::close() }}
                        </div>

                        <div id="email-settings" class="card">
                            <div class="card-header">
                                <h5>{{ __('Email Settings') }}</h5>
                                <small
                                    class="text-muted">{{ __('This SMTP will be used for system-level email sending. Additionally, if a company user does not set their SMTP, then this SMTP will be used for sending emails.') }}</small>
                            </div>
                            {{ Form::open(['route' => 'email.setting', 'method' => 'post','class'=>'mb-0']) }}
                            <div class="card-body">
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_driver"
                                                class="col-form-label text-dark">{{ __('Mail Driver') }}</label>
                                            <input type="text" name="mail_driver" id="mail_driver"
                                                class="form-control {{ $errors->has('mail_driver') ? 'is-invalid' : '' }}"
                                                value="{{ !isset($settings['mail_driver']) || is_null($settings['mail_driver']) ? '' : $settings['mail_driver'] }}"
                                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_placeholder') }}" />
                                            @if ($errors->has('mail_driver'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_driver') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_host"
                                                class="col-form-label text-dark">{{ __('Mail Host') }}</label>
                                            <input type="text" name="mail_host" id="mail_host"
                                                class="form-control {{ $errors->has('mail_host') ? 'is-invalid' : '' }}"
                                                value="{{ !isset($settings['mail_host']) || is_null($settings['mail_host']) ? '' : $settings['mail_host'] }}"
                                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}" />
                                            @if ($errors->has('mail_host'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_host') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_port"
                                                class="col-form-label text-dark">{{ __('Mail Port') }}</label>
                                            <input type="number" name="mail_port" id="mail_port"
                                                class="form-control {{ $errors->has('mail_port') ? 'is-invalid' : '' }}"
                                                value="{{ isset($settings['mail_port']) ? $settings['mail_port'] : '' }}"
                                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}" />
                                            @if ($errors->has('mail_port'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_port') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_username"
                                                class="col-form-label text-dark">{{ __('Mail Username') }}</label>
                                            <input type="text" name="mail_username" id="mail_username"
                                                class="form-control {{ $errors->has('mail_username') ? 'is-invalid' : '' }}"
                                                value="{{ isset($settings['mail_username']) ? $settings['mail_username'] : '' }}"
                                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}" />
                                            @if ($errors->has('mail_username'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_username') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_password"
                                                class="col-form-label text-dark">{{ __('Mail Password') }}</label>
                                            <input type="text" name="mail_password" id="mail_password"
                                                class="form-control {{ $errors->has('mail_password') ? 'is-invalid' : '' }}"
                                                value="{{ isset($settings['mail_password']) ? $settings['mail_password'] : '' }}"
                                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}" />
                                            @if ($errors->has('mail_password'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_password') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_encryption"
                                                class="col-form-label text-dark">{{ __('Mail Encryption') }}</label>
                                            <input type="text" name="mail_encryption" id="mail_encryption"
                                                class="form-control {{ $errors->has('mail_encryption') ? 'is-invalid' : '' }}"
                                                value="{{ isset($settings['mail_encryption']) ? $settings['mail_encryption'] : '' }}"
                                                placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}" />
                                            @if ($errors->has('mail_encryption'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_encryption') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_from_address"
                                                class="col-form-label text-dark">{{ __('Mail From Address') }}</label>
                                            <input type="text" name="mail_from_address" id="mail_from_address"
                                                class="form-control {{ $errors->has('mail_from_address') ? 'is-invalid' : '' }}"
                                                value="{{ isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '' }}"
                                                placeholder="{{ __('Enter Mail From Address') }}" />
                                            @if ($errors->has('mail_from_address'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_from_address') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_from_name"
                                                class="col-form-label text-dark">{{ __('Mail From Name') }}</label>
                                            <input type="text" name="mail_from_name" id="mail_from_name"
                                                class="form-control {{ $errors->has('mail_from_name') ? 'is-invalid' : '' }}"
                                                value="{{ isset($settings['mail_from_name']) ? $settings['mail_from_name'] : '' }}"
                                                placeholder="{{ __('Enter Mail From Name') }}" />
                                            @if ($errors->has('mail_from_name'))
                                                <span class="invalid-feedback text-danger text-xs">
                                                    {{ $errors->first('mail_from_name') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-0">
                                    <div class="footer-row justify-content-end felx-wrap d-flex">
                                        <a href="#"
                                            class="btn btn-print-invoice  btn-primary m-r-10 send_email mb-0"
                                            data-ajax-popup="true" data-title="{{ __('Send Test Mail') }}"
                                            data-url="{{ route('test.mail') }}">
                                            {{ __('Send Test Mail') }}
                                        </a>
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-print-invoice  btn-primary  mb-0">
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>

                        <div id="pusher-settings" class="card">
                            <div class="card-header">
                                <h5>{{ __('Pusher Settings') }}</h5>
                                <small class="text-muted">{{ __('Edit your pusher details') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::model($settings, ['route' => 'pusher.setting', 'method' => 'post']) }}
                                <div class="row mt-3">
                                    <div class="form-group col-md-6">
                                        {{ Form::label('pusher_app_id', __('Pusher App Id *'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_id', isset($settings['pusher_app_id']) ? $settings['pusher_app_id'] : '', ['class' => 'form-control font-style', 'placeholder' => __('Pusher App Id'), 'required' => 'required']) }}
                                        @error('pusher_app_id')
                                            <span class="invalid-pusher_app_id" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('pusher_app_key', __('Pusher App Key *'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_key', isset($settings['pusher_app_key']) ? $settings['pusher_app_key'] : '', ['class' => 'form-control font-style', 'placeholder' => __('Pusher App Key'), 'required' => 'required']) }}
                                        @error('pusher_app_key')
                                            <span class="invalid-pusher_app_key" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('pusher_app_secret', __('Pusher App Secret *'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_secret', isset($settings['pusher_app_secret']) ? $settings['pusher_app_secret'] : '', ['class' => 'form-control font-style', 'placeholder' => __('Pusher App Key'), 'required' => 'required']) }}
                                        @error('pusher_app_secret')
                                            <span class="invalid-pusher_app_secret" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('pusher_app_cluster', __('Pusher App Cluster *'), ['class' => 'form-label']) }}
                                        {{ Form::text('pusher_app_cluster', isset($settings['pusher_app_cluster']) ? $settings['pusher_app_cluster'] : '', ['class' => 'form-control font-style', 'placeholder' => __('Pusher App Cluster'), 'required' => 'required']) }}
                                        @error('pusher_app_cluster')
                                            <span class="invalid-pusher_app_cluster" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="text-end">
                                        {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <div id="payment-settings" class="card">
                            <div class="card-header">
                                <h5>{{ __('Payment Settings') }}</h5>
                                <small
                                    class="text-muted">{{ __('These details will be used to collect subscription plan payments.Each subscription plan will have a payment button based on the below configuration') }}</small>
                            </div>
                            {{ Form::model($settings, ['route' => 'payment.setting', 'method' => 'POST','class'=>'mb-0']) }}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label class="col-form-label">{{ __('Currency') }} *</label>
                                                <input type="text" name="currency" class="form-control"
                                                    id="currency"
                                                    value="{{ !isset($payment['currency']) || is_null($payment['currency']) ? '' : $payment['currency'] }}"
                                                    placeholder="USD" required>
                                                <small class="text-xs">
                                                    {{ __('Note: Add currency code as per three-letter ISO code.') }}.
                                                    <a href="https://stripe.com/docs/currencies"
                                                        target="_blank">{{ __('You can find out how to do that here.') }}</a>
                                                </small>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="currency_symbol"
                                                    class="col-form-label">{{ __('Currency Symbol') }}</label>
                                                <input type="text" name="currency_symbol" class="form-control"
                                                    id="currency_symbol"
                                                    value="{{ !isset($payment['currency_symbol']) || is_null($payment['currency_symbol']) ? '' : $payment['currency_symbol'] }}"
                                                    placeholder="$" required>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="faq justify-content-center">
                                    <div class="col-sm-12 col-md-10 col-xxl-12">
                                        <div class="accordion accordion-flush setting setting-accordion1"
                                            id="accordionExample">

                                            {{-- maually --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-15">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse15"
                                                        aria-expanded="false" aria-controls="collapse15">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Manually') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_manually_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                name="is_manually_enabled" id="is_manually_enabled"
                                                                {{ isset($payment['is_manually_enabled']) && $payment['is_manually_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-1"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse15"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-15"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                <div class="row pt-2">
                                                                    <label class="pb-2"
                                                                        for="is_manually_enabled">{{ __('Requesting manual payment for the planned amount for the subscriptions paln.') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- bank-transfer --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-16">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse16"
                                                        aria-expanded="false" aria-controls="collapse16">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Bank Transfer') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_bank_enabled" value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                name="is_bank_enabled" id="is_bank_enabled"
                                                                {{ isset($payment['is_bank_enabled']) && $payment['is_bank_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-1"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse16"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-16"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row gy-4">
                                                            <div class="col-md-12 mt-3">
                                                                <div class="form-group">
                                                                    {{ Form::label('bank_details', __('Bank Details'), ['class' => 'col-form-label']) }}
                                                                    {{ Form::textarea('bank_details', isset($payment['bank_details']) ? $payment['bank_details'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Your Bank Details'), 'rows' => 4]) }}
                                                                    <small class="text-xs">
                                                                        {{ __('Example : Bank : bank name </br> Account Number : 0000 0000 </br>') }}
                                                                    </small>
                                                                    @if ($errors->has('bank_details'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('bank_details') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Stripe -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-2">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse1"
                                                        aria-expanded="false" aria-controls="collapse1">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Stripe') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_stripe_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                name="is_stripe_enabled" id="is_stripe_enabled"
                                                                {{ isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-1"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse1"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-2"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row gy-4">
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="stripe_key"
                                                                        class="form-label">{{ __('Stripe Key') }}</label>
                                                                    <input class="form-control"
                                                                        placeholder="{{ __('Stripe Key') }}"
                                                                        name="stripe_key" type="text"
                                                                        value="{{ !isset($payment['stripe_key']) || is_null($payment['stripe_key']) ? '' : $payment['stripe_key'] }}"
                                                                        id="stripe_key">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="stripe_secret"
                                                                        class="form-label">{{ __('Stripe Secret') }}</label>
                                                                    <input class="form-control "
                                                                        placeholder="{{ __('Stripe Secret') }}"
                                                                        name="stripe_secret" type="text"
                                                                        value="{{ !isset($payment['stripe_secret']) || is_null($payment['stripe_secret']) ? '' : $payment['stripe_secret'] }}"
                                                                        id="stripe_secret">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paypal -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-3">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse2"
                                                        aria-expanded="false" aria-controls="collapse2">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Paypal') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paypal_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                id="customswitchv1-2" name="is_paypal_enabled"
                                                                id="is_paypal_enabled"
                                                                {{ isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse2"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-3"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                <div class="row pt-2">
                                                                    <label class="pb-2"
                                                                        for="paypal_mode">{{ __('Paypal Mode') }}</label>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="paypal_mode" value="sandbox"
                                                                                    {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="paypal_mode" value="live"
                                                                                    {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Live') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id"
                                                                        class="form-label">{{ __('Client ID') }}</label>
                                                                    <input type="text" name="paypal_client_id"
                                                                        id="paypal_client_id" class="form-control"
                                                                        value="{{ !isset($payment['paypal_client_id']) || is_null($payment['paypal_client_id']) ? '' : $payment['paypal_client_id'] }}"
                                                                        placeholder="{{ __('Client ID') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paypal_secret_key"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="paypal_secret_key"
                                                                        id="paypal_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['paypal_secret_key']) || is_null($payment['paypal_secret_key']) ? '' : $payment['paypal_secret_key'] }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paystack -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-4">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse3"
                                                        aria-expanded="true" aria-controls="collapse3">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Paystack') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paystack_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                id="customswitchv1-2" name="is_paystack_enabled"
                                                                id="is_paystack_enabled"
                                                                {{ isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse3"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-4"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id"
                                                                        class="form-label">{{ __('Public Key') }}</label>
                                                                    <input type="text" name="paystack_public_key"
                                                                        id="paystack_public_key" class="form-control"
                                                                        value="{{ !isset($payment['paystack_public_key']) || is_null($payment['paystack_public_key']) ? '' : $payment['paystack_public_key'] }}"
                                                                        placeholder="{{ __('Public Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="paystack_secret_key"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="paystack_secret_key"
                                                                        id="paystack_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['paystack_secret_key']) || is_null($payment['paystack_secret_key']) ? '' : $payment['paystack_secret_key'] }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- FLUTTERWAVE -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-5">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse4"
                                                        aria-expanded="true" aria-controls="collapse4">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Flutterwave') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_flutterwave_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                id="customswitchv1-2" name="is_flutterwave_enabled"
                                                                id="is_flutterwave_enabled"
                                                                {{ isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse4"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-5"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id"
                                                                        class="form-label">{{ __('Public Key') }}</label>
                                                                    <input type="text" name="flutterwave_public_key"
                                                                        id="flutterwave_public_key" class="form-control"
                                                                        value="{{ !isset($payment['flutterwave_public_key']) || is_null($payment['flutterwave_public_key']) ? '' : $payment['flutterwave_public_key'] }}"
                                                                        placeholder="{{ __('Public Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="paystack_secret_key"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="flutterwave_secret_key"
                                                                        id="flutterwave_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['flutterwave_secret_key']) || is_null($payment['flutterwave_secret_key']) ? '' : $payment['flutterwave_secret_key'] }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Razorpay -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-6">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse5"
                                                        aria-expanded="true" aria-controls="collapse5">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Razorpay') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_razorpay_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                id="customswitchv1-2" name="is_razorpay_enabled"
                                                                id="is_razorpay_enabled"
                                                                {{ isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse5"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-6"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="paypal_client_id"
                                                                        class="form-label">{{ __('Public Key') }}</label>

                                                                    <input type="text" name="razorpay_public_key"
                                                                        id="razorpay_public_key" class="form-control"
                                                                        value="{{ !isset($payment['razorpay_public_key']) || is_null($payment['razorpay_public_key']) ? '' : $payment['razorpay_public_key'] }}"
                                                                        placeholder="{{ __('Public Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="paystack_secret_key"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="razorpay_secret_key"
                                                                        id="razorpay_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['razorpay_secret_key']) || is_null($payment['razorpay_secret_key']) ? '' : $payment['razorpay_secret_key'] }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paytm -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-7">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse6"
                                                        aria-expanded="true" aria-controls="collapse6">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Paytm') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paytm_enabled" value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                name="is_paytm_enabled" id="is_paytm_enabled"
                                                                {{ isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse6"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-7"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                <div class="row pt-2">
                                                                    <label class="pb-2"
                                                                        for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="paytm_mode" value="local"
                                                                                    {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>


                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Local') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary"
                                                                                    name="paytm_mode" value="production"
                                                                                    {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Production') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-md-12 pb-4">
                                                                        <label class="paypal-label form-control-label" for="paypal_mode">Paytm Environment</label> <br>
                                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                            <label class="btn btn-primary btn-sm {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'active' : '' }}">
                                                                                <input type="radio" name="paytm_mode" value="local" {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>Local
                                                                            </label>
                                                                            <label class="btn btn-primary btn-sm {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'active' : '' }}">
                                                                                <input type="radio" name="paytm_mode" value="production" {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>Production
                                                                            </label>
                                                                        </div>
                                                                    </div> --}}
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="paytm_public_key"
                                                                        class="form-label">{{ __('Merchant ID') }}</label>
                                                                    <input type="text" name="paytm_merchant_id"
                                                                        id="paytm_merchant_id" class="form-control"
                                                                        value="{{ !isset($payment['paytm_merchant_id']) || is_null($payment['paytm_merchant_id']) ? '' : $payment['paytm_merchant_id'] }}"
                                                                        placeholder="{{ __('Merchant ID') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="paytm_secret_key"
                                                                        class="form-label">{{ __('Merchant Key') }}</label>
                                                                    <input type="text" name="paytm_merchant_key"
                                                                        id="paytm_merchant_key" class="form-control"
                                                                        value="{{ !isset($payment['paytm_merchant_key']) || is_null($payment['paytm_merchant_key']) ? '' : $payment['paytm_merchant_key'] }}"
                                                                        placeholder="{{ __('Merchant Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="paytm_industry_type"
                                                                        class="form-label">{{ __('Industry Type') }}</label>
                                                                    <input type="text" name="paytm_industry_type"
                                                                        id="paytm_industry_type" class="form-control"
                                                                        value="{{ !isset($payment['paytm_industry_type']) || is_null($payment['paytm_industry_type']) ? '' : $payment['paytm_industry_type'] }}"
                                                                        placeholder="{{ __('Industry Type') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mercado Pago-->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-8">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse7"
                                                        aria-expanded="true" aria-controls="collapse7">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('MercadoPago') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_mercado_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                name="is_mercado_enabled" id="is_mercado_enabled"
                                                                {{ isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>

                                                    </button>
                                                </h2>
                                                <div id="collapse7"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-8"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                <div class="row pt-2">
                                                                    <label class="pb-2"
                                                                        for="paypal_mode">{{ __('Mercado Mode') }}</label>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="mercado_mode" value="sandbox"
                                                                                    {{ (isset($payment['mercado_mode']) && $payment['mercado_mode'] == '') || (isset($payment['mercado_mode']) && $payment['mercado_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>


                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="mercado_mode" value="live"
                                                                                    {{ isset($payment['mercado_mode']) && $payment['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Live') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="mercado_access_token"
                                                                        class="form-label">{{ __('Access Token') }}</label>
                                                                    <input type="text" name="mercado_access_token"
                                                                        id="mercado_access_token" class="form-control"
                                                                        value="{{ isset($payment['mercado_access_token']) ? $payment['mercado_access_token'] : '' }}" placeholder="{{ __('Access Token')}}" />
                                                                    @if ($errors->has('mercado_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('mercado_access_token') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Mollie -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-9">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse8"
                                                        aria-expanded="true" aria-controls="collapse8">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Mollie') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_mollie_enabled"
                                                                value="off">
                                                            <input type="checkbox" class="form-check-input input-primary"
                                                                name="is_mollie_enabled" id="is_mollie_enabled"
                                                                {{ isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>

                                                    </button>
                                                </h2>
                                                <div id="collapse8"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-9"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key"
                                                                            class="form-label">{{ __('Mollie Api Key') }}</label>
                                                                        <input type="text" name="mollie_api_key"
                                                                            id="mollie_api_key" class="form-control"
                                                                            value="{{ !isset($payment['mollie_api_key']) || is_null($payment['mollie_api_key']) ? '' : $payment['mollie_api_key'] }}"
                                                                            placeholder="{{ __('Mollie Api Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="mollie_profile_id"
                                                                            class="form-label">{{ __('Mollie Profile Id') }}</label>
                                                                        <input type="text" name="mollie_profile_id"
                                                                            id="mollie_profile_id" class="form-control"
                                                                            value="{{ !isset($payment['mollie_profile_id']) || is_null($payment['mollie_profile_id']) ? '' : $payment['mollie_profile_id'] }}"
                                                                            placeholder="{{ __('Mollie Profile Id') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="mollie_partner_id"
                                                                            class="form-label">{{ __('Mollie Partner Id') }}</label>
                                                                        <input type="text" name="mollie_partner_id"
                                                                            id="mollie_partner_id" class="form-control"
                                                                            value="{{ !isset($payment['mollie_partner_id']) || is_null($payment['mollie_partner_id']) ? '' : $payment['mollie_partner_id'] }}"
                                                                            placeholder="{{ __('Mollie Partner Id') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Skrill -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-10">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse9"
                                                        aria-expanded="true" aria-controls="collapse9">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Skrill') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_skrill_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_skrill_enabled" id="is_skrill_enabled"
                                                                {{ isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>

                                                    </button>
                                                </h2>
                                                <div id="collapse9"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-10"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="col-md-6 mt-3">
                                                                <div class="form-group">
                                                                    <label for="mollie_api_key"
                                                                        class="form-label">{{ __('Skrill Email') }}</label>
                                                                    <input type="text" name="skrill_email"
                                                                        id="skrill_email" class="form-control"
                                                                        value="{{ !isset($payment['skrill_email']) || is_null($payment['skrill_email']) ? '' : $payment['skrill_email'] }}"
                                                                        placeholder="{{ __('Skrill Email') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CoinGate -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-11">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse10"
                                                        aria-expanded="true" aria-controls="collapse10">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('CoinGate') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_coingate_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_coingate_enabled" id="is_coingate_enabled"
                                                                {{ isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>

                                                    </button>
                                                </h2>
                                                <div id="collapse10"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-11"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                <div class="row pt-2">
                                                                    <label class="pb-2"
                                                                        for="paypal_mode">{{ __('CoinGate Mode') }}</label>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary"
                                                                                    name="coingate_mode" value="sandbox"
                                                                                    {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>


                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary"
                                                                                    name="coingate_mode" value="live"
                                                                                    {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Live') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="coingate_auth_token"
                                                                        class="form-label">{{ __('CoinGate Auth Token') }}</label>
                                                                    <input type="text" name="coingate_auth_token"
                                                                        id="coingate_auth_token" class="form-control"
                                                                        value="{{ !isset($payment['coingate_auth_token']) || is_null($payment['coingate_auth_token']) ? '' : $payment['coingate_auth_token'] }}"
                                                                        placeholder="{{ __('CoinGate Auth Token') }}">
                                                                </div>
                                                                @if ($errors->has('coingate_auth_token'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('coingate_auth_token') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PaymentWall -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-12">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse11"
                                                        aria-expanded="true" aria-controls="collapse11">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('PaymentWall') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paymentwall_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_paymentwall_enabled"
                                                                id="is_paymentwall_enabled"
                                                                {{ isset($payment['is_paymentwall_enabled']) && $payment['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>

                                                    </button>
                                                </h2>
                                                <div id="collapse11"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-12"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paymentwall_public_key"
                                                                        class="form-label">{{ __('Public Key') }}</label>
                                                                    <input type="text" name="paymentwall_public_key"
                                                                        id="paymentwall_public_key" class="form-control"
                                                                        value="{{ !isset($payment['paymentwall_public_key']) || is_null($payment['paymentwall_public_key']) ? '' : $payment['paymentwall_public_key'] }}"
                                                                        placeholder="{{ __('Public Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paymentwall_private_key"
                                                                        class="form-label">{{ __('Private Key') }}</label>
                                                                    <input type="text" name="paymentwall_private_key"
                                                                        id="paymentwall_private_key"
                                                                        class="form-control"
                                                                        value="{{ !isset($payment['paymentwall_private_key']) || is_null($payment['paymentwall_private_key']) ? '' : $payment['paymentwall_private_key'] }}"
                                                                        placeholder="{{ __('Private Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Toyyibpay --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-13">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse12"
                                                        aria-expanded="true" aria-controls="collapse12">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Toyyibpay') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_toyyibpay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_toyyibpay_enabled" id="is_toyyibpay_enabled"
                                                                {{ isset($payment['is_toyyibpay_enabled']) && $payment['is_toyyibpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label for="customswitch1-2"
                                                                class="form-check-label"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse12"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-13"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="toyyibpay_secret_key"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="toyyibpay_secret_key"
                                                                        id="toyyibpay_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['toyyibpay_secret_key']) || is_null($payment['toyyibpay_secret_key']) ? '' : $payment['toyyibpay_secret_key'] }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="category_code"
                                                                        class="form-label">{{ __('Category Code') }}</label>
                                                                    <input type="text" name="category_code"
                                                                        id="category_code" class="form-control"
                                                                        value="{{ !isset($payment['category_code']) || is_null($payment['category_code']) ? '' : $payment['category_code'] }}"
                                                                        placeholder="{{ __('Category Code') }}">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            {{-- PayFast --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-14">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse14"
                                                        aria-expanded="true" aria-controls="collapse14">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('Payfast') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_payfast_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_payfast_enabled" id="is_payfast_enabled"
                                                                {{ isset($payment['is_payfast_enabled']) && $payment['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapse14"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-14"
                                                    data-bs-parent="#accordionExample">

                                                    <div class="accordion-body">
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="payfast_mode" value="sandbox"
                                                                                {{ !isset($payment['payfast_mode']) || $payment['payfast_mode'] == '' || $payment['payfast_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="payfast_mode" value="live"
                                                                                {{ isset($payment['payfast_mode']) && $payment['payfast_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Live') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="payfast_merchant_id"
                                                                        class="form-label">{{ __('Merchant Id') }}</label>
                                                                    <input type="text" name="payfast_merchant_id"
                                                                        id="payfast_merchant_id" class="form-control"
                                                                        value="{{ !isset($payment['payfast_merchant_id']) || is_null($payment['payfast_merchant_id']) ? '' : $payment['payfast_merchant_id'] }}"
                                                                        placeholder="{{ __('Merchant Id') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="payfast_merchant_key"
                                                                        class="form-label">{{ __('Merchant Key') }}</label>
                                                                    <input type="text" name="payfast_merchant_key"
                                                                        id="payfast_merchant_key" class="form-control"
                                                                        value="{{ !isset($payment['payfast_merchant_key']) || is_null($payment['payfast_merchant_key']) ? '' : $payment['payfast_merchant_key'] }}"
                                                                        placeholder="{{ __('Merchant Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="payfast_signature"
                                                                        class="form-label">{{ __('Salt Passphrase') }}</label>
                                                                    <input type="text" name="payfast_signature"
                                                                        id="payfast_signature" class="form-control"
                                                                        value="{{ !isset($payment['payfast_signature']) || is_null($payment['payfast_signature']) ? '' : $payment['payfast_signature'] }}"
                                                                        placeholder="{{ __('Salt Passphrase') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- iyzipay --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-15">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse15"
                                                        aria-expanded="true" aria-controls="collapse15">
                                                        <span class="d-flex align-items-center">
                                                            {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                            {{ __('IyziPay') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_iyzipay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_iyzipay_enabled" id="is_iyzipay_enabled"
                                                                {{ isset($payment['is_iyzipay_enabled']) && $payment['is_iyzipay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapse15"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-14"
                                                    data-bs-parent="#accordionExample">

                                                    <div class="accordion-body">
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="iyzipay_mode">{{ __('IyziPay Mode') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="iyzipay_mode" value="sandbox"
                                                                                {{ !isset($payment['iyzipay_mode']) || $payment['iyzipay_mode'] == '' || $payment['iyzipay_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="iyzipay_mode" value="live"
                                                                                {{ isset($payment['iyzipay_mode']) && $payment['iyzipay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Live') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="iyzipay_key"
                                                                        class="form-label">{{ __('IyziPay Key') }}</label>
                                                                    <input type="text" name="iyzipay_key"
                                                                        id="iyzipay_key" class="form-control"
                                                                        value="{{ !isset($payment['iyzipay_key']) || is_null($payment['iyzipay_key']) ? '' : $payment['iyzipay_key'] }}"
                                                                        placeholder="{{ __('IyziPay Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="iyzipay_secret"
                                                                        class="form-label">{{ __('IyziPay Secret') }}</label>
                                                                    <input type="text" name="iyzipay_secret"
                                                                        id="iyzipay_secret" class="form-control"
                                                                        value="{{ !isset($payment['iyzipay_secret']) || is_null($payment['iyzipay_secret']) ? '' : $payment['iyzipay_secret'] }}"
                                                                        placeholder="{{ __('IyziPay Secret') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- SSPay --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-16">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse16"
                                                        aria-expanded="true" aria-controls="collapse16">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('SSPay') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_sspay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_sspay_enabled" id="is_sspay_enabled"
                                                                {{ isset($payment['is_sspay_enabled']) && $payment['is_sspay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label for="customswitch1-2"
                                                                class="form-check-label"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse16"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-16"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="sspay_secret_key"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="sspay_secret_key"
                                                                        id="sspay_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['sspay_secret_key']) || is_null($payment['sspay_secret_key']) ? '' : $payment['sspay_secret_key'] }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="sspay_category_code"
                                                                        class="form-label">{{ __('Category Code') }}</label>
                                                                    <input type="text" name="sspay_category_code"
                                                                        id="sspay_category_code" class="form-control"
                                                                        value="{{ !isset($payment['sspay_category_code']) || is_null($payment['sspay_category_code']) ? '' : $payment['sspay_category_code'] }}"
                                                                        placeholder="{{ __('Category Code') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- PayTab  --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-17">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse17"
                                                        aria-expanded="true" aria-controls="collapse17">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('PayTab') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paytab_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_paytab_enabled" id="is_paytab_enabled"
                                                                {{ isset($payment['is_paytab_enabled']) && $payment['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label for="customswitch1-2"
                                                                class="form-check-label"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse17"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-17"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paytab_profile_id"
                                                                        class="form-label">{{ __('Profile Id') }}</label>
                                                                    <input type="text" name="paytab_profile_id"
                                                                        id="paytab_profile_id" class="form-control"
                                                                        value="{{ !isset($payment['paytab_profile_id']) || is_null($payment['paytab_profile_id']) ? '' : $payment['paytab_profile_id'] }}"
                                                                        placeholder="{{ __('Profile Id') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paytab_server_key"
                                                                        class="form-label">{{ __('Server Key') }}</label>
                                                                    <input type="text" name="paytab_server_key"
                                                                        id="paytab_server_key" class="form-control"
                                                                        value="{{ !isset($payment['paytab_server_key']) || is_null($payment['paytab_server_key']) ? '' : $payment['paytab_server_key'] }}"
                                                                        placeholder="{{ __('Server Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paytab_region"
                                                                        class="form-label">{{ __('Paytab Region') }}</label>
                                                                    <input type="text" name="paytab_region"
                                                                        id="paytab_region" class="form-control"
                                                                        value="{{ !isset($payment['paytab_region']) || is_null($payment['paytab_region']) ? '' : $payment['paytab_region'] }}"
                                                                        placeholder="{{ __('Paytab Region') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Benefit  --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-18">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse18"
                                                        aria-expanded="true" aria-controls="collapse18">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Benefit') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_benefit_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_benefit_enabled" id="is_benefit_enabled"
                                                                {{ isset($payment['is_benefit_enabled']) && $payment['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label for="customswitch1-2"
                                                                class="form-check-label"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse18"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-18"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="benefit_api_key"
                                                                        class="form-label">{{ __('Benefit Key') }}</label>
                                                                    <input type="text" name="benefit_api_key"
                                                                        id="benefit_api_key" class="form-control"
                                                                        value="{{ !isset($payment['benefit_api_key']) || is_null($payment['benefit_api_key']) ? '' : $payment['benefit_api_key'] }}"
                                                                        placeholder="{{ __('Enter Benefit Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="benefit_secret_key"
                                                                        class="form-label">{{ __('Benefit Secret Key') }}</label>
                                                                    <input type="text" name="benefit_secret_key"
                                                                        id="benefit_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['benefit_secret_key']) || is_null($payment['benefit_secret_key']) ? '' : $payment['benefit_secret_key'] }}"
                                                                        placeholder="{{ __('Enter Benefit Secret key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Cashfree  --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-19">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse19"
                                                        aria-expanded="true" aria-controls="collapse19">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Cashfree') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_cashfree_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_cashfree_enabled" id="is_cashfree_enabled"
                                                                {{ isset($payment['is_cashfree_enabled']) && $payment['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label for="customswitch1-2"
                                                                class="form-check-label"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse19"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-19"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="cashfree_api_key"
                                                                        class="form-label">{{ __(' Cashfree Key') }}</label>
                                                                    <input type="text" name="cashfree_api_key"
                                                                        id="cashfree_api_key" class="form-control"
                                                                        value="{{ !isset($payment['cashfree_api_key']) || is_null($payment['cashfree_api_key']) ? '' : $payment['cashfree_api_key'] }}"
                                                                        placeholder="{{ __('Enter Cashfree Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="cashfree_secret_key"
                                                                        class="form-label">{{ __('Cashfree Secret Key') }}</label>
                                                                    <input type="text" name="cashfree_secret_key"
                                                                        id="cashfree_secret_key" class="form-control"
                                                                        value="{{ !isset($payment['cashfree_secret_key']) || is_null($payment['cashfree_secret_key']) ? '' : $payment['cashfree_secret_key'] }}"
                                                                        placeholder="{{ __('Enter Cashfree Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Aamarpay  --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-20">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse20"
                                                        aria-expanded="true" aria-controls="collapse20">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Aamarpay') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_aamarpay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_aamarpay_enabled" id="is_aamarpay_enabled"
                                                                {{ isset($payment['is_aamarpay_enabled']) && $payment['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label for="customswitch1-2"
                                                                class="form-check-label"></label>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse20"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-20"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="aamarpay_store_id"
                                                                        class="form-label">{{ __(' Store Id') }}</label>
                                                                    <input type="text" name="aamarpay_store_id"
                                                                        id="aamarpay_store_id" class="form-control"
                                                                        value="{{ !isset($payment['aamarpay_store_id']) || is_null($payment['aamarpay_store_id']) ? '' : $payment['aamarpay_store_id'] }}"
                                                                        placeholder="{{ __('Enter Store Id') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="aamarpay_signature_key"
                                                                        class="form-label">{{ __('Signature Key') }}</label>
                                                                    <input type="text" name="aamarpay_signature_key"
                                                                        id="aamarpay_signature_key" class="form-control"
                                                                        value="{{ !isset($payment['aamarpay_signature_key']) || is_null($payment['aamarpay_signature_key']) ? '' : $payment['aamarpay_signature_key'] }}"
                                                                        placeholder="{{ __('Enter Signature Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="aamarpay_description"
                                                                        class="form-label">{{ __('Description') }}</label>
                                                                    <input type="text" name="aamarpay_description"
                                                                        id="aamarpay_description" class="form-control"
                                                                        value="{{ !isset($payment['aamarpay_description']) || is_null($payment['aamarpay_description']) ? '' : $payment['aamarpay_description'] }}"
                                                                        placeholder="{{ __('Enter Signature Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- PayTR --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-21">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse21"
                                                        aria-expanded="true" aria-controls="collapse21">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Pay TR') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paytr_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_paytr_enabled" id="is_paytr_enabled"
                                                                {{ isset($payment['is_paytr_enabled']) && $payment['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapse21"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-21"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paytr_merchant_id"
                                                                        class="form-label">{{ __('Merchant Id') }}</label>
                                                                    <input type="text" name="paytr_merchant_id"
                                                                        id="paytr_merchant_id" class="form-control"
                                                                        value="{{ !isset($payment['paytr_merchant_id']) || is_null($payment['paytr_merchant_id']) ? '' : $payment['paytr_merchant_id'] }}"
                                                                        placeholder="{{ __('Merchant Id') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paytr_merchant_key"
                                                                        class="form-label">{{ __('Merchant Key') }}</label>
                                                                    <input type="text" name="paytr_merchant_key"
                                                                        id="paytr_merchant_key" class="form-control"
                                                                        value="{{ !isset($payment['paytr_merchant_key']) || is_null($payment['paytr_merchant_key']) ? '' : $payment['paytr_merchant_key'] }}"
                                                                        placeholder="{{ __('Merchant Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="paytr_merchant_salt"
                                                                        class="form-label">{{ __('Salt Passphrase') }}</label>
                                                                    <input type="text" name="paytr_merchant_salt"
                                                                        id="paytr_merchant_salt" class="form-control"
                                                                        value="{{ !isset($payment['paytr_merchant_salt']) || is_null($payment['paytr_merchant_salt']) ? '' : $payment['paytr_merchant_salt'] }}"
                                                                        placeholder="{{ __('Salt Passphrase') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Yookassa --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-22">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse22"
                                                        aria-expanded="true" aria-controls="collapse22">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Yookassa') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_yookassa_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_yookassa_enabled" id="is_yookassa_enabled"
                                                                {{ isset($payment['is_yookassa_enabled']) && $payment['is_yookassa_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapse22"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-22"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="yookassa_shop_id"
                                                                        class="form-label">{{ __('Shop ID Key') }}</label>
                                                                    <input type="text" name="yookassa_shop_id"
                                                                        id="yookassa_shop_id" class="form-control"
                                                                        value="{{ !isset($payment['yookassa_shop_id']) || is_null($payment['yookassa_shop_id']) ? '' : $payment['yookassa_shop_id'] }}"
                                                                        placeholder="{{ __('Enter Shop ID Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="yookassa_secret"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="yookassa_secret"
                                                                        id="yookassa_secret" class="form-control"
                                                                        value="{{ !isset($payment['yookassa_secret']) || is_null($payment['yookassa_secret']) ? '' : $payment['yookassa_secret'] }}"
                                                                        placeholder="{{ __('Enter Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Midtrans --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-23">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse23"
                                                        aria-expanded="true" aria-controls="collapse23">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Midtrans') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_midtrans_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_midtrans_enabled" id="is_midtrans_enabled"
                                                                {{ isset($payment['is_midtrans_enabled']) && $payment['is_midtrans_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapse23"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-23"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                <div class="row pt-2">
                                                                    <label class="pb-2"
                                                                        for="midtrans_mode">{{ __('Midtrans Mode') }}</label>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="midtrans_mode" value="sandbox"
                                                                                    {{ !isset($payment['midtrans_mode']) || $payment['midtrans_mode'] == '' || $payment['midtrans_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="border card p-3">
                                                                            <div class="form-check">
                                                                                <input type="radio"
                                                                                    class="form-check-input input-primary "
                                                                                    name="midtrans_mode" value="live"
                                                                                    {{ isset($payment['midtrans_mode']) && $payment['midtrans_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                <label class="form-check-label d-block"
                                                                                    for="">
                                                                                    <span>
                                                                                        <span class="h5 d-block"><strong
                                                                                                class="float-end"></strong>{{ __('Live') }}</span>
                                                                                    </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="midtrans_secret"
                                                                        class="form-label">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="midtrans_secret"
                                                                        id="midtrans_secret" class="form-control"
                                                                        value="{{ !isset($payment['midtrans_secret']) || is_null($payment['midtrans_secret']) ? '' : $payment['midtrans_secret'] }}"
                                                                        placeholder="{{ __('Enter Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Xendit --}}
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="heading-2-24">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse24"
                                                        aria-expanded="true" aria-controls="collapse24">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Xendit') }}
                                                        </span>
                                                        {{ __('Enable:') }}
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_xendit_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_xendit_enabled" id="is_xendit_enabled"
                                                                {{ isset($payment['is_xendit_enabled']) && $payment['is_xendit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="form-check-label"
                                                                for="customswitchv1-2"></label>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapse24"
                                                    class="accordion-collapse collapse"aria-labelledby="heading-2-24"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="xendit_api"
                                                                        class="form-label">{{ __('API Key') }}</label>
                                                                    <input type="text" name="xendit_api"
                                                                        id="xendit_api" class="form-control"
                                                                        value="{{ !isset($payment['xendit_api']) || is_null($payment['xendit_api']) ? '' : $payment['xendit_api'] }}"
                                                                        placeholder="{{ __('Enter API Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="xendit_token"
                                                                        class="form-label">{{ __('Token') }}</label>
                                                                    <input type="text" name="xendit_token"
                                                                        id="xendit_token" class="form-control"
                                                                        value="{{ !isset($payment['xendit_token']) || is_null($payment['xendit_token']) ? '' : $payment['xendit_token'] }}"
                                                                        placeholder="{{ __('Enter Token') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Nepalste -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="headingTwenty-six">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwenty-six"
                                                        aria-expanded="true" aria-controls="collapseTwenty-six">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Nepalste') }}
                                                        </span>
                                                        <div class="d-flex align-items-center">
                                                            <label class="form-check-label m-1"
                                                                for="is_nepalste_enabled">{{ __('Enable') }}</label>
                                                            <div class="form-check form-switch custom-switch-v1">
                                                                <input type="hidden" name="is_nepalste_enabled"
                                                                    value="off">
                                                                <input type="checkbox"
                                                                    class="form-check-input input-primary"
                                                                    name="is_nepalste_enabled" id="is_nepalste_enabled"
                                                                    {{ isset($payment['is_nepalste_enabled']) && $payment['is_nepalste_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwenty-six" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwenty-six"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row pt-2">
                                                            <div class="col-md-12 pb-4 form-group">
                                                                <label class="nepalste-label form-label"
                                                                    for="nepalste_mode">{{ __('Nepalste Mode') }}</label>
                                                                <br>
                                                                <div class="d-flex">
                                                                    <div class="col-lg-3" style="margin-right: 15px;">
                                                                        <div class="border accordion-header p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="nepalste_mode"
                                                                                        value="sandbox"
                                                                                        class="form-check-input"
                                                                                        {{ !isset($admin_payment_setting['nepalste_mode']) || $admin_payment_setting['nepalste_mode'] == '' || $admin_payment_setting['nepalste_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Sandbox') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <div class="border accordion-header p-3">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label text-dark">
                                                                                    <input type="radio"
                                                                                        name="nepalste_mode"
                                                                                        value="live"
                                                                                        class="form-check-input"
                                                                                        {{ isset($admin_payment_setting['nepalste_mode']) && $admin_payment_setting['nepalste_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                    {{ __('Live') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('nepalste_public_key', __('Public Key'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('nepalste_public_key', isset($payment['nepalste_public_key']) ? $payment['nepalste_public_key'] : '', ['class' => 'form-control', 'placeholder' => __('Public Key')]) }}<br>
                                                                    @if ($errors->has('nepalste_public_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('nepalste_public_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('nepalste_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('nepalste_secret_key', isset($payment['nepalste_secret_key']) ? $payment['nepalste_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}<br>
                                                                    @if ($errors->has('nepalste_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('nepalste_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Paiement Pro -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="headingTwenty-seven">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwenty-seven"
                                                        aria-expanded="true" aria-controls="collapseTwenty-seven">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Paiement Pro') }}
                                                        </span>
                                                        <div class="d-flex align-items-center">
                                                            <label class="form-check-label m-1"
                                                                for="is_paiementpro_enabled">{{ __('Enable') }}</label>
                                                            <div class="form-check form-switch custom-switch-v1">
                                                                <input type="hidden" name="is_paiementpro_enabled"
                                                                    value="off">
                                                                <input type="checkbox"
                                                                    class="form-check-input input-primary"
                                                                    name="is_paiementpro_enabled"
                                                                    id="is_paiementpro_enabled"
                                                                    {{ isset($payment['is_paiementpro_enabled']) && $payment['is_paiementpro_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwenty-seven" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwenty-seven"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row pt-2">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('paiementpro_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('paiementpro_merchant_id', isset($payment['paiementpro_merchant_id']) ? $payment['paiementpro_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                    @if ($errors->has('paiementpro_merchant_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paiementpro_merchant_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Fedapay -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="headingTwenty-eight">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwenty-eight"
                                                        aria-expanded="false" aria-controls="collapseTwenty-eight">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('Fedapay') }}
                                                        </span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2">{{ __('Enable') }}:</span>
                                                            <div class="form-check form-switch custom-switch-v1">
                                                                <input type="hidden" name="is_fedapay_enabled"
                                                                    value="off">
                                                                <input type="checkbox"
                                                                    class="form-check-input input-primary"
                                                                    id="customswitchv1-1 is_fedapay_enabled"
                                                                    name="is_fedapay_enabled"
                                                                    {{ isset($payment['is_fedapay_enabled']) && $payment['is_fedapay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwenty-eight" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwenty-eight"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="d-flex">
                                                            <div class="mr-2" style="margin-right: 15px;">
                                                                <div class="border card p-1">
                                                                    <div class="form-check">
                                                                        <label class="form-check-labe text-dark">
                                                                            <input type="radio" name="fedapay_mode"
                                                                                value="sandbox" class="form-check-input"
                                                                                {{ (isset($payment['fedapay_mode']) && $payment['fedapay_mode'] == '') || (isset($payment['fedapay_mode']) && $payment['fedapay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mr-2">
                                                                <div class="border card p-1">
                                                                    <div class="form-check">
                                                                        <label class="form-check-labe text-dark">
                                                                            <input type="radio" name="fedapay_mode"
                                                                                value="live" class="form-check-input"
                                                                                {{ isset($payment['fedapay_mode']) && $payment['fedapay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row gy-4">
                                                            <div class="col-lg-6">
                                                                <div class="input-edits">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label"
                                                                            for="fedapay_public_key">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="fedapay_public_key"
                                                                            id="fedapay_public_key" class="form-control"
                                                                            value="{{ !isset($payment['fedapay_public_key']) || is_null($payment['fedapay_public_key']) ? '' : $payment['fedapay_public_key'] }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="input-edits">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label"
                                                                            for="fedapay_secret_key">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="fedapay_secret_key"
                                                                            id="fedapay_secret_key" class="form-control"
                                                                            value="{{ isset($payment['fedapay_secret_key']) ? $payment['fedapay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- PayHere -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="headingTwenty-nine">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwenty-nine"
                                                        aria-expanded="false" aria-controls="collapseTwenty-nine">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('PayHere') }}
                                                        </span>
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2">{{ __('Enable') }}:</span>
                                                            <div class="form-check form-switch custom-switch-v1">
                                                                <input type="hidden" name="is_payhere_enabled"
                                                                    value="off">
                                                                <input type="checkbox"
                                                                    class="form-check-input input-primary"
                                                                    id="customswitchv1-1 is_payhere_enabled"
                                                                    name="is_payhere_enabled"
                                                                    {{ isset($payment['is_payhere_enabled']) && $payment['is_payhere_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseTwenty-nine" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwenty-nine"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="d-flex">
                                                            <div class="mr-2" style="margin-right: 15px;">
                                                                <div class="border card p-1">
                                                                    <div class="form-check">
                                                                        <label class="form-check-labe text-dark">
                                                                            <input type="radio" name="payhere_mode"
                                                                                value="sandbox" class="form-check-input"
                                                                                {{ (isset($payment['payhere_mode']) && $payment['payhere_mode'] == '') || (isset($payment['payhere_mode']) && $payment['payhere_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                            {{ __('Sandbox') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mr-2">
                                                                <div class="border card p-1">
                                                                    <div class="form-check">
                                                                        <label class="form-check-labe text-dark">
                                                                            <input type="radio" name="payhere_mode"
                                                                                value="live" class="form-check-input"
                                                                                {{ isset($payment['payhere_mode']) && $payment['payhere_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            {{ __('Live') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row gy-4">
                                                            <div class="col-lg-6">
                                                                <div class="input-edits">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label"
                                                                            for="payhere_merchant_id">{{ __('Merchant ID') }}</label>
                                                                        <input type="text" name="payhere_merchant_id"
                                                                            id="payhere_merchant_id"
                                                                            class="form-control"
                                                                            value="{{ !isset($payment['payhere_merchant_id']) || is_null($payment['payhere_merchant_id']) ? '' : $payment['payhere_merchant_id'] }}"
                                                                            placeholder="{{ __('Merchant ID') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="input-edits">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label"
                                                                            for="payhere_merchant_secret">{{ __('Merchant Secret') }}</label>
                                                                        <input type="text"
                                                                            name="payhere_merchant_secret"
                                                                            id="payhere_merchant_secret"
                                                                            class="form-control"
                                                                            value="{{ isset($payment['payhere_merchant_secret']) ? $payment['payhere_merchant_secret'] : '' }}"
                                                                            placeholder="{{ __('Merchant Secret') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="input-edits">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label"
                                                                            for="payhere_app_id">{{ __('App ID') }}</label>
                                                                        <input type="text" name="payhere_app_id"
                                                                            id="payhere_app_id" class="form-control"
                                                                            value="{{ isset($payment['payhere_app_id']) ? $payment['payhere_app_id'] : '' }}"
                                                                            placeholder="{{ __('App ID') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="input-edits">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label"
                                                                            for="payhere_app_secret">{{ __('App Secret') }}</label>
                                                                        <input type="text" name="payhere_app_secret"
                                                                            id="payhere_app_secret" class="form-control"
                                                                            value="{{ isset($payment['payhere_app_secret']) ? $payment['payhere_app_secret'] : '' }}"
                                                                            placeholder="{{ __('App Secret') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CinetPay -->
                                            <div class="accordion-item card">
                                                <h2 class="accordion-header" id="headingThirty">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseThirty"
                                                        aria-expanded="true" aria-controls="collapseThirty">
                                                        <span class="d-flex align-items-center">
                                                            {{ __('CinetPay') }}
                                                        </span>
                                                        <div class="d-flex align-items-center">
                                                            <label class="form-check-label m-1"
                                                                for="is_cinetpay_enabled">{{ __('Enable') }}</label>
                                                            <div class="form-check form-switch custom-switch-v1">
                                                                <input type="hidden" name="is_cinetpay_enabled"
                                                                    value="off">
                                                                <input type="checkbox"
                                                                    class="form-check-input input-primary"
                                                                    name="is_cinetpay_enabled" id="is_cinetpay_enabled"
                                                                    {{ isset($payment['is_cinetpay_enabled']) && $payment['is_cinetpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapseThirty" class="accordion-collapse collapse"
                                                    aria-labelledby="headingThirty" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="row pt-2">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('cinetpay_api_key', __('CinetPay API Key'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('cinetpay_api_key', isset($payment['cinetpay_api_key']) ? $payment['cinetpay_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('CinetPay API Key')]) }}<br>
                                                                    @if ($errors->has('cinetpay_api_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('cinetpay_api_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    {{ Form::label('cinetpay_site_id', __('CinetPay Site ID'), ['class' => 'form-label']) }}
                                                                    {{ Form::text('cinetpay_site_id', isset($payment['cinetpay_site_id']) ? $payment['cinetpay_site_id'] : '', ['class' => 'form-control', 'placeholder' => __('CinetPay Site ID')]) }}<br>
                                                                    @if ($errors->has('cinetpay_site_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('cinetpay_site_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end pb-0">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary mb-0']) }}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>


                        {{-- recaptcha --}}
                        <div id="recaptcha-settings" class="card">
                            <form method="POST" action="{{ route('recaptcha.settings.store') }}"
                                accept-charset="UTF-8">
                                @csrf
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5>{{ __('ReCaptcha settings') }}</h5>
                                            <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/"
                                                target="_blank" class="text-blue">
                                                <small>({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                            </a>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="col switch-width">
                                                <div class="form-group ml-2 mb-0">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            data-onstyle="primary" data-toggle="switchbutton"
                                                            name="recaptcha_module" id="recaptcha_module"
                                                            value="yes"
                                                            {{ $settings['recaptcha_module'] == 'yes' ? ' checked ' : '' }}>
                                                        <label class="custom-control-label form-control-label px-2"
                                                            for="recaptcha_module"></label><br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row mt-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4 form-group">
                                            <div class="form-group col switch-width">
                                                {{ Form::label('google_recaptcha_version', __('Google Recaptcha Version'), ['class' => 'form-label']) }}
                                                {{ Form::select('google_recaptcha_version', $google_recaptcha_version, isset($settings['google_recaptcha_version']) ? $settings['google_recaptcha_version'] : 'v2', ['id' => 'google_recaptcha_version', 'class' => 'form-control choices', 'searchEnabled' => 'true']) }}
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-sm-4 form-group">
                                            <label for="google_recaptcha_key"
                                                class="form-label">{{ __('Google Recaptcha Key') }}</label>
                                            <input class="form-control"
                                                placeholder="{{ __('Enter Google Recaptcha Key') }}"
                                                name="google_recaptcha_key" type="text"
                                                value=" {{ !isset($settings['google_recaptcha_key']) || is_null($settings['google_recaptcha_key']) ? '' : $settings['google_recaptcha_key'] }}"
                                                id="google_recaptcha_key">
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 form-group">
                                            <label for="google_recaptcha_secret"
                                                class="form-label">{{ __('Google Recaptcha Secret') }}</label>
                                            <input class="form-control "
                                                placeholder="{{ __('Enter Google Recaptcha Secret') }}"
                                                name="google_recaptcha_secret" type="text"
                                                value=" {{ !isset($settings['google_recaptcha_secret']) || is_null($settings['google_recaptcha_secret']) ? '' : $settings['google_recaptcha_secret'] }}"
                                                id="google_recaptcha_secret">
                                        </div>
                                        <div class="text-end">
                                            {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>




                        <!--storage Setting-->
                        <div id="storage-settings" class="card mb-3">
                            {{ Form::open(['route' => 'storage.setting.store', 'enctype' => 'multipart/form-data']) }}
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-10 col-md-10 col-sm-10">
                                        <h5 class="">{{ __('Storage Settings') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting"
                                            id="local-outlined" autocomplete="off"
                                            {{ $settings['storage_setting'] == 'local' ? 'checked' : '' }}
                                            value="local" checked>
                                        <label class="btn btn-outline-primary"
                                            for="local-outlined">{{ __('Local') }}</label>
                                    </div>
                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting"
                                            id="s3-outlined" autocomplete="off"
                                            {{ $settings['storage_setting'] == 's3' ? 'checked' : '' }} value="s3">
                                        <label class="btn btn-outline-primary" for="s3-outlined">
                                            {{ __('AWS S3') }}</label>
                                    </div>

                                    <div class="pe-2">
                                        <input type="radio" class="btn-check" name="storage_setting"
                                            id="wasabi-outlined" autocomplete="off"
                                            {{ $settings['storage_setting'] == 'wasabi' ? 'checked' : '' }}
                                            value="wasabi">
                                        <label class="btn btn-outline-primary"
                                            for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="local-setting row">
                                        <div class="col-lg-7">
                                            {{-- <h4 class="small-title">{{ __('Local Settings') }}</h4> --}}
                                            <div class="form-group col-12 switch-width">
                                                {{ Form::label('local_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                                <select name="local_storage_validation[]" class="select2"
                                                    id="local_storage_validation" multiple>
                                                    @foreach ($file_type as $f)
                                                        <option @if (in_array($f, $local_storage_validations)) selected @endif>
                                                            {{ $f }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label"
                                                    for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                <input type="number" name="local_storage_max_upload_size"
                                                    class="form-control"
                                                    value="{{ !isset($settings['local_storage_max_upload_size']) || is_null($settings['local_storage_max_upload_size']) ? '' : $settings['local_storage_max_upload_size'] }}"
                                                    placeholder="{{ __('Max upload size') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="s3-setting row {{ $settings['storage_setting'] == 's3' ? ' ' : 'd-none' }}">

                                        <div class=" row ">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_key">{{ __('S3 Key') }}</label>
                                                    <input type="text" name="s3_key" class="form-control"
                                                        value="{{ !isset($settings['s3_key']) || is_null($settings['s3_key']) ? '' : $settings['s3_key'] }}"
                                                        placeholder="{{ __('S3 Key') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_secret">{{ __('S3 Secret') }}</label>
                                                    <input type="text" name="s3_secret" class="form-control"
                                                        value="{{ !isset($settings['s3_secret']) || is_null($settings['s3_secret']) ? '' : $settings['s3_secret'] }}"
                                                        placeholder="{{ __('S3 Secret') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_region">{{ __('S3 Region') }}</label>
                                                    <input type="text" name="s3_region" class="form-control"
                                                        value="{{ !isset($settings['s3_region']) || is_null($settings['s3_region']) ? '' : $settings['s3_region'] }}"
                                                        placeholder="{{ __('S3 Region') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                    <input type="text" name="s3_bucket" class="form-control"
                                                        value="{{ !isset($settings['s3_bucket']) || is_null($settings['s3_bucket']) ? '' : $settings['s3_bucket'] }}"
                                                        placeholder="{{ __('S3 Bucket') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_url">{{ __('S3 URL') }}</label>
                                                    <input type="text" name="s3_url" class="form-control"
                                                        value="{{ !isset($settings['s3_url']) || is_null($settings['s3_url']) ? '' : $settings['s3_url'] }}"
                                                        placeholder="{{ __('S3 URL') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_endpoint">{{ __('S3 Endpoint') }}</label>
                                                    <input type="text" name="s3_endpoint" class="form-control"
                                                        value="{{ !isset($settings['s3_endpoint']) || is_null($settings['s3_endpoint']) ? '' : $settings['s3_endpoint'] }}"
                                                        placeholder="{{ __('S3 Endpoint') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group col-12 switch-width">
                                                    {{ Form::label('s3_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                                    <select name="s3_storage_validation[]" class="select2"
                                                        id="s3_storage_validation" multiple>
                                                        @foreach ($file_type as $f)
                                                            <option @if (in_array($f, $s3_storage_validations)) selected @endif>
                                                                {{ $f }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="s3_max_upload_size"
                                                        class="form-control"
                                                        value="{{ !isset($settings['s3_max_upload_size']) || is_null($settings['s3_max_upload_size']) ? '' : $settings['s3_max_upload_size'] }}"
                                                        placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div
                                        class="wasabi-setting row {{ $settings['storage_setting'] == 'wasabi' ? ' ' : 'd-none' }}">
                                        <div class=" row ">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_key">{{ __('Wasabi Key') }}</label>
                                                    <input type="text" name="wasabi_key" class="form-control"
                                                        value="{{ !isset($settings['wasabi_key']) || is_null($settings['wasabi_key']) ? '' : $settings['wasabi_key'] }}"
                                                        placeholder="{{ __('Wasabi Key') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                    <input type="text" name="wasabi_secret" class="form-control"
                                                        value="{{ !isset($settings['wasabi_secret']) || is_null($settings['wasabi_secret']) ? '' : $settings['wasabi_secret'] }}"
                                                        placeholder="{{ __('Wasabi Secret') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="s3_region">{{ __('Wasabi Region') }}</label>
                                                    <input type="text" name="wasabi_region" class="form-control"
                                                        value="{{ !isset($settings['wasabi_region']) || is_null($settings['wasabi_region']) ? '' : $settings['wasabi_region'] }}"
                                                        placeholder="{{ __('Wasabi Region') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                    <input type="text" name="wasabi_bucket" class="form-control"
                                                        value="{{ !isset($settings['wasabi_bucket']) || is_null($settings['wasabi_bucket']) ? '' : $settings['wasabi_bucket'] }}"
                                                        placeholder="{{ __('Wasabi Bucket') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="wasabi_url">{{ __('Wasabi URL') }}</label>
                                                    <input type="text" name="wasabi_url" class="form-control"
                                                        value="{{ !isset($settings['wasabi_url']) || is_null($settings['wasabi_url']) ? '' : $settings['wasabi_url'] }}"
                                                        placeholder="{{ __('Wasabi URL') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="wasabi_root">{{ __('Wasabi Root') }}</label>
                                                    <input type="text" name="wasabi_root" class="form-control"
                                                        value="{{ !isset($settings['wasabi_root']) || is_null($settings['wasabi_root']) ? '' : $settings['wasabi_root'] }}"
                                                        placeholder="{{ __('Wasabi Root') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">

                                                <div class="form-group col-12 switch-width">
                                                    {{ Form::label('wasabi_storage_validation', __('Only Upload Files'), ['class' => 'form-label']) }}

                                                    <select name="wasabi_storage_validation[]" class="select2"
                                                        id="wasabi_storage_validation" multiple>
                                                        @foreach ($file_type as $f)
                                                            <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>
                                                                {{ $f }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="wasabi_root">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="wasabi_max_upload_size"
                                                        class="form-control"
                                                        value="{{ !isset($settings['wasabi_max_upload_size']) || is_null($settings['wasabi_max_upload_size']) ? '' : $settings['wasabi_max_upload_size'] }}"
                                                        placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class=" text-end pb-0">
                                    <input class="btn btn-print-invoice  btn-primary" type="submit"
                                        value="{{ __('Save Changes') }}">
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                        {{-- SEO setting --}}
                        <div class="card" id="SEO">
                            {{ Form::open(['url' => route('seo.settings'), 'enctype' => 'multipart/form-data']) }}
                            <div class="card-header">

                                <h5>{{ __('SEO Settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if (!empty($settings['chatgpt_key']))
                                        <div class="text-end">
                                            <a href="#" data-size="md" class="btn btn-sm btn-primary "
                                                data-ajax-popup-over="true" data-size="md"
                                                data-title="{{ __('Generate content with AI') }}"
                                                data-url="{{ route('generate', ['seo']) }}" data-toggle="tooltip"
                                                title="{{ __('Generate') }}">
                                                <i class="fas fa-robot"></span><span
                                                        class="robot">{{ __('Generate With AI') }}</span></i>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('meta_keywords', __('Meta Keywords'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('meta_keywords', Utility::getValByName('meta_keywords'), ['class' => 'form-control ', 'placeholder' => 'Meta Keywords']) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('meta_description', __('Meta Description'), ['class' => 'form-label']) }}
                                            {{ Form::textarea('meta_description', Utility::getValByName('meta_description'), ['class' => 'form-control ', 'rows' => '5', 'placeholder' => 'Enter Meta Description']) }}
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('Meta Image', __('Meta Image'), ['class' => 'col-form-label']) }}
                                            <div class="">
                                                {{-- @php
                                                    $src = isset($settings['meta_image']) && !empty($settings['meta_image']) ? asset(Storage::url('uploads/metaevent/meta_image.png' . $settings['meta_image'])) : '';
                                                @endphp --}}

                                                <a href="{{ $meta_image . '/' . (isset($settings['meta_image']) && !empty($settings['meta_image']) ? $settings['meta_image'] : 'meta_image.png') }}"
                                                    target="_blank"> <img id="meta"
                                                        src="{{ $meta_image . '/' . (isset($settings['meta_image']) && !empty($settings['meta_image']) ? $settings['meta_image'] : 'meta_image.png') }}"
                                                        width="300px" class="img_setting"> </a>
                                            </div>
                                            <div class="choose-files mt-4">
                                                <label for="meta_image">
                                                    <div class=" bg-primary logo"> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Select image') }}
                                                    </div>
                                                    <input style="margin-top: -40px;" type="file"
                                                        class="form-control file" name="meta_image" id="meta_image"
                                                        data-filename="meta_image"
                                                        onchange="document.getElementById('meta_image_pre').src = window.URL.createObjectURL(this.files[0])" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn-submit btn btn-primary mb-0" type="submit">
                                            {{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{ Form::close() }}
                        </div>

                        {{-- cache setting --}}
                        <div class="card" id="cache">
                            <div class="card-header">
                                <div class="row">

                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <h5>{{ __('Cache Settings') }}</h5>
                                        <p class="text-muted">{{__('This is a page meant for more advanced users, simply ignore it if you don not understand what cache is.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                </div>
                                <div class="row">
                                    <div class="row col-xl-12">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="size">Current cache size</label>
                                                <div class="input-group">
                                                    <input id="size" name="size" type="text"
                                                        class="form-control" value="{{ Utility::GetCacheSize() }}"
                                                        readonly="readonly">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            MB
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ url('config-cache') }}"
                                        class="btn btn-print-invoice btn-primary mb-0">{{ __('Clear Cache') }}</a>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                        {{-- Cookie Setting --}}
                        <div class="card" id="cookie">
                            {{ Form::model($settings, ['route' => 'cookie.setting', 'method' => 'post']) }}
                            <div
                                class="card-header flex-column flex-lg-row  d-flex align-items-lg-center gap-2 justify-content-between">
                                <h5>{{ __('Cookie Settings') }}</h5>
                                <div class="d-flex align-items-center">
                                    {{ Form::label('enable_cookie', __('Enable cookie'), ['class' => 'col-form-label p-0 fw-bold me-3']) }}
                                    <div class="custom-control custom-switch" onclick="enablecookie()">

                                        <input type="checkbox" data-toggle="switchbutton" data-onstyle="primary"
                                            name="enable_cookie" class="form-check-input input-primary "
                                            id="enable_cookie"
                                            {{ $settings['enable_cookie'] == 'on' ? ' checked ' : '' }}>
                                        <label class="custom-control-label mb-1" for="enable_cookie"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="card-body cookieDiv {{ $settings['enable_cookie'] == 'off' ? 'disabledCookie ' : '' }}">
                                <div class="row">
                                    @if (!empty($settings['chatgpt_key']))
                                        <div class="text-end">
                                            <a href="#" data-size="md" class="btn btn-sm btn-primary"
                                                data-ajax-popup-over="true" data-size="md"
                                                data-title="{{ __('Generate content with AI') }}"
                                                data-url="{{ route('generate', ['account']) }}" data-toggle="tooltip"
                                                title="{{ __('Generate') }}">
                                                <i class="fas fa-robot"></span><span
                                                        class="robot">{{ __('Generate With AI') }}</span></i>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="form-check form-switch custom-switch-v1" id="cookie_log">
                                            <input type="checkbox" name="cookie_logging"
                                                class="form-check-input tem input-primary cookie_setting str"
                                                id="cookie_logging"{{ $settings['cookie_logging'] == 'on' ? ' checked ' : '' }}>
                                            <label class="form-check-label" style="margin-left:5px"
                                                for="cookie_logging">{{ __('Enable logging') }}</label>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('cookie_title', __('Cookie Title'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('cookie_title', null, ['class' => 'form-control cookie_setting','placeholder'=>__('Enter Cookie Title')]) }}
                                        </div>
                                        <div class="form-group ">
                                            {{ Form::label('cookie_description', __('Cookie Description'), ['class' => ' form-label']) }}
                                            {!! Form::textarea('cookie_description', null, ['class' => 'form-control cookie_setting', 'rows' => '3','placeholder'=>__('Enter Cookie Description')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch custom-switch-v1 ">
                                            <input type="checkbox" name="necessary_cookies"
                                                class="form-check-input tem input-primary str" id="necessary_cookies"
                                                checked onclick="return false">
                                            <label class="form-check-label" style="margin-left:5px"
                                                for="necessary_cookies">{{ __('Strictly necessary cookies') }}</label>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('strictly_cookie_title', __(' Strictly Cookie Title'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('strictly_cookie_title', null, ['class' => 'form-control cookie_setting','placeholder'=>__('Enter Strictly Cookie Title')]) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('strictly_cookie_description', __('Strictly Cookie Description'), ['class' => ' form-label']) }}
                                            {!! Form::textarea('strictly_cookie_description', null, [
                                                'class' => 'form-control cookie_setting ',
                                                'rows' => '3','placeholder'=>__('Enter Strictly Cookie Description')
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h5>{{ __('More Information') }}</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            {{ Form::label('more_information_description', __('Contact Us Description'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('more_information_description', null, ['class' => 'form-control cookie_setting','placeholder'=>__('Enter Contact Us Description')]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            {{ Form::label('contactus_url', __('Contact Us URL'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('contactus_url', null, ['class' => 'form-control cookie_setting','placeholder'=>__('Enter Contact Us URL')]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="card-footer d-flex align-items-center gap-2 flex-sm-column flex-lg-row justify-content-between">
                                <div>
                                    @if (isset($settings['cookie_logging']) && $settings['cookie_logging'] == 'on')
                                        <label for="file"
                                            class="form-label">{{ __('Download cookie accepted data') }}</label>
                                        <a href="{{ asset(Storage::url('uploads/sample')) . '/data.csv' }}"
                                            class="btn btn-primary mr-2 ">
                                            <i class="ti ti-download"></i>
                                        </a>
                                    @endif
                                </div>
                                <input type="submit" value="{{ __(' Save Changes') }}" class="btn btn-primary">
                            </div>
                            {{ Form::close() }}
                        </div>

                        {{-- ChatGPT  --}}
                        @if (\Auth::user()->type == 'super admin')
                            <div class="card" id="pills-chatgpt-settings">
                                {{ Form::model($settings, ['route' => 'settings.chatgptkey', 'method' => 'post']) }}
                                <div class="card-header">
                                    <h5>{{ __('Chat GPT Key Settings') }}</h5>
                                    <small>{{ __('Edit your key details') }}</small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group">
                                            {{ Form::label('chatgpt_key', __('Chat GPT key'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('chatgpt_key', isset($settings['chatgpt_key']) ? $settings['chatgpt_key'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Chatgpt Key Here')]) }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('Chat GPT Model', __('Chat GPT Model Name'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('chatgpt_model', isset($settings['chatgpt_model']) ? $settings['chatgpt_model'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Chatgpt Model')]) }}
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-primary" type="submit">{{ __('Save Changes') }}</button>
                                </div>
                                {{ Form::close() }}
                            </div>
                        @endif

                </div>
                @endif
                @if (\Auth::user()->type == 'owner')
                    <div id="site-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Site Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your site details') }}</small>
                        </div>
                        <div class="card-body">
                            {{ Form::model($settings, ['route' => 'business.setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="small-title">{{ __('Dark Logo') }}</h5>
                                        </div>
                                        <div class="card-body setting-card setting-logo-box p-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="logo-content logo-set-bg py-2" style="height:65px">
                                                        {{-- <a href="{{$logos.'logo-dark.png'}}" target="_blank">
                                                                    <img id="blah" alt="your image" src="{{$logo.'logo-dark.png'}}" width="170px" class="big-logo">
                                                                </a> --}}
                                                        <a href="{{ $logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png' . '?' . time()) }}"
                                                            target="_blank" style="height: 50px; width:150px;">
                                                            <img id="blah" alt="your image"
                                                                src="{{ $logo . '/' . (isset($dark_logo) && !empty($dark_logo) ? $dark_logo : 'logo-dark.png' . '?' . time()) }}"
                                                                style="height: 50px; width:150px;">
                                                        </a>

                                                    </div>
                                                </div>
                                                <div class="col-12">

                                                    <div class="choose-files mt-5">
                                                        <label for="company_logo_dark">
                                                            <div class=" bg-primary"> <i
                                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"name="company_logo_dark"
                                                                id="company_logo_dark" class="form-control file"
                                                                data-filename="company_logo_update"
                                                                onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">

                                                            {{-- <input type="file" name="company_logo_dark" id="company_logo_dark" class="form-control file" data-filename="company_logo_update"> --}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="small-title">{{ __('Light Logo') }}</h5>
                                        </div>
                                        <div class="card-body setting-card setting-logo-box p-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="logo-content logo-set-bg py-2" style="height:65px">
                                                        <a href="{{ $logo . '/' . (isset($light_logo) && !empty($light_logo) ? $light_logo : 'logo-light.png' . '?' . time()) }}"
                                                            target="_blank" style="height: 50px; width:150px;">
                                                            <img id="blah1" alt="your image"
                                                                src="{{ $logo . '/' . (isset($light_logo) && !empty($light_logo) ? $light_logo : 'logo-light.png' . '?' . time()) }}"
                                                                style="height: 50px; width:150px;" class="img_setting">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="choose-files mt-5">
                                                        <label for="company_logo_light">
                                                            <div class=" bg-primary"> <i
                                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"name="company_logo_light"
                                                                id="company_logo_light" class="form-control file"
                                                                data-filename="company_logo_update"
                                                                onchange="document.getElementById('blah1').src = window.URL.createObjectURL(this.files[0])">

                                                            {{-- <input type="file" name="company_logo_light" id="company_logo_light" class="form-control file" data-filename="company_logo_update"> --}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="small-title">{{ __('Favicon') }}</h5>
                                        </div>
                                        <div class="card-body setting-card setting-logo-box p-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="logo-content logo-set-bg py-2" style="height:65px">
                                                        <a href="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png' . '?' . time()) }}"
                                                            target="_blank">
                                                            <img id="blah2" alt="your image"
                                                                src="{{ $logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png' . '?' . time()) }}"
                                                                width="50px" class="img_setting">
                                                        </a>

                                                    </div>
                                                </div>
                                                <div class="col-12">

                                                    <div class="choose-files mt-5">
                                                        <label for="company_favicon">
                                                            <div class=" bg-primary"> <i
                                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                            </div>
                                                            <input type="file"name="company_favicon"
                                                                id="company_favicon" class="form-control file"
                                                                data-filename="company_logo_update"
                                                                onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">

                                                            {{-- <input type="file" name="company_favicon" id="company_favicon" class="form-control file" data-filename="company_logo_update"> --}}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        {{ Form::label('title_text', __('Title Text'), ['class' => 'form-label']) }}
                                        {{ Form::text('title_text', null, ['class' => 'form-control', 'placeholder' => __('Title Text')]) }}
                                        @error('title_text')
                                            <span class="invalid-title_text" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Application URL') }}</label>
                                            {{ Form::text('currency', URL::to('/'), ['class' => 'form-control', 'placeholder' => __('Enter Currency'), 'disabled' => 'true']) }}
                                            <small>{{ __('Application URL to log into the app') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="col switch-width">
                                            <div class="form-group ml-2 mr-3 ">
                                                {{ Form::label('SITE_RTL', __('Enable RTL'), ['class' => 'form-label']) }}
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" data-toggle="switchbutton"
                                                        data-onstyle="primary" class="" name="SITE_RTL"
                                                        id="SITE_RTL"{{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label" for="SITE_RTL"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        {{ Form::label('default_owner_language', __('Default Language'), ['class' => 'form-label']) }}
                                        <div class="changeLanguage">
                                            <select name="default_owner_language" id="default_owner_language"
                                                class="form-control custom-select">
                                                @foreach (\App\Models\Utility::languages() as $code => $language)
                                                    <option @if ($lang == $code) selected @endif
                                                        value="{{ $code }}">{{ ucfirst($language) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="small-title">{{ __('Theme Customizer') }}</h4>

                                <div class="setting-card setting-logo-box p-3">
                                    <div class="row">
                                        <div class="col-lg-4 col-xl-4 col-md-4">
                                            <h6 class="mt-2">
                                                <i data-feather="credit-card"
                                                    class="me-2"></i>{{ __('Primary color settings') }}
                                            </h6>

                                            <hr class="my-2" />

                                            <div class="color-wrp">
                                                <div class="theme-color themes-color">
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-1' ? 'active_color' : '' }}"
                                                        data-value="theme-1"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-1"{{ $color == 'theme-1' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-2' ? 'active_color' : '' }}"
                                                        data-value="theme-2"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-2"{{ $color == 'theme-2' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-3' ? 'active_color' : '' }}"
                                                        data-value="theme-3"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-3"{{ $color == 'theme-3' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-4' ? 'active_color' : '' }}"
                                                        data-value="theme-4"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-4"{{ $color == 'theme-4' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-5' ? 'active_color' : '' }}"
                                                        data-value="theme-5"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-5"{{ $color == 'theme-5' ? 'checked' : '' }}>
                                                    <br>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-6' ? 'active_color' : '' }}"
                                                        data-value="theme-6"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-6"{{ $color == 'theme-6' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-7' ? 'active_color' : '' }}"
                                                        data-value="theme-7"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-7"{{ $color == 'theme-7' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-8' ? 'active_color' : '' }}"
                                                        data-value="theme-8"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-8"{{ $color == 'theme-8' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-9' ? 'active_color' : '' }}"
                                                        data-value="theme-9"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-9"{{ $color == 'theme-9' ? 'checked' : '' }}>
                                                    <a href="#!"
                                                        class="themes-color-change {{ $color == 'theme-10' ? 'active_color' : '' }}"
                                                        data-value="theme-10"></a>
                                                    <input type="radio" class="theme_color d-none" name="color"
                                                        value="theme-10"{{ $color == 'theme-10' ? 'checked' : '' }}>
                                                </div>
                                                <div class="color-picker-wrp ">
                                                    <input type="color" value="{{ $color ? $color : '' }}"
                                                        class="colorPicker {{ isset($flag) && $flag == 'true' ? 'active_color' : '' }}"
                                                        name="custom_color" id="color-picker">
                                                    <input type='hidden' name="color_flag"
                                                        value={{ isset($flag) && $flag == 'true' ? 'true' : 'false' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4 col-md-4">
                                            <h6 class="mt-2">
                                                <i data-feather="layout"
                                                    class="me-2"></i>{{ __('Sidebar settings') }}
                                            </h6>
                                            <hr class="my-2" />
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="cust-theme-bg"
                                                    name="cust_theme_bg"
                                                    {{ !empty($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on' ? 'checked' : '' }} />
                                                <label class="form-check-label f-w-600 pl-1"
                                                    for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-xl-4 col-md-4">
                                            <h6 class="mt-2">
                                                <i data-feather="sun" class="me-2"></i>{{ __('Layout settings') }}
                                            </h6>
                                            <hr class="my-2" />
                                            <div class="form-check form-switch mt-2">
                                                <input type="checkbox" class="form-check-input" id="cust-darklayout"
                                                    name="cust_darklayout"{{ !empty($settings['cust_darklayout']) && $settings['cust_darklayout'] == 'on' ? 'checked' : '' }} />
                                                <label class="form-check-label f-w-600 pl-1"
                                                    for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                            </div>


                            {{ Form::close() }}
                        </div>
                    </div>

                    <div id="company-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Company Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your company details') }}</small>
                        </div>
                        <div class="card-body">
                            {{ Form::model($settings, ['route' => 'company.setting', 'method' => 'post']) }}
                            <div class="row mt-3">
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_name *', __('Company Name *'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_name', null, ['class' => 'form-control font-style', 'placeholder' =>__( 'Company Name')]) }}
                                    @error('company_name')
                                        <span class="invalid-company_name" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_address', __('Address'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_address', null, ['class' => 'form-control font-style', 'placeholder' => __('Company Address')]) }}
                                    @error('company_address')
                                        <span class="invalid-company_address" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_city', __('City'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_city', null, ['class' => 'form-control font-style', 'placeholder' =>__('City')]) }}
                                    @error('company_city')
                                        <span class="invalid-company_city" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_state', __('State'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_state', null, ['class' => 'form-control font-style', 'placeholder' => __('State')]) }}
                                    @error('company_state')
                                        <span class="invalid-company_state" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_zipcode', __('Zip/Post Code'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_zipcode', null, ['class' => 'form-control', 'placeholder' => __('Zipcode')]) }}
                                    @error('company_zipcode')
                                        <span class="invalid-company_zipcode" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group  col-md-6">
                                    {{ Form::label('company_country', __('Country'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_country', null, ['class' => 'form-control font-style', 'placeholder' => __('Country')]) }}
                                    @error('company_country')
                                        <span class="invalid-company_country" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_telephone', __('Telephone'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_telephone', null, ['class' => 'form-control', 'placeholder' => __('Telephone')]) }}
                                    @error('company_telephone')
                                        <span class="invalid-company_telephone" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    {{ Form::label('company_email', __('System Email *'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_email', null, ['class' => 'form-control', 'placeholder' => 'Company Email']) }}
                                    @error('company_email')
                                        <span class="invalid-company_email" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('company_email_from_name', __('Email (From Name) *'), ['class' => 'form-label']) }}
                                    {{ Form::text('company_email_from_name', null, ['class' => 'form-control font-style', 'placeholder' => 'Company Email From Name']) }}
                                    @error('company_email_from_name')
                                        <span class="invalid-company_email_from_name" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div> --}}


                                <div class="text-end">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>

                    <div id="company-email-setting" class="card">
                        <div class="card-header">
                            <h5>{{ __('Email Settings') }}</h5>
                            <small
                                class="text-muted">{{ __('This SMTP will be used for sending your company-level email. If this field is empty, then SuperAdmin SMTP will be used for sending emails.') }}</small>
                        </div>
                        {{ Form::open(['route' => 'email.setting', 'method' => 'post','class'=>'mb-0']) }}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_driver"
                                            class="form-label text-dark">{{ __('Mail Driver') }}</label>
                                        <input type="text" name="mail_driver" id="mail_driver"
                                            class="form-control {{ $errors->has('mail_driver') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_driver']) ? $company_setting['mail_driver'] : '' }}"
                                            placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_driver_placeholder') }}" />
                                        @if ($errors->has('mail_driver'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_driver') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_host"
                                            class="form-label text-dark">{{ __('Mail Host') }}</label>
                                        <input type="text" name="mail_host" id="mail_host"
                                            class="form-control {{ $errors->has('mail_host') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_host']) ? $company_setting['mail_host'] : '' }}"
                                            placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}" />
                                        @if ($errors->has('mail_host'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_host') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_port"
                                            class="form-label text-dark">{{ __('Mail Port') }}</label>
                                        <input type="number" name="mail_port" id="mail_port"
                                            class="form-control {{ $errors->has('mail_port') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_port']) ? $company_setting['mail_port'] : '' }}"
                                            placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}" />
                                        @if ($errors->has('mail_port'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_port') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_username"
                                            class="form-label text-dark">{{ __('Mail Username') }}</label>
                                        <input type="text" name="mail_username" id="mail_username"
                                            class="form-control {{ $errors->has('mail_username') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_username']) ? $company_setting['mail_username'] : '' }}"
                                            placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}" />
                                        @if ($errors->has('mail_username'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_username') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_password"
                                            class="form-label text-dark">{{ __('Mail Password') }}</label>
                                        <input type="text" name="mail_password" id="mail_password"
                                            class="form-control {{ $errors->has('mail_password') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_password']) ? $company_setting['mail_password'] : '' }}"
                                            placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}" />
                                        @if ($errors->has('mail_password'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_password') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_encryption"
                                            class="form-label text-dark">{{ __('Mail Encryption') }}</label>
                                        <input type="text" name="mail_encryption" id="mail_encryption"
                                            class="form-control {{ $errors->has('mail_encryption') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_encryption']) ? $company_setting['mail_encryption'] : '' }}"
                                            placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}" />
                                        @if ($errors->has('mail_encryption'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_encryption') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_from_address"
                                            class="form-label text-dark">{{ __('Mail From Address') }}</label>
                                        <input type="text" name="mail_from_address" id="mail_from_address"
                                            class="form-control {{ $errors->has('mail_from_address') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_from_address']) ? $company_setting['mail_from_address'] : '' }}"
                                            placeholder="{{ __('Enter Mail From Address') }}" />
                                        @if ($errors->has('mail_from_address'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_from_address') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mail_from_name"
                                            class="form-label text-dark">{{ __('Mail From Name') }}</label>
                                        <input type="text" name="mail_from_name" id="mail_from_name"
                                            class="form-control {{ $errors->has('mail_from_name') ? 'is-invalid' : '' }}"
                                            value="{{ isset($company_setting['mail_from_name']) ? $company_setting['mail_from_name'] : '' }}"
                                            placeholder="{{ __('Enter Mail From Name') }}" />
                                        @if ($errors->has('mail_from_name'))
                                            <span class="invalid-feedback text-danger text-xs">
                                                {{ $errors->first('mail_from_name') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="footer-row justify-content-end felx-wrap d-flex">
                                    <a href="#" class="btn btn-print-invoice  btn-primary m-r-10 send_email mb-0"
                                        data-ajax-popup="true" data-title="{{ __('Send Test Mail') }}"
                                        data-url="{{ route('test.mail') }}">
                                        {{ __('Send Test Mail') }}
                                    </a>
                                    <input type="submit" value="{{ __('Save Changes') }}"
                                        class="btn btn-print-invoice  btn-primary mb-0">
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="system-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('System Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your system details') }}</small>
                        </div>

                        {{ Form::model($settings, ['route' => 'system.setting', 'method' => 'post']) }}
                        <div class="card-body">
                            <div class="row mt-3">
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency', __('Currency *'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency', null, ['class' => 'form-control font-style','placeholder'=>__('Enter Currency')]) }}
                                    @error('site_currency')
                                        <span class="invalid-site_currency" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('site_currency_symbol', __('Currency Symbol *'), ['class' => 'form-label']) }}
                                    {{ Form::text('site_currency_symbol', null, ['class' => 'form-control','placeholder'=>__('Enter Currency Symbol')]) }}
                                    @error('site_currency_symbol')
                                        <span class="invalid-site_currency_symbol" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"
                                            for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="custom-control custom-radio mb-3">
                                                    <input type="radio" id="flexRadioDefault2"
                                                        name="site_currency_symbol_position" value="pre"
                                                        class="form-check-input"
                                                        @if (@$settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                                    <label class="form-check-label"
                                                        for="flexRadioDefault2">{{ __('Pre') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="custom-control custom-radio mb-3">
                                                    <input type="radio" id="customRadio6"
                                                        name="site_currency_symbol_position" value="post"
                                                        class="form-check-input"
                                                        @if (@$settings['site_currency_symbol_position'] == 'post') checked @endif>
                                                    <label class="form-check-label"
                                                        for="customRadio6">{{ __('Post') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="site_date_format" class="form-label">{{ __('Date Format') }}</label>
                                    <select type="text" name="site_date_format" class="form-control selectric"
                                        id="site_date_format">
                                        <option value="M j, Y"
                                            @if (@$settings['site_date_format'] == 'M j, Y') selected="selected" @endif>Jan 1,2015
                                        </option>
                                        <option value="d-m-Y"
                                            @if (@$settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>dd-mm-yyyy
                                        </option>
                                        <option value="m-d-Y"
                                            @if (@$settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>mm-dd-yyyy
                                        </option>
                                        <option value="Y-m-d"
                                            @if (@$settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>yyyy-mm-dd
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="site_time_format" class="form-label">{{ __('Time Format') }}</label>
                                    <select type="text" name="site_time_format" class="form-control selectric"
                                        id="site_time_format">
                                        <option value="g:i A"
                                            @if (@$settings['site_time_format'] == 'g:i A') selected="selected" @endif>10:30 PM
                                        </option>
                                        <option value="g:i a"
                                            @if (@$settings['site_time_format'] == 'g:i a') selected="selected" @endif>10:30 pm
                                        </option>
                                        <option value="H:i"
                                            @if (@$settings['site_time_format'] == 'H:i') selected="selected" @endif>22:30
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('quote_prefix', __('Quote Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('quote_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Quote Prefix')]) }}
                                    @error('quote_prefix')
                                        <span class="invalid-quote_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('salesorder_prefix', __('Sales Order Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('salesorder_prefix', null, ['class' => 'form-control','placeholder'=>__('Enter Sales Order Prefix')]) }}
                                    @error('salesorder_prefix')
                                        <span class="invalid-salesorder_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('invoice_prefix', __('Invoice Prefix'), ['class' => 'form-label']) }}
                                    {{ Form::text('invoice_prefix', null, ['class' => 'form-control', 'placeholder' => 'Invoice Prefix','placeholder'=>__('Enter Invoice Prefix')]) }}
                                    @error('invoice_prefix')
                                        <span class="invalid-invoice_prefix" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('footer_title', __('Quote/Sales Order/Invoice Footer Title'), ['class' => 'form-label']) }}
                                    {{ Form::text('footer_title', null, ['class' => 'form-control', 'placeholder' =>__( 'Enter Footer Title')]) }}
                                    @error('footer_title')
                                        <span class="invalid-footer_title" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('shipping_display', __('Quote / Invoice / Sales-Order Shipping Display'), ['class' => 'form-label']) }}
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="shipping_display" data-toggle="switchbutton"
                                            class="custom-control-input" id="shipping_display"
                                            {{ $settings['shipping_display'] == 'on' ? 'checked="checked"' : '' }}
                                            placeholder="Invoice Footer Title">
                                        <label name="shipping_display" class="custom-control-label "
                                            for="shipping_display"></label>
                                    </div>
                                    @error('shipping_display')
                                        <span class="invalid-shipping_display" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('footer_notes', __('Quote/SalesOrder/Invoice Footer Notes'), ['class' => 'form-label']) }}
                                    {{ Form::textarea('footer_notes', null, ['class' => 'form-control', 'rows' => '3','placeholder'=>__('Enter Notes')]) }}
                                    @error('footer_notes')
                                        <span class="invalid-footer_notes" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                        </div>
                        {{ Form::close() }}
                    </div>


                    <div id="quote-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Quote Settings') }}</h5>
                            <small class="text-muted">{{ __('') }}</small>
                        </div>
                        <div class="card-body p-3">
                            <form id="setting-form" method="post" action="{{ route('quote.template.setting') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row ">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label"for="address">{{ __('Quote Template') }}</label>
                                            <select class="form-control" name="quote_template">
                                                @foreach (\App\Models\Utility::templateData()['templates'] as $key => $template)
                                                    <option value="{{ $key }}"
                                                        {{ isset($settings['quote_template']) && $settings['quote_template'] == $key ? 'selected' : '' }}>
                                                        {{ $template }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- QR code --}}
                                        <div class="form-group row">
                                            <label for="qrcode" class="col-8">{{ __('QR Display?') }}</label>
                                            <div class="form-check form-switch custom-switch-v1 col-3 ">
                                                <input type="checkbox" class="form-check-input input-primary "
                                                    name="quote_qr_enabled"
                                                    {{ isset($settings['quote_qr_enabled']) && $settings['quote_qr_enabled'] == 'on' ? 'checked="checked"' : '' }}
                                                    id="quote_qr_enabled">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Color Input') }}</label>
                                            <div class="row gutters-xs">
                                                @foreach (\App\Models\Utility::templateData()['colors'] as $key => $color)
                                                    <div class="col-auto">
                                                        <label class="colorinput">
                                                            <input name="quote_color" type="radio"
                                                                value="{{ $color }}" class="colorinput-input"
                                                                {{ isset($settings['quote_color']) && $settings['quote_color'] == $color ? 'checked' : '' }}>
                                                            <span class="colorinput-color"
                                                                style="background:#{{ $color }}"></span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">{{ __('Quote Logo') }}</label>
                                            <div class="choose-files ">
                                                <label for="quote_logo">
                                                    <div class=" bg-primary quote_logo_update"> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    {{-- <input type="file" class="form-control file" name="quote_logo" id="quote_logo" data-filename="quote_logo_update" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                        <img id="blah" src="" width="100"  /> --}}
                                                    <img id="blah1452" class="mt-3" src=""
                                                        width="70%" />
                                                    <input type="file" class="form-control file " name="quote_logo"
                                                        id="quote_logo" data-filename="quote_logo_update"
                                                        onchange="document.getElementById('blah1452').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                            </div>
                                        </div>
                                        <div clas="form-group">
                                            <input type="submit" value="Save Changes"
                                                class="btn btn-print-invoice  btn-primary m-r-10 float-end">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        @if (isset($settings['quote_template']) && isset($settings['quote_color']))
                                            <iframe id="quote_frame" class="w-100 h-100" frameborder="0"
                                                src="{{ route('quote.preview', [$settings['quote_template'], $settings['quote_color']]) }}"></iframe>
                                        @else
                                            <iframe id="quote_frame" class="w-100 h-100" frameborder="0"
                                                src="{{ route('quote.preview', ['template1', 'fffff']) }}"></iframe>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="invoice-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Invoice Settings') }}</h5>
                            <small class="text-muted">{{ __('') }}</small>
                        </div>
                        <div class="card-body">
                            <form id="setting-form" method="post" action="{{ route('invoice.template.setting') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label"for="address">{{ __('Invoice Template') }}</label>
                                            <select class="form-control" name="invoice_template">
                                                @foreach (\App\Models\Utility::templateData()['templates'] as $key => $template)
                                                    <option value="{{ $key }}"
                                                        {{ isset($settings['invoice_template']) && $settings['invoice_template'] == $key ? 'selected' : '' }}>
                                                        {{ $template }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- QR code --}}
                                        <div class="form-group row">
                                            <label for="qrcode" class="col-8">{{ __('QR Display?') }}</label>
                                            <div class="form-check form-switch custom-switch-v1 col-3 ">
                                                <input type="checkbox" class="form-check-input input-primary "
                                                    name="is_qr_enabled"
                                                    {{ isset($settings['is_qr_enabled']) && $settings['is_qr_enabled'] == 'on' ? 'checked="checked"' : '' }}
                                                    id="is_qr_enabled">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Color Input') }}</label>
                                            <div class="row gutters-xs">
                                                @foreach (\App\Models\Utility::templateData()['colors'] as $key => $color)
                                                    <div class="col-auto">
                                                        <label class="colorinput">
                                                            <input name="invoice_color" type="radio"
                                                                value="{{ $color }}" class="colorinput-input"
                                                                {{ isset($settings['invoice_color']) && $settings['invoice_color'] == $color ? 'checked' : '' }}>
                                                            <span class="colorinput-color"
                                                                style="background:#{{ $color }}"></span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">{{ __('Invoice Logo') }}</label>
                                            <div class="choose-files ">
                                                <label for="invoice_logo">
                                                    <div class=" bg-primary invoice_logo_update"> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    {{-- <input type="file" class="form-control file" name="invoice_logo" id="invoice_logo" data-filename="invoice_logo_update"> --}}
                                                    <img id="blah1454" class="mt-3" src=""
                                                        width="70%" />
                                                    <input type="file" class="form-control file"
                                                        name="invoice_logo" id="invoice_logo"
                                                        data-filename="invoice_logo_update"
                                                        onchange="document.getElementById('blah1454').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                            </div>
                                        </div>
                                        <div clas="form-group">
                                            <input type="submit" value="Save Changes"
                                                class="btn btn-print-invoice  btn-primary m-r-10 float-end">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        @if (isset($settings['invoice_template']) && isset($settings['invoice_color']))
                                            <iframe id="invoice_frame" class="w-100 h-100" frameborder="0"
                                                src="{{ route('invoice.preview', [$settings['invoice_template'], $settings['invoice_color']]) }}"></iframe>
                                        @else
                                            <iframe id="invoice_frame" class="w-100 h-100" frameborder="0"
                                                src="{{ route('invoice.preview', ['template1', 'fffff']) }}"></iframe>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="sales-order-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Sales Order Settings') }}</h5>
                            <small class="text-muted">{{ __('') }}</small>
                        </div>
                        <div class="card-body">
                            <form id="setting-form" method="post"
                                action="{{ route('salesorder.template.setting') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label
                                                class="form-label"for="address">{{ __('Sales Order Template') }}</label>
                                            <select class="form-control" name="salesorder_template">
                                                @foreach (\App\Models\Utility::templateData()['templates'] as $key => $template)
                                                    <option value="{{ $key }}"
                                                        {{ isset($settings['salesorder_template']) && $settings['salesorder_template'] == $key ? 'selected' : '' }}>
                                                        {{ $template }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- QR code --}}
                                        <div class="form-group row">
                                            <label for="qrcode" class="col-8">{{ __('QR Display?') }}</label>
                                            <div class="form-check form-switch custom-switch-v1 col-3 ">
                                                <input type="checkbox" class="form-check-input input-primary "
                                                    name="salesorder_qr_enabled"
                                                    {{ isset($settings['salesorder_qr_enabled']) && $settings['salesorder_qr_enabled'] == 'on' ? 'checked="checked"' : '' }}
                                                    id="salesorder_qr_enabled">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Color Input') }}</label>
                                            <div class="row gutters-xs">
                                                @foreach (\App\Models\Utility::templateData()['colors'] as $key => $color)
                                                    <div class="col-auto">
                                                        <label class="colorinput">
                                                            <input name="salesorder_color" type="radio"
                                                                value="{{ $color }}" class="colorinput-input"
                                                                {{ isset($settings['salesorder_color']) && $settings['salesorder_color'] == $color ? 'checked' : '' }}>
                                                            <span class="colorinput-color"
                                                                style="background: #{{ $color }}"></span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">{{ __('Sales Order Logo') }}</label>
                                            <div class="choose-files ">
                                                <label for="salesorder_logo">
                                                    <div class=" bg-primary salesorder_logo_update"> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    {{-- <input type="file" class="form-control file" name="salesorder_logo" id="salesorder_logo" data-filename="salesorder_logo_update"> --}}

                                                    <img id="blah1453" class="mt-3" src=""
                                                        width="70%" />
                                                    <input type="file" class="form-control file"
                                                        name="salesorder_logo" id="salesorder_logo"
                                                        data-filename="salesorder_logo_update"
                                                        onchange="document.getElementById('blah1453').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                            </div>
                                        </div>
                                        <div clas="form-group">
                                            <input type="submit" value="Save Changes"
                                                class="btn btn-print-invoice btn-primary m-r-10 float-end">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        @if (isset($settings['salesorder_template']) && isset($settings['salesorder_color']))
                                            <iframe id="salesorder_frame" class="w-100 h-100" frameborder="0"
                                                src="{{ route('salesorder.preview', [$settings['salesorder_template'], $settings['salesorder_color']]) }}"></iframe>
                                        @else
                                            <iframe id="salesorder_frame" class="w-100 h-100" frameborder="0"
                                                src="{{ route('salesorder.preview', ['template1', 'fffff']) }}"></iframe>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="payment-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Payment Settings') }}</h5>
                            <small
                                class="text-muted">{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration') }}</small>
                        </div>
                        {{ Form::model($settings, ['route' => 'payment.setting', 'method' => 'POST','class'=>'mb-0']) }}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label class="col-form-label">{{ __('Currency') }} *</label>
                                            <input type="text" name="currency" class="form-control"
                                                id="currency"
                                                value="{{ !isset($payment['currency']) || is_null($payment['currency']) ? '' : $payment['currency'] }}"
                                                placeholder="USD" required>
                                            <small class="text-xs">
                                                {{ __('Note: Add currency code as per three-letter ISO code.') }}.
                                                <a href="https://stripe.com/docs/currencies"
                                                    target="_blank">{{ __('You can find out how to do that here.') }}</a>
                                            </small>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                            <label for="currency_symbol"
                                                class="col-form-label">{{ __('Currency Symbol') }}</label>
                                            <input type="text" name="currency_symbol" class="form-control"
                                                id="currency_symbol"
                                                value="{{ !isset($payment['currency_symbol']) || is_null($payment['currency_symbol']) ? '' : $payment['currency_symbol'] }}"
                                                placeholder="$" required>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="faq justify-content-center">
                                <div class="col-sm-12 col-md-10 col-xxl-12">
                                    <div class="accordion accordion-flush setting setting-accordion1"
                                        id="accordionExample">

                                        {{-- bank-transfer --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-16">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse16"
                                                    aria-expanded="false" aria-controls="collapse16">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Bank Transfer') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_bank_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_bank_enabled" id="is_bank_enabled"
                                                            {{ isset($payment['is_bank_enabled']) && $payment['is_bank_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-1"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse16"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-16"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row gy-4">
                                                        <div class="col-md-12 mt-3">
                                                            <div class="form-group">
                                                                {{ Form::label('bank_details', __('Bank Details'), ['class' => 'col-form-label']) }}
                                                                {{ Form::textarea('bank_details', isset($payment['bank_details']) ? $payment['bank_details'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Your Bank Details'), 'rows' => 4]) }}
                                                                <small class="text-xs">
                                                                    {{ __('Example : Bank : bank name </br> Account Number : 0000 0000 </br>') }}
                                                                </small>
                                                                @if ($errors->has('bank_details'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('bank_details') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Strip -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-2">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse1"
                                                    aria-expanded="true" aria-controls="collapse1">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Stripe') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_stripe_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_stripe_enabled" id="is_stripe_enabled"
                                                            {{ isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-1"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse1"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-2"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">


                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>

                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block">
                                                                        <input type="hidden" name="is_stripe_enabled" value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input input-primary"
                                                                            name="is_stripe_enabled" id="is_stripe_enabled"
                                                                            {{ isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_stripe_enabled">{{ __('Enable Stripe') }}</label>
                                                                    </div>
                                                                </div> --}}
                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="stripe_key"
                                                                    class="form-label">{{ __('Stripe Key') }}</label>
                                                                <input class="form-control"
                                                                    placeholder="{{ __('Stripe Key') }}"
                                                                    name="stripe_key" type="text"
                                                                    value="{{ !isset($payment['stripe_key']) || is_null($payment['stripe_key']) ? '' : $payment['stripe_key'] }}"
                                                                    id="stripe_key">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="stripe_secret"
                                                                    class="form-label">{{ __('Stripe Secret') }}</label>
                                                                <input class="form-control "
                                                                    placeholder="{{ __('Stripe Secret') }}"
                                                                    name="stripe_secret" type="text"
                                                                    value="{{ !isset($payment['stripe_secret']) || is_null($payment['stripe_secret']) ? '' : $payment['stripe_secret'] }}"
                                                                    id="stripe_secret">
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="stripe_secret"
                                                                    class="form-label">{{ __('Stripe_Webhook_Secret') }}</label>
                                                                <input class="form-control "
                                                                    placeholder="{{ __('Enter Stripe Webhook Secret') }}"
                                                                    name="stripe_webhook_secret" type="text"
                                                                    value="{{ !isset($payment['stripe_webhook_secret']) || is_null($payment['stripe_webhook_secret']) ? '' : $payment['stripe_webhook_secret'] }}"
                                                                    id="stripe_webhook_secret">
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Paypal -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-3">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse2"
                                                    aria-expanded="true" aria-controls="collapse2">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Paypal') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paypal_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            id="customswitchv1-2" name="is_paypal_enabled"
                                                            id="is_paypal_enabled"
                                                            {{ isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse2"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-3"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_paypal_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_paypal_enabled" id="is_paypal_enabled"
                                                                            {{ isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-control-label"
                                                                            for="is_paypal_enabled">{{ __('Enable Paypal') }}</label> --}}
                                                        {{-- </div> --}}
                                                        {{-- </div> --}}
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="paypal_mode">{{ __('Paypal Mode') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="paypal_mode" value="sandbox"
                                                                                {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="paypal_mode" value="live"
                                                                                {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Live') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paypal_client_id"
                                                                    class="form-label">{{ __('Client ID') }}</label>
                                                                <input type="text" name="paypal_client_id"
                                                                    id="paypal_client_id" class="form-control"
                                                                    value="{{ !isset($payment['paypal_client_id']) || is_null($payment['paypal_client_id']) ? '' : $payment['paypal_client_id'] }}"
                                                                    placeholder="{{ __('Client ID') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paypal_secret_key"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="paypal_secret_key"
                                                                    id="paypal_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['paypal_secret_key']) || is_null($payment['paypal_secret_key']) ? '' : $payment['paypal_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Paystack -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-4">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse3"
                                                    aria-expanded="true" aria-controls="collapse3">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Paystack') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paystack_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            id="customswitchv1-2" name="is_paystack_enabled"
                                                            id="is_paystack_enabled"
                                                            {{ isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse3"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-4"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">


                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div> --}}
                                                        {{-- <div class="form-check form-switch d-inline-block">
                                                                        <input type="checkbox" class="form-check-input" name="is_paystack_enabled" id="is_paystack_enabled" {{ isset($payment_detail['is_paystack_enabled']) && $payment_detail['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>  <label class="custom-control-label form-control-label" for="is_paystack_enabled">{{__('Enable Paystack')}}</label>
                                                                    </div> --}}
                                                        {{-- <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block text-right"> --}}
                                                        {{-- <input type="hidden" name="is_paystack_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_paystack_enabled" id="is_paystack_enabled"
                                                                            {{ isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_paystack_enabled">{{ __('Enable Paystack') }}
                                                                        </label> --}}
                                                        {{-- </div>
                                                                </div> --}}

                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="paypal_client_id"
                                                                    class="form-label">{{ __('Public Key') }}</label>
                                                                <input type="text" name="paystack_public_key"
                                                                    id="paystack_public_key" class="form-control"
                                                                    value="{{ !isset($payment['paystack_public_key']) || is_null($payment['paystack_public_key']) ? '' : $payment['paystack_public_key'] }}"
                                                                    placeholder="{{ __('Public Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="paystack_secret_key"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="paystack_secret_key"
                                                                    id="paystack_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['paystack_secret_key']) || is_null($payment['paystack_secret_key']) ? '' : $payment['paystack_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FLUTTERWAVE -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-5">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse4"
                                                    aria-expanded="true" aria-controls="collapse4">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Flutterwave') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_flutterwave_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            id="customswitchv1-2" name="is_flutterwave_enabled"
                                                            id="is_flutterwave_enabled"
                                                            {{ isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse4"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-5"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">

                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block "> --}}
                                                        {{-- <input type="hidden" name="is_flutterwave_enabled"
                                                                            value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_flutterwave_enabled"
                                                                            id="is_flutterwave_enabled"
                                                                            {{ isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_flutterwave_enabled">{{ __('Enable Flutterwave') }}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}

                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="paypal_client_id"
                                                                    class="form-label">{{ __('Public Key') }}</label>
                                                                <input type="text" name="flutterwave_public_key"
                                                                    id="flutterwave_public_key" class="form-control"
                                                                    value="{{ !isset($payment['flutterwave_public_key']) || is_null($payment['flutterwave_public_key']) ? '' : $payment['flutterwave_public_key'] }}"
                                                                    placeholder="{{ __('Public Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="paystack_secret_key"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="flutterwave_secret_key"
                                                                    id="flutterwave_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['flutterwave_secret_key']) || is_null($payment['flutterwave_secret_key']) ? '' : $payment['flutterwave_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Razorpay -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-6">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse5"
                                                    aria-expanded="true" aria-controls="collapse5">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Razorpay') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_razorpay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            id="customswitchv1-2" name="is_razorpay_enabled"
                                                            id="is_razorpay_enabled"
                                                            {{ isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse5"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-6"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_razorpay_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_razorpay_enabled" id="is_razorpay_enabled"
                                                                            {{ isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_razorpay_enabled">{{ __('Enable Razorpay') }}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}

                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="paypal_client_id"
                                                                    class="form-label">{{ __('Public Key') }}</label>

                                                                <input type="text" name="razorpay_public_key"
                                                                    id="razorpay_public_key" class="form-control"
                                                                    value="{{ !isset($payment['razorpay_public_key']) || is_null($payment['razorpay_public_key']) ? '' : $payment['razorpay_public_key'] }}"
                                                                    placeholder="{{ __('Public Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="paystack_secret_key"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="razorpay_secret_key"
                                                                    id="razorpay_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['razorpay_secret_key']) || is_null($payment['razorpay_secret_key']) ? '' : $payment['razorpay_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Paytm -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-7">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse6"
                                                    aria-expanded="true" aria-controls="collapse6">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Paytm') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paytm_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_paytm_enabled" id="is_paytm_enabled"
                                                            {{ isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse6"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-7"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_paytm_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_paytm_enabled" id="is_paytm_enabled"
                                                                            {{ isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_paytm_enabled">{{ __('Enable Paytm') }}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}

                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="paytm_mode" value="local"
                                                                                {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>


                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Local') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary"
                                                                                name="paytm_mode" value="production"
                                                                                {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Production') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-12 pb-4">
                                                                        <label class="paypal-label form-control-label" for="paypal_mode">Paytm Environment</label> <br>
                                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                            <label class="btn btn-primary btn-sm {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'active' : '' }}">
                                                                                <input type="radio" name="paytm_mode" value="local" {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>Local
                                                                            </label>
                                                                            <label class="btn btn-primary btn-sm {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'active' : '' }}">
                                                                                <input type="radio" name="paytm_mode" value="production" {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>Production
                                                                            </label>
                                                                        </div>
                                                                    </div> --}}
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="paytm_public_key"
                                                                    class="form-label">{{ __('Merchant ID') }}</label>
                                                                <input type="text" name="paytm_merchant_id"
                                                                    id="paytm_merchant_id" class="form-control"
                                                                    value="{{ !isset($payment['paytm_merchant_id']) || is_null($payment['paytm_merchant_id']) ? '' : $payment['paytm_merchant_id'] }}"
                                                                    placeholder="{{ __('Merchant ID') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="paytm_secret_key"
                                                                    class="form-label">{{ __('Merchant Key') }}</label>
                                                                <input type="text" name="paytm_merchant_key"
                                                                    id="paytm_merchant_key" class="form-control"
                                                                    value="{{ !isset($payment['paytm_merchant_key']) || is_null($payment['paytm_merchant_key']) ? '' : $payment['paytm_merchant_key'] }}"
                                                                    placeholder="{{ __('Merchant Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="paytm_industry_type"
                                                                    class="form-label">{{ __('Industry Type') }}</label>
                                                                <input type="text" name="paytm_industry_type"
                                                                    id="paytm_industry_type" class="form-control"
                                                                    value="{{ !isset($payment['paytm_industry_type']) || is_null($payment['paytm_industry_type']) ? '' : $payment['paytm_industry_type'] }}"
                                                                    placeholder="{{ __('Industry Type') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mercado Pago-->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-8">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse7"
                                                    aria-expanded="true" aria-controls="collapse7">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Mercado Pago') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_mercado_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_mercado_enabled" id="is_mercado_enabled"
                                                            {{ isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse7"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-8"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_mercado_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_mercado_enabled" id="is_mercado_enabled"
                                                                            {{ isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_mercado_enabled">{{ __('Enable Mercado Pago') }}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="paypal_mode">{{ __('Mercado Mode') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="mercado_mode" value="sandbox"
                                                                                {{ (isset($payment['mercado_mode']) && $payment['mercado_mode'] == '') || (isset($payment['mercado_mode']) && $payment['mercado_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>


                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="mercado_mode" value="live"
                                                                                {{ isset($payment['mercado_mode']) && $payment['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Live') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="mercado_access_token"
                                                                    class="form-label">{{ __('Access Token') }}</label>
                                                                <input type="text" name="mercado_access_token"
                                                                    id="mercado_access_token" class="form-control"
                                                                    value="{{ isset($payment['mercado_access_token']) ? $payment['mercado_access_token'] : '' }}" />
                                                                @if ($errors->has('mercado_secret_key'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('mercado_access_token') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mollie -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-9">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse8"
                                                    aria-expanded="true" aria-controls="collapse8">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Mollie') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_mollie_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_mollie_enabled" id="is_mollie_enabled"
                                                            {{ isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse8"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-9"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_mollie_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_mollie_enabled" id="is_mollie_enabled"
                                                                            {{ isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_mollie_enabled">{{__('Enable Mollie')}}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}
                                                        <div class="row mt-2">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="mollie_api_key"
                                                                        class="form-label">{{ __('Mollie Api Key') }}</label>
                                                                    <input type="text" name="mollie_api_key"
                                                                        id="mollie_api_key" class="form-control"
                                                                        value="{{ !isset($payment['mollie_api_key']) || is_null($payment['mollie_api_key']) ? '' : $payment['mollie_api_key'] }}"
                                                                        placeholder="{{ __('Mollie Api Key') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="mollie_profile_id"
                                                                        class="form-label">{{ __('Mollie Profile Id') }}</label>
                                                                    <input type="text" name="mollie_profile_id"
                                                                        id="mollie_profile_id" class="form-control"
                                                                        value="{{ !isset($payment['mollie_profile_id']) || is_null($payment['mollie_profile_id']) ? '' : $payment['mollie_profile_id'] }}"
                                                                        placeholder="{{ __('Mollie Profile Id') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="mollie_partner_id"
                                                                        class="form-label">{{ __('Mollie Partner Id') }}</label>
                                                                    <input type="text" name="mollie_partner_id"
                                                                        id="mollie_partner_id" class="form-control"
                                                                        value="{{ !isset($payment['mollie_partner_id']) || is_null($payment['mollie_partner_id']) ? '' : $payment['mollie_partner_id'] }}"
                                                                        placeholder="{{ __('Mollie Partner Id') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Skrill -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-10">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse9"
                                                    aria-expanded="true" aria-controls="collapse9">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Skrill') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_skrill_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_skrill_enabled" id="is_skrill_enabled"
                                                            {{ isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse9"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-10"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_skrill_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_skrill_enabled" id="is_skrill_enabled"
                                                                            {{ isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_skrill_enabled">{{__('Enable Skrill')}}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}
                                                        <div class="col-md-6 mt-3">
                                                            <div class="form-group">
                                                                <label for="mollie_api_key"
                                                                    class="form-label">{{ __('Skrill Email') }}</label>
                                                                <input type="text" name="skrill_email"
                                                                    id="skrill_email" class="form-control"
                                                                    value="{{ !isset($payment['skrill_email']) || is_null($payment['skrill_email']) ? '' : $payment['skrill_email'] }}"
                                                                    placeholder="{{ __('Skrill Email') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CoinGate -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-11">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse10"
                                                    aria-expanded="true" aria-controls="collapse10">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('CoinGate') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_coingate_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_coingate_enabled" id="is_coingate_enabled"
                                                            {{ isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse10"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-11"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_coingate_enabled" value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_coingate_enabled" id="is_coingate_enabled"
                                                                            {{ isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-control-label"
                                                                            for="is_coingate_enabled">{{__('Enable CoinGate')}}</label> --}}
                                                        {{-- </div>
                                                                </div> --}}
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="paypal_mode">{{ __('CoinGate Mode') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary"
                                                                                name="coingate_mode" value="sandbox"
                                                                                {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>


                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary"
                                                                                name="coingate_mode" value="live"
                                                                                {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Live') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-12 pb-4">
                                                                        <label class="coingate-label form-control-label" for="coingate_mode">CoinGate Mode</label> <br>
                                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                            <label class="btn btn-primary btn-sm {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                <input type="radio" name="coingate_mode" value="sandbox" {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>Sandbox
                                                                            </label>
                                                                            <label class="btn btn-primary btn-sm {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'active' :'' }}">
                                                                                <input type="radio" name="coingate_mode" value="live" {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>Live
                                                                            </label>
                                                                        </div>
                                                                    </div> --}}
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="coingate_auth_token"
                                                                    class="form-label">{{ __('CoinGate Auth Token') }}</label>
                                                                <input type="text" name="coingate_auth_token"
                                                                    id="coingate_auth_token" class="form-control"
                                                                    value="{{ !isset($payment['coingate_auth_token']) || is_null($payment['coingate_auth_token']) ? '' : $payment['coingate_auth_token'] }}"
                                                                    placeholder="{{ __('CoinGate Auth Token') }}">
                                                            </div>
                                                            @if ($errors->has('coingate_auth_token'))
                                                                <span class="invalid-feedback d-block">
                                                                    {{ $errors->first('coingate_auth_token') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PaymentWall -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-12">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse11"
                                                    aria-expanded="true" aria-controls="collapse11">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('PaymentWall') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paymentwall_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_paymentwall_enabled" id="is_paymentwall_enabled"
                                                            {{ isset($payment['is_paymentwall_enabled']) && $payment['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse11"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-12"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        {{-- <div class="col-6 py-2">
                                                                </div>
                                                                <div class="col-6 py-2 text-end">
                                                                    <div class="form-check form-switch d-inline-block"> --}}
                                                        {{-- <input type="hidden" name="is_paymentwall_enabled"
                                                                            value="off">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            name="is_paymentwall_enabled"
                                                                            id="is_paymentwall_enabled"
                                                                            {{ isset($payment['is_paymentwall_enabled']) && $payment['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label form-label"
                                                                            for="is_paymentwall_enabled">{{ __('Enable PaymentWall') }}
                                                                        </label> --}}
                                                        {{-- </div>
                                                                </div> --}}

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paymentwall_public_key"
                                                                    class="form-label">{{ __('Public Key') }}</label>
                                                                <input type="text" name="paymentwall_public_key"
                                                                    id="paymentwall_public_key" class="form-control"
                                                                    value="{{ !isset($payment['paymentwall_public_key']) || is_null($payment['paymentwall_public_key']) ? '' : $payment['paymentwall_public_key'] }}"
                                                                    placeholder="{{ __('Public Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paymentwall_private_key"
                                                                    class="form-label">{{ __('Private Key') }}</label>
                                                                <input type="text" name="paymentwall_private_key"
                                                                    id="paymentwall_private_key" class="form-control"
                                                                    value="{{ !isset($payment['paymentwall_private_key']) || is_null($payment['paymentwall_private_key']) ? '' : $payment['paymentwall_private_key'] }}"
                                                                    placeholder="{{ __('Private Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Toyyibpay --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-13">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse12"
                                                    aria-expanded="true" aria-controls="collapse12">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Toyyibpay') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_toyyibpay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_toyyibpay_enabled" id="is_toyyibpay_enabled"
                                                            {{ isset($payment['is_toyyibpay_enabled']) && $payment['is_toyyibpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label for="customswitch1-2" class="form-check-label"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse12"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-13"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="toyyibpay_secret_key"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="toyyibpay_secret_key"
                                                                    id="toyyibpay_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['toyyibpay_secret_key']) || is_null($payment['toyyibpay_secret_key']) ? '' : $payment['toyyibpay_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="category_code"
                                                                    class="form-label">{{ __('Category Code') }}</label>
                                                                <input type="text" name="category_code"
                                                                    id="category_code" class="form-control"
                                                                    value="{{ !isset($payment['category_code']) || is_null($payment['category_code']) ? '' : $payment['category_code'] }}"
                                                                    placeholder="{{ __('Category Code') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PayFast --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-14">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse14"
                                                    aria-expanded="true" aria-controls="collapse14">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('Payfast') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_payfast_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_payfast_enabled" id="is_payfast_enabled"
                                                            {{ isset($payment['is_payfast_enabled']) && $payment['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="collapse14"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-14"
                                                data-bs-parent="#accordionExample">

                                                <div class="accordion-body">
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                        <div class="row pt-2">
                                                            <label class="pb-2"
                                                                for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                            <div class="col-lg-3">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <input type="radio"
                                                                            class="form-check-input input-primary "
                                                                            name="payfast_mode" value="sandbox"
                                                                            {{ !isset($payment['payfast_mode']) || $payment['payfast_mode'] == '' || $payment['payfast_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label d-block"
                                                                            for="">
                                                                            <span>
                                                                                <span class="h5 d-block"><strong
                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <input type="radio"
                                                                            class="form-check-input input-primary "
                                                                            name="payfast_mode" value="live"
                                                                            {{ isset($payment['payfast_mode']) && $payment['payfast_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label d-block"
                                                                            for="">
                                                                            <span>
                                                                                <span class="h5 d-block"><strong
                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="payfast_merchant_id"
                                                                    class="form-label">{{ __('Merchant Id') }}</label>
                                                                <input type="text" name="payfast_merchant_id"
                                                                    id="payfast_merchant_id" class="form-control"
                                                                    value="{{ !isset($payment['payfast_merchant_id']) || is_null($payment['payfast_merchant_id']) ? '' : $payment['payfast_merchant_id'] }}"
                                                                    placeholder="{{ __('Merchant Id') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="payfast_merchant_key"
                                                                    class="form-label">{{ __('Merchant Key') }}</label>
                                                                <input type="text" name="payfast_merchant_key"
                                                                    id="payfast_merchant_key" class="form-control"
                                                                    value="{{ !isset($payment['payfast_merchant_key']) || is_null($payment['payfast_merchant_key']) ? '' : $payment['payfast_merchant_key'] }}"
                                                                    placeholder="{{ __('Merchant Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="payfast_signature"
                                                                    class="form-label">{{ __('Salt Passphrase') }}</label>
                                                                <input type="text" name="payfast_signature"
                                                                    id="payfast_signature" class="form-control"
                                                                    value="{{ !isset($payment['payfast_signature']) || is_null($payment['payfast_signature']) ? '' : $payment['payfast_signature'] }}"
                                                                    placeholder="{{ __('Salt Passphrase') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- iyzipay --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-15">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse15"
                                                    aria-expanded="true" aria-controls="collapse15">
                                                    <span class="d-flex align-items-center">
                                                        {{-- <i class="ti ti-credit-card text-primary"></i> --}}
                                                        {{ __('IyziPay') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_iyzipay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_iyzipay_enabled" id="is_iyzipay_enabled"
                                                            {{ isset($payment['is_iyzipay_enabled']) && $payment['is_iyzipay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="collapse15"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-14"
                                                data-bs-parent="#accordionExample">

                                                <div class="accordion-body">
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                        <div class="row pt-2">
                                                            <label class="pb-2"
                                                                for="iyzipay_mode">{{ __('IyziPay Mode') }}</label>
                                                            <div class="col-lg-3">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <input type="radio"
                                                                            class="form-check-input input-primary "
                                                                            name="iyzipay_mode" value="sandbox"
                                                                            {{ !isset($payment['iyzipay_mode']) || $payment['iyzipay_mode'] == '' || $payment['iyzipay_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label d-block"
                                                                            for="">
                                                                            <span>
                                                                                <span class="h5 d-block"><strong
                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="border card p-3">
                                                                    <div class="form-check">
                                                                        <input type="radio"
                                                                            class="form-check-input input-primary "
                                                                            name="iyzipay_mode" value="live"
                                                                            {{ isset($payment['iyzipay_mode']) && $payment['iyzipay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label d-block"
                                                                            for="">
                                                                            <span>
                                                                                <span class="h5 d-block"><strong
                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="iyzipay_key"
                                                                    class="form-label">{{ __('IyziPay Key') }}</label>
                                                                <input type="text" name="iyzipay_key"
                                                                    id="iyzipay_key" class="form-control"
                                                                    value="{{ !isset($payment['iyzipay_key']) || is_null($payment['iyzipay_key']) ? '' : $payment['iyzipay_key'] }}"
                                                                    placeholder="{{ __('IyziPay Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="iyzipay_secret"
                                                                    class="form-label">{{ __('IyziPay Secret') }}</label>
                                                                <input type="text" name="iyzipay_secret"
                                                                    id="iyzipay_secret" class="form-control"
                                                                    value="{{ !isset($payment['iyzipay_secret']) || is_null($payment['iyzipay_secret']) ? '' : $payment['iyzipay_secret'] }}"
                                                                    placeholder="{{ __('IyziPay Secret') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SSPay --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-16">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse16"
                                                    aria-expanded="true" aria-controls="collapse16">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('SSPay') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_sspay_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_sspay_enabled" id="is_sspay_enabled"
                                                            {{ isset($payment['is_sspay_enabled']) && $payment['is_sspay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label for="customswitch1-2" class="form-check-label"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse16"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-16"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="sspay_secret_key"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="sspay_secret_key"
                                                                    id="sspay_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['sspay_secret_key']) || is_null($payment['sspay_secret_key']) ? '' : $payment['sspay_secret_key'] }}"
                                                                    placeholder="{{ __('Secret Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="sspay_category_code"
                                                                    class="form-label">{{ __('Category Code') }}</label>
                                                                <input type="text" name="sspay_category_code"
                                                                    id="sspay_category_code" class="form-control"
                                                                    value="{{ !isset($payment['sspay_category_code']) || is_null($payment['sspay_category_code']) ? '' : $payment['sspay_category_code'] }}"
                                                                    placeholder="{{ __('Category Code') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PayTab  --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-17">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse17"
                                                    aria-expanded="true" aria-controls="collapse17">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('PayTab') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paytab_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_paytab_enabled" id="is_paytab_enabled"
                                                            {{ isset($payment['is_paytab_enabled']) && $payment['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label for="customswitch1-2" class="form-check-label"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse17"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-17"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytab_profile_id"
                                                                    class="form-label">{{ __('Profile Id') }}</label>
                                                                <input type="text" name="paytab_profile_id"
                                                                    id="paytab_profile_id" class="form-control"
                                                                    value="{{ !isset($payment['paytab_profile_id']) || is_null($payment['paytab_profile_id']) ? '' : $payment['paytab_profile_id'] }}"
                                                                    placeholder="{{ __('Profile Id') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytab_server_key"
                                                                    class="form-label">{{ __('Server Key') }}</label>
                                                                <input type="text" name="paytab_server_key"
                                                                    id="paytab_server_key" class="form-control"
                                                                    value="{{ !isset($payment['paytab_server_key']) || is_null($payment['paytab_server_key']) ? '' : $payment['paytab_server_key'] }}"
                                                                    placeholder="{{ __('Server Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytab_region"
                                                                    class="form-label">{{ __('Paytab Region') }}</label>
                                                                <input type="text" name="paytab_region"
                                                                    id="paytab_region" class="form-control"
                                                                    value="{{ !isset($payment['paytab_region']) || is_null($payment['paytab_region']) ? '' : $payment['paytab_region'] }}"
                                                                    placeholder="{{ __('Paytab Region') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Benefit  --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-18">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse18"
                                                    aria-expanded="true" aria-controls="collapse18">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Benefit') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_benefit_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_benefit_enabled" id="is_benefit_enabled"
                                                            {{ isset($payment['is_benefit_enabled']) && $payment['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label for="customswitch1-2" class="form-check-label"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse18"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-18"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="benefit_api_key"
                                                                    class="form-label">{{ __('Benefit Key') }}</label>
                                                                <input type="text" name="benefit_api_key"
                                                                    id="benefit_api_key" class="form-control"
                                                                    value="{{ !isset($payment['benefit_api_key']) || is_null($payment['benefit_api_key']) ? '' : $payment['benefit_api_key'] }}"
                                                                    placeholder="{{ __('Enter Benefit Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="benefit_secret_key"
                                                                    class="form-label">{{ __('Benefit Secret Key') }}</label>
                                                                <input type="text" name="benefit_secret_key"
                                                                    id="benefit_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['benefit_secret_key']) || is_null($payment['benefit_secret_key']) ? '' : $payment['benefit_secret_key'] }}"
                                                                    placeholder="{{ __('Enter Benefit Secret key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Cashfree  --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-19">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse19"
                                                    aria-expanded="true" aria-controls="collapse19">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Cashfree') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_cashfree_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_cashfree_enabled" id="is_cashfree_enabled"
                                                            {{ isset($payment['is_cashfree_enabled']) && $payment['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label for="customswitch1-2" class="form-check-label"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse19"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-19"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="cashfree_api_key"
                                                                    class="form-label">{{ __(' Cashfree Key') }}</label>
                                                                <input type="text" name="cashfree_api_key"
                                                                    id="cashfree_api_key" class="form-control"
                                                                    value="{{ !isset($payment['cashfree_api_key']) || is_null($payment['cashfree_api_key']) ? '' : $payment['cashfree_api_key'] }}"
                                                                    placeholder="{{ __('Enter Cashfree Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="cashfree_secret_key"
                                                                    class="form-label">{{ __('Cashfree Secret Key') }}</label>
                                                                <input type="text" name="cashfree_secret_key"
                                                                    id="cashfree_secret_key" class="form-control"
                                                                    value="{{ !isset($payment['cashfree_secret_key']) || is_null($payment['cashfree_secret_key']) ? '' : $payment['cashfree_secret_key'] }}"
                                                                    placeholder="{{ __('Enter Cashfree Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Aamarpay  --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-20">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse20"
                                                    aria-expanded="true" aria-controls="collapse20">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Aamarpay') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_aamarpay_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_aamarpay_enabled" id="is_aamarpay_enabled"
                                                            {{ isset($payment['is_aamarpay_enabled']) && $payment['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label for="customswitch1-2" class="form-check-label"></label>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse20"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-20"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="aamarpay_store_id"
                                                                    class="form-label">{{ __(' Store Id') }}</label>
                                                                <input type="text" name="aamarpay_store_id"
                                                                    id="aamarpay_store_id" class="form-control"
                                                                    value="{{ !isset($payment['aamarpay_store_id']) || is_null($payment['aamarpay_store_id']) ? '' : $payment['aamarpay_store_id'] }}"
                                                                    placeholder="{{ __('Enter Store Id') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="aamarpay_signature_key"
                                                                    class="form-label">{{ __('Signature Key') }}</label>
                                                                <input type="text" name="aamarpay_signature_key"
                                                                    id="aamarpay_signature_key" class="form-control"
                                                                    value="{{ !isset($payment['aamarpay_signature_key']) || is_null($payment['aamarpay_signature_key']) ? '' : $payment['aamarpay_signature_key'] }}"
                                                                    placeholder="{{ __('Enter Signature Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="aamarpay_description"
                                                                    class="form-label">{{ __('Description') }}</label>
                                                                <input type="text" name="aamarpay_description"
                                                                    id="aamarpay_description" class="form-control"
                                                                    value="{{ !isset($payment['aamarpay_description']) || is_null($payment['aamarpay_description']) ? '' : $payment['aamarpay_description'] }}"
                                                                    placeholder="{{ __('Enter Signature Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PayTR --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-21">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse21"
                                                    aria-expanded="true" aria-controls="collapse21">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Pay TR') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_paytr_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_paytr_enabled" id="is_paytr_enabled"
                                                            {{ isset($payment['is_paytr_enabled']) && $payment['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="collapse21"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-21"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytr_merchant_id"
                                                                    class="form-label">{{ __('Merchant Id') }}</label>
                                                                <input type="text" name="paytr_merchant_id"
                                                                    id="paytr_merchant_id" class="form-control"
                                                                    value="{{ !isset($payment['paytr_merchant_id']) || is_null($payment['paytr_merchant_id']) ? '' : $payment['paytr_merchant_id'] }}"
                                                                    placeholder="{{ __('Enter Merchant Id') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytr_merchant_key"
                                                                    class="form-label">{{ __('Merchant Key') }}</label>
                                                                <input type="text" name="paytr_merchant_key"
                                                                    id="paytr_merchant_key" class="form-control"
                                                                    value="{{ !isset($payment['paytr_merchant_key']) || is_null($payment['paytr_merchant_key']) ? '' : $payment['paytr_merchant_key'] }}"
                                                                    placeholder="{{ __('Enter Merchant Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paytr_merchant_salt"
                                                                    class="form-label">{{ __('Salt Passphrase') }}</label>
                                                                <input type="text" name="paytr_merchant_salt"
                                                                    id="paytr_merchant_salt" class="form-control"
                                                                    value="{{ !isset($payment['paytr_merchant_salt']) || is_null($payment['paytr_merchant_salt']) ? '' : $payment['paytr_merchant_salt'] }}"
                                                                    placeholder="{{ __('Enter Salt Passphrase') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Yookassa --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-21">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse22"
                                                    aria-expanded="true" aria-controls="collapse22">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Yookassa') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_yookassa_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_yookassa_enabled" id="is_yookassa_enabled"
                                                            {{ isset($payment['is_yookassa_enabled']) && $payment['is_yookassa_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="collapse22"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-22"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="yookassa_shop_id"
                                                                    class="form-label">{{ __('Shop ID Key') }}</label>
                                                                <input type="text" name="yookassa_shop_id"
                                                                    id="yookassa_shop_id" class="form-control"
                                                                    value="{{ !isset($payment['yookassa_shop_id']) || is_null($payment['yookassa_shop_id']) ? '' : $payment['yookassa_shop_id'] }}"
                                                                    placeholder="{{ __('Enter Shop ID Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="yookassa_secret"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="yookassa_secret"
                                                                    id="yookassa_secret" class="form-control"
                                                                    value="{{ !isset($payment['yookassa_secret']) || is_null($payment['yookassa_secret']) ? '' : $payment['yookassa_secret'] }}"
                                                                    placeholder="{{ __('Enter Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- Midtrans --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-23">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse23"
                                                    aria-expanded="true" aria-controls="collapse23">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Midtrans') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_midtrans_enabled"
                                                            value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_midtrans_enabled" id="is_midtrans_enabled"
                                                            {{ isset($payment['is_midtrans_enabled']) && $payment['is_midtrans_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="collapse23"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-23"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                            <div class="row pt-2">
                                                                <label class="pb-2"
                                                                    for="midtrans_mode">{{ __('Midtrans Mode') }}</label>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="midtrans_mode" value="sandbox"
                                                                                {{ !isset($payment['midtrans_mode']) || $payment['midtrans_mode'] == '' || $payment['midtrans_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border card p-3">
                                                                        <div class="form-check">
                                                                            <input type="radio"
                                                                                class="form-check-input input-primary "
                                                                                name="midtrans_mode" value="live"
                                                                                {{ isset($payment['midtrans_mode']) && $payment['midtrans_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label d-block"
                                                                                for="">
                                                                                <span>
                                                                                    <span class="h5 d-block"><strong
                                                                                            class="float-end"></strong>{{ __('Live') }}</span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="midtrans_secret"
                                                                    class="form-label">{{ __('Secret Key') }}</label>
                                                                <input type="text" name="midtrans_secret"
                                                                    id="midtrans_secret" class="form-control"
                                                                    value="{{ !isset($payment['midtrans_secret']) || is_null($payment['midtrans_secret']) ? '' : $payment['midtrans_secret'] }}"
                                                                    placeholder="{{ __('Enter Secret Key') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Xendit --}}
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="heading-2-24">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse24"
                                                    aria-expanded="true" aria-controls="collapse24">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Xendit') }}
                                                    </span>
                                                    {{ __('Enable:') }}
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        <input type="hidden" name="is_xendit_enabled" value="off">
                                                        <input type="checkbox" class="form-check-input input-primary"
                                                            name="is_xendit_enabled" id="is_xendit_enabled"
                                                            {{ isset($payment['is_xendit_enabled']) && $payment['is_xendit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="form-check-label" for="customswitchv1-2"></label>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="collapse24"
                                                class="accordion-collapse collapse"aria-labelledby="heading-2-24"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="xendit_api"
                                                                    class="form-label">{{ __('API Key') }}</label>
                                                                <input type="text" name="xendit_api"
                                                                    id="xendit_api" class="form-control"
                                                                    value="{{ !isset($payment['xendit_api']) || is_null($payment['xendit_api']) ? '' : $payment['xendit_api'] }}"
                                                                    placeholder="{{ __('Enter API Key') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="xendit_token"
                                                                    class="form-label">{{ __('Token') }}</label>
                                                                <input type="text" name="xendit_token"
                                                                    id="xendit_token" class="form-control"
                                                                    value="{{ !isset($payment['xendit_token']) || is_null($payment['xendit_token']) ? '' : $payment['xendit_token'] }}"
                                                                    placeholder="{{ __('Enter Token') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nepalste -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="headingTwenty-six">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseTwenty-six"
                                                    aria-expanded="true" aria-controls="collapseTwenty-six">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Nepalste') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <label class="form-check-label m-1"
                                                            for="is_nepalste_enabled">{{ __('Enable') }}</label>
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_nepalste_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_nepalste_enabled" id="is_nepalste_enabled"
                                                                {{ isset($payment['is_nepalste_enabled']) && $payment['is_nepalste_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseTwenty-six" class="accordion-collapse collapse"
                                                aria-labelledby="headingTwenty-six" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row pt-2">
                                                        <div class="col-md-12 pb-4 form-group">
                                                            <label class="nepalste-label form-label"
                                                                for="nepalste_mode">{{ __('Nepalste Mode') }}</label>
                                                            <br>
                                                            <div class="d-flex">
                                                                <div class="col-lg-3" style="margin-right: 15px;">
                                                                    <div class="border accordion-header p-3">
                                                                        <div class="form-check">
                                                                            <label class="form-check-label text-dark">
                                                                                <input type="radio"
                                                                                    name="nepalste_mode"
                                                                                    value="sandbox"
                                                                                    class="form-check-input"
                                                                                    {{ !isset($admin_payment_setting['nepalste_mode']) || $admin_payment_setting['nepalste_mode'] == '' || $admin_payment_setting['nepalste_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                                                {{ __('Sandbox') }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3">
                                                                    <div class="border accordion-header p-3">
                                                                        <div class="form-check">
                                                                            <label class="form-check-label text-dark">
                                                                                <input type="radio"
                                                                                    name="nepalste_mode"
                                                                                    value="live"
                                                                                    class="form-check-input"
                                                                                    {{ isset($admin_payment_setting['nepalste_mode']) && $admin_payment_setting['nepalste_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                {{ __('Live') }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('nepalste_public_key', __('Public Key'), ['class' => 'form-label']) }}
                                                                {{ Form::text('nepalste_public_key', isset($payment['nepalste_public_key']) ? $payment['nepalste_public_key'] : '', ['class' => 'form-control', 'placeholder' => __('Public Key')]) }}<br>
                                                                @if ($errors->has('nepalste_public_key'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('nepalste_public_key') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('nepalste_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                {{ Form::text('nepalste_secret_key', isset($payment['nepalste_secret_key']) ? $payment['nepalste_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}<br>
                                                                @if ($errors->has('nepalste_secret_key'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('nepalste_secret_key') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Paiement Pro -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="headingTwenty-seven">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseTwenty-seven"
                                                    aria-expanded="true" aria-controls="collapseTwenty-seven">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Paiement Pro') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <label class="form-check-label m-1"
                                                            for="is_paiementpro_enabled">{{ __('Enable') }}</label>
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_paiementpro_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_paiementpro_enabled"
                                                                id="is_paiementpro_enabled"
                                                                {{ isset($payment['is_paiementpro_enabled']) && $payment['is_paiementpro_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseTwenty-seven" class="accordion-collapse collapse"
                                                aria-labelledby="headingTwenty-seven"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row pt-2">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('paiementpro_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                {{ Form::text('paiementpro_merchant_id', isset($payment['paiementpro_merchant_id']) ? $payment['paiementpro_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                @if ($errors->has('paiementpro_merchant_id'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('paiementpro_merchant_id') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fedapay -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="headingTwenty-eight">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseTwenty-eight"
                                                    aria-expanded="false" aria-controls="collapseTwenty-eight">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('Fedapay') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable') }}:</span>
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_fedapay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                id="customswitchv1-1 is_fedapay_enabled"
                                                                name="is_fedapay_enabled"
                                                                {{ isset($payment['is_fedapay_enabled']) && $payment['is_fedapay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseTwenty-eight" class="accordion-collapse collapse"
                                                aria-labelledby="headingTwenty-eight"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="d-flex">
                                                        <div class="mr-2" style="margin-right: 15px;">
                                                            <div class="border card p-1">
                                                                <div class="form-check">
                                                                    <label class="form-check-labe text-dark">
                                                                        <input type="radio" name="fedapay_mode"
                                                                            value="sandbox" class="form-check-input"
                                                                            {{ (isset($payment['fedapay_mode']) && $payment['fedapay_mode'] == '') || (isset($payment['fedapay_mode']) && $payment['fedapay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                        {{ __('Sandbox') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mr-2">
                                                            <div class="border card p-1">
                                                                <div class="form-check">
                                                                    <label class="form-check-labe text-dark">
                                                                        <input type="radio" name="fedapay_mode"
                                                                            value="live" class="form-check-input"
                                                                            {{ isset($payment['fedapay_mode']) && $payment['fedapay_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                        {{ __('Live') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row gy-4">
                                                        <div class="col-lg-6">
                                                            <div class="input-edits">
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="fedapay_public_key">{{ __('Public Key') }}</label>
                                                                    <input type="text" name="fedapay_public_key"
                                                                        id="fedapay_public_key" class="form-control"
                                                                        value="{{ !isset($payment['fedapay_public_key']) || is_null($payment['fedapay_public_key']) ? '' : $payment['fedapay_public_key'] }}"
                                                                        placeholder="{{ __('Public Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-edits">
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="fedapay_secret_key">{{ __('Secret Key') }}</label>
                                                                    <input type="text" name="fedapay_secret_key"
                                                                        id="fedapay_secret_key" class="form-control"
                                                                        value="{{ isset($payment['fedapay_secret_key']) ? $payment['fedapay_secret_key'] : '' }}"
                                                                        placeholder="{{ __('Secret Key') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PayHere -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="headingTwenty-nine">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseTwenty-nine"
                                                    aria-expanded="false" aria-controls="collapseTwenty-nine">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('PayHere') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ __('Enable') }}:</span>
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_payhere_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                id="customswitchv1-1 is_payhere_enabled"
                                                                name="is_payhere_enabled"
                                                                {{ isset($payment['is_payhere_enabled']) && $payment['is_payhere_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseTwenty-nine" class="accordion-collapse collapse"
                                                aria-labelledby="headingTwenty-nine" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="d-flex">
                                                        <div class="mr-2" style="margin-right: 15px;">
                                                            <div class="border card p-1">
                                                                <div class="form-check">
                                                                    <label class="form-check-labe text-dark">
                                                                        <input type="radio" name="payhere_mode"
                                                                            value="sandbox" class="form-check-input"
                                                                            {{ (isset($payment['payhere_mode']) && $payment['payhere_mode'] == '') || (isset($payment['payhere_mode']) && $payment['payhere_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                        {{ __('Sandbox') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mr-2">
                                                            <div class="border card p-1">
                                                                <div class="form-check">
                                                                    <label class="form-check-labe text-dark">
                                                                        <input type="radio" name="payhere_mode"
                                                                            value="live" class="form-check-input"
                                                                            {{ isset($payment['payhere_mode']) && $payment['payhere_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                        {{ __('Live') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row gy-4">
                                                        <div class="col-lg-6">
                                                            <div class="input-edits">
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="payhere_merchant_id">{{ __('Merchant ID') }}</label>
                                                                    <input type="text" name="payhere_merchant_id"
                                                                        id="payhere_merchant_id" class="form-control"
                                                                        value="{{ !isset($payment['payhere_merchant_id']) || is_null($payment['payhere_merchant_id']) ? '' : $payment['payhere_merchant_id'] }}"
                                                                        placeholder="{{ __('Merchant ID') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-edits">
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="payhere_merchant_secret">{{ __('Merchant Secret') }}</label>
                                                                    <input type="text" name="payhere_merchant_secret"
                                                                        id="payhere_merchant_secret"
                                                                        class="form-control"
                                                                        value="{{ isset($payment['payhere_merchant_secret']) ? $payment['payhere_merchant_secret'] : '' }}"
                                                                        placeholder="{{ __('Merchant Secret') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-edits">
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="payhere_app_id">{{ __('App ID') }}</label>
                                                                    <input type="text" name="payhere_app_id"
                                                                        id="payhere_app_id" class="form-control"
                                                                        value="{{ isset($payment['payhere_app_id']) ? $payment['payhere_app_id'] : '' }}"
                                                                        placeholder="{{ __('App ID') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-edits">
                                                                <div class="form-group">
                                                                    <label class="col-form-label"
                                                                        for="payhere_app_secret">{{ __('App Secret') }}</label>
                                                                    <input type="text" name="payhere_app_secret"
                                                                        id="payhere_app_secret" class="form-control"
                                                                        value="{{ isset($payment['payhere_app_secret']) ? $payment['payhere_app_secret'] : '' }}"
                                                                        placeholder="{{ __('App Secret') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CinetPay -->
                                        <div class="accordion-item card">
                                            <h2 class="accordion-header" id="headingThirty">
                                                <button class="accordion-button" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseThirty"
                                                    aria-expanded="true" aria-controls="collapseThirty">
                                                    <span class="d-flex align-items-center">
                                                        {{ __('CinetPay') }}
                                                    </span>
                                                    <div class="d-flex align-items-center">
                                                        <label class="form-check-label m-1"
                                                            for="is_cinetpay_enabled">{{ __('Enable') }}</label>
                                                        <div class="form-check form-switch custom-switch-v1">
                                                            <input type="hidden" name="is_cinetpay_enabled"
                                                                value="off">
                                                            <input type="checkbox"
                                                                class="form-check-input input-primary"
                                                                name="is_cinetpay_enabled" id="is_cinetpay_enabled"
                                                                {{ isset($payment['is_cinetpay_enabled']) && $payment['is_cinetpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapseThirty" class="accordion-collapse collapse"
                                                aria-labelledby="headingThirty" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="row pt-2">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('cinetpay_api_key', __('CinetPay API Key'), ['class' => 'form-label']) }}
                                                                {{ Form::text('cinetpay_api_key', isset($payment['cinetpay_api_key']) ? $payment['cinetpay_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('CinetPay API Key')]) }}<br>
                                                                @if ($errors->has('cinetpay_api_key'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('cinetpay_api_key') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{ Form::label('cinetpay_site_id', __('CinetPay Site ID'), ['class' => 'form-label']) }}
                                                                {{ Form::text('cinetpay_site_id', isset($payment['cinetpay_site_id']) ? $payment['cinetpay_site_id'] : '', ['class' => 'form-control', 'placeholder' => __('CinetPay Site ID')]) }}<br>
                                                                @if ($errors->has('cinetpay_site_id'))
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $errors->first('cinetpay_site_id') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end pb-0">
                                {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary mb-0']) }}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>

                    <div id="twilio-settings" class="card">
                        <div class="card-header">
                            <h5>{{ __('Twilio Settings') }}</h5>
                            <small class="text-muted">{{ __('Edit your twilio details') }}</small>
                        </div>
                        <div class="card-body">
                            <h4 class="small-title">{{ __('Twilio') }}</h4>
                            {{ Form::model($settings, ['route' => 'twilio.setting', 'method' => 'post']) }}
                            @csrf
                            <div class="row mt-3">
                                <div class="form-group col-md-4">
                                    {{ Form::label('SID', __('SID'), ['class' => 'form-label']) }}
                                    {{ Form::text('twilio_sid', isset($settings['twilio_sid']) ? $settings['twilio_sid'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Twilio Sid'), 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::label('Token', __('Token'), ['class' => 'form-label']) }}
                                    {{ Form::text('twilio_token', isset($settings['twilio_token']) ? $settings['twilio_token'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Twilio Token'), 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ Form::label('From', __('From'), ['class' => 'form-label']) }}

                                    {{ Form::text('twilio_from', isset($settings['twilio_from']) ? $settings['twilio_from'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Twilio From'), 'required' => 'required']) }}
                                </div>
                                <div class="col-md-12 mt-4 mb-2">
                                    <h4 class="small-title">{{ __('Module Settings') }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <span>{{ __('New User') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_user_create', '1', isset($settings['twilio_user_create']) && $settings['twilio_user_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_user_create']) }}
                                                <label class="form-check-label" for="twilio_user_create"></label>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <span>{{ __('New Lead') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_lead_create', '1', isset($settings['twilio_lead_create']) && $settings['twilio_lead_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_lead_create']) }}
                                                <label class="form-check-label" for="twilio_lead_create"></label>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <span>{{ __('New Quotes') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_quotes_create', '1', isset($settings['twilio_quotes_create']) && $settings['twilio_quotes_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_quotes_create']) }}
                                                <label class="form-check-label" for="twilio_quotes_create"></label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <span>{{ __('New Sales Order') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_salesorder_create', '1', isset($settings['twilio_salesorder_create']) && $settings['twilio_salesorder_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_salesorder_create']) }}
                                                <label class="form-check-label" for="twilio_salesorder_create"></label>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <span>{{ __('New Invoice') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_invoice_create', '1', isset($settings['twilio_invoice_create']) && $settings['twilio_invoice_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_invoice_create']) }}
                                                <label class="form-check-label" for="twilio_invoice_create"></label>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <span>{{ __('New Invoice Payment') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_invoicepay_create', '1', isset($settings['twilio_invoicepay_create']) && $settings['twilio_invoicepay_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_invoicepay_create']) }}
                                                <label class="form-check-label" for="twilio_invoicepay_create"></label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <span>{{ __('New Meeting') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_meeting_create', '1', isset($settings['twilio_meeting_create']) && $settings['twilio_meeting_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_meeting_create']) }}
                                                <label class="form-check-label" for="twilio_meeting_create"></label>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <span>{{ __('New Task') }}</span>
                                            <div class="form-check form-switch float-end">
                                                {{ Form::checkbox('twilio_task_create', '1', isset($settings['twilio_task_create']) && $settings['twilio_task_create'] == '1' ? 'checked' : '', ['class' => 'form-check-input input-primary', 'id' => 'twilio_task_create']) }}
                                                <label class="form-check-label" for="twilio_task_create"></label>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="text-end">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn-submit btn btn-primary']) }}
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>

                    <!--Email Notification Setting-->
                    <div id="email-notification-settings" class="card">
                        {{ Form::model($settings, ['route' => ['status.email.language'], 'method' => 'post']) }}
                        @csrf
                        <div class="col-md-12">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <h5>{{ __('Email Notification Settings') }}</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    @foreach ($EmailTemplates as $EmailTemplate)
                                        <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                            <div class="list-group">
                                                <div class="list-group-item form-switch form-switch-right">
                                                    <label class="form-label"
                                                        style="margin-left:5%;">{{ $EmailTemplate->name }}</label>

                                                    <input class="form-check-input" name='{{ $EmailTemplate->id }}'
                                                        id="email_tempalte_{{ $EmailTemplate->template->id }}"
                                                        type="checkbox"
                                                        @if ($EmailTemplate->template->is_active == 1) checked="checked" @endif
                                                        type="checkbox" value="1"
                                                        data-url="{{ route('status.email.language', [$EmailTemplate->template->id]) }}" />
                                                    <label class="form-check-label"
                                                        for="email_tempalte_{{ $EmailTemplate->template->id }}"></label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="card-footer p-0">
                                    <div class="col-sm-12 mt-3 px-2">
                                        <div class="text-end">
                                            <input class="btn btn-print-invoice  btn-primary " type="submit"
                                                value="{{ __('Save Changes') }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    {{-- Google Calendar --}}
                    <div class="" id="google-calender">
                        <div class="card">
                            {{ Form::open(['url' => route('google.calender.settings'), 'enctype' => 'multipart/form-data']) }}
                            <div class="card-header">
                                <div class="row">

                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <h5>{{ __('Google Calendar Settings') }}</h5>
                                    </div>

                                    <div class="col-lg-4 col-md-4 text-end">
                                        <div class="form-check custom-control custom-switch">
                                            <input type="checkbox" class="form-check-input" name="is_enabled"
                                                data-toggle="switchbutton" data-onstyle="primary" id="is_enabled"
                                                {{ isset($settings['is_enabled']) && $settings['is_enabled'] == 'on' ? 'checked' : '' }}>
                                            <label class="custom-control-label form-label" for="is_enabled"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                        {{ Form::label('Google calendar id', __('Google Calendar Id'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('google_clender_id', !empty($settings['google_clender_id']) ? $settings['google_clender_id'] : '', ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id']) }}
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                        {{ Form::label('Google calendar json file', __('Google Calendar json File'), ['class' => 'col-form-label']) }}
                                        <input type="file" class="form-control" name="google_calender_json_file"
                                            id="file">
                                        {{-- {{Form::text('zoom_secret_key', !empty($settings['zoom_secret_key']) ? $settings['zoom_secret_key'] : '' ,array('class'=>'form-control', 'placeholder'=>'Google Calendar json File'))}} --}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn-submit btn btn-primary" type="submit">
                                    {{ __('Save Changes') }}
                                </button>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>

                    {{-- Weebhook Setting --}}
                    <div class="" id="webhook-settings">
                        <div class="card">

                            <div class="card-header">
                                <div class="row">

                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <h5>{{ __('Webhook Settings') }}</h5>
                                    </div>

                                    <div class="col-lg-4 col-md-4 text-end">
                                        <div class="form-check custom-control custom-switch">
                                            <a href="#" data-url="{{ route('webhook.create') }}"
                                                data-size="md" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                title="{{ __('Create') }}"data-title="{{ __('Create New Webhook') }}"
                                                class="btn btn-sm btn-primary btn-icon">
                                                <i class="ti ti-plus"></i>
                                            </a>
                                            <label class="custom-control-label form-label" for="is_enabled"></label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr class="col-md-6">
                                                <th scope="col" class="sort" data-sort="module">
                                                    {{ __('Module') }}</th>
                                                <th scope="col" class="sort" data-sort="url">
                                                    {{ __('URL') }}</th>
                                                <th scope="col" class="sort" data-sort="method">
                                                    {{ __('Method') }}</th>
                                                <th scope="col" class="sort" data-sort="">
                                                    {{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($webhooks as $webhook)
                                                <tr class="Action">
                                                    <td>
                                                        <label for="module"
                                                            class="control-label text-decoration-none tag-lable-{{ $webhook->id }}">{{ $webhook->module }}</label>
                                                    </td>
                                                    <td>
                                                        <label for="url"
                                                            class="control-label text-decoration-none tag-lable-{{ $webhook->id }}">{{ $webhook->url }}</label>
                                                    </td>
                                                    <td>
                                                        <label for="method"
                                                            class="control-label text-decoration-none tag-lable-{{ $webhook->id }}">{{ $webhook->method }}</label>
                                                    </td>
                                                    <td class="">

                                                        <div class="action-btn me-2">
                                                            <a class="mx-3 btn btn-sm bg-info align-items-center"
                                                                data-url="{{ route('webhook.edit', $webhook->id) }}"
                                                                data-size="md" data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Edit') }}"
                                                                data-bs-placement="top" data-ajax-popup="true"
                                                                data-title="{{ __('Edit WebHook') }}"
                                                                class="edit-icon"
                                                                data-original-title="{{ __('Edit') }}"><i
                                                                    class="ti ti-pencil text-white"></i></a>
                                                        </div>
                                                        <div class="action-btn">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['webhook.destroy', $webhook->id]]) !!}
                                                            <a href="#!"
                                                                class="mx-3 btn bg-danger btn-sm align-items-center text-white show_confirm"
                                                                data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                @endif
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->

@endsection
@push('script-page')
    <script></script>
@endpush
