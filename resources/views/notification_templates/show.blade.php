@extends('layouts.admin')
@section('page-title')
    {{ $notification_template->name }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="dashboard">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('notification-templates.index') }}">{{ __('Notification Template') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Notification Template') }}</li>
@endsection
@section('title')
    {{ __('Notification Template') }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
    $lang = isset($users->lang) ? $users->lang : 'en';
    $LangName = $curr_noti_tempLang->language ?? (App\Models\Languages::where('code', $lang)->first() ?? new Utility(['fullName' => 'English']));

@endphp
@push('pre-purpose-css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        if ($(".pc-tinymce-2").length) {
            tinymce.init({
                selector: '.pc-tinymce-2',
                height: "400",
                content_style: 'body { font-family: "Inter", sans-serif; }'
            });
        }
    </script>
@endpush

@section('action-btn')
    {{-- <div class="row">

        <div class="text-end mb-3">
            <div class="text-end">
                <div class="d-flex justify-content-end drp-languages">
                    <ul class="list-unstyled mb-0 m-2">
                        <li class="dropdown dash-h-item drp-language">
                            <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false"
                                id="dropdownLanguage">
                                <span
                                    class="drp-text hide-mob text-primary">{{ ucFirst(isset($LangName->fullName) ? $LangName->fullName : 'en') }}</span>
                                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                            </a>
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" aria-labelledby="dropdownLanguage">
                                @foreach ($languages as $code => $lang)
                                    <a href="{{ route('notification_templates.index', [$notification_template->id, $code]) }}"
                                        @if(!empty($EmailTemplate->template)?$EmailTemplate->template->is_active:'0' == 1) checked="checked" @endif type="checkbox" value="{{!empty($EmailTemplate->template)?$EmailTemplate->template->is_active:''}} "
                                        class="dropdown-item {{ $curr_noti_tempLang->lang == $lang ? 'text-primary' : '' }}">{{ ucfirst($lang) }}</a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                    <ul class="list-unstyled mb-0 m-2">
                        <li class="dropdown dash-h-item drp-language">
                            <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false"
                                id="dropdownLanguage">
                                <span
                                    class="drp-text hide-mob text-primary">{{ __('Template: ') }}{{ $notification_template->name }}</span>
                                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                            </a>
                            @if (isset($settings['enable_chatgpt']) && $settings['enable_chatgpt'] == 'on')
                                <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true"
                                    data-size="md" data-title="{{ __('Generate content with AI') }}"
                                    data-url="{{ route('generate', ['notification template']) }}" data-toggle="tooltip"
                                    title="{{ __('Generate') }}">
                                    <i class="fas fa-robot"></span><span
                                            class="robot">{{ __('Generate With AI') }}</span></i>
                                </a>
                            @endif
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" aria-labelledby="dropdownLanguage">
                                @foreach ($notification_templates as $notification_template)
                                    <a href="{{ route('notification_templates.index', [$notification_template->id, Request::segment(3) ? Request::segment(3) : \Auth::user()->lang]) }}"
                                        class="dropdown-item {{ $notification_template->name == $notification_template->name ? 'text-primary' : '' }}">{{ $notification_template->name }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="text-end mb-3">
        <div class="d-flex justify-content-end drp-languages">
            <ul class="list-unstyled mb-0 m-2">

            </ul>
            <ul class="list-unstyled mb-0 m-2">
                <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true"
                                    data-size="md" data-title="{{ __('Generate content with AI') }}"
                                    data-url="{{ route('generate', ['notification template']) }}" data-toggle="tooltip"
                                    title="{{ __('Generate') }}">
                                    <i class="fas fa-robot"></span><span
                                            class="robot">{{ __('Generate With AI') }}</span></i>
                                </a>
                            @endif
                </li>
            </ul>
        </div>
    </div>
@endsection
{{-- @section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body ">
                    <h5 class="font-weight-bold pb-3">{{ __('Placeholders') }}</h5>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header card-body">
                                <div class="row text-xs">
                                    <h6 class="font-weight-bold mb-4">{{ __('Variables') }}</h6>
                                    @php
                                        $variables = json_decode($curr_noti_tempLang->variables);
                                    @endphp
                                    @if (!empty($variables) > 0)
                                        @foreach ($variables as $key => $var)
                                            <div class="col-6 pb-1">
                                                <p class="mb-1">{{ __($key) }} : <span
                                                        class="pull-right text-primary">{{ '{' . $var . '}' }}</span></p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    {{ Form::model($curr_noti_tempLang, ['route' => ['notification-templates.update', $curr_noti_tempLang->parent_id], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="form-group col-12">
                            {{ Form::label('content', __('Notification Message'), ['class' => 'form-label text-dark']) }}
                            {{ Form::textarea('content', $curr_noti_tempLang->content, ['class' => 'form-control', 'required' => 'required', 'rows' => '04', 'placeholder' => 'EX. Hello, {company_name}']) }}
                            <small>{{ __('A variable is to be used in such a way.') }} <span
                                    class="text-primary">{{ __('Ex. Hello, {company_name}') }}</span></small>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12 text-end">
                        {{ Form::hidden('lang', null) }}
                        <input type="submit" value="{{ __('Save Changes') }}"
                            class="btn btn-print-invoice  btn-primary m-r-10">
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection --}}
@section('content')

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header card-body">
                    <h5></h5>
                    <div class="row text-xs">
                        <h6 class="font-weight-bold mb-4">{{ __('Variables') }}</h6>
                        @php
                            $variables = json_decode($curr_noti_tempLang->variables);
                        @endphp
                        @if (!empty($variables) > 0)
                            @foreach ($variables as $key => $var)
                                <div class="col-6 pb-1">
                                    <p class="mb-1">{{ __($key) }} : <span
                                            class="pull-right text-primary">{{ '{' . $var . '}' }}</span></p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5></h5>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3 ">
                    <div class="card sticky-top language-sidebar mb-0">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $key => $lang)
                                <a class="list-group-item list-group-item-action border-0 {{ $curr_noti_tempLang->lang == $key ? 'active' : '' }}"
                                    href="{{ route('manage.notification.language', [$notification_template->id, $key]) }}">
                                    {{ Str::ucfirst($lang) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="card h-100 p-3">
                        {{ Form::model($curr_noti_tempLang, ['route' => ['notification-templates.update', $curr_noti_tempLang->parent_id], 'method' => 'PUT']) }}
                        <div class="row">
                            <div class="form-group col-12">
                                {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                                {{ Form::text('name', $notification_template->name, ['class' => 'form-control font-style', 'disabled' => 'disabled']) }}
                            </div>
                            <div class="form-group col-12">
                            {{ Form::label('content', __('Notification Message'), ['class' => 'form-label text-dark']) }}
                            {{ Form::textarea('content', $curr_noti_tempLang->content, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'EX. Hello, {company_name}']) }}
                            <small>{{ __('A variable is to be used in such a way.') }} <span
                                    class="text-primary">{{ __('Ex. Hello, {company_name}') }}</span></small>
                            </div>
                            <div class="col-md-12 text-end mb-3">
                                {{ Form::hidden('lang', null) }}
                                <input type="submit" value="{{ __('Save') }}"
                                    class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection