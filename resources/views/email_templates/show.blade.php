@extends('layouts.admin')
@section('page-title')
    {{ __('Email Templates') }}
@endsection
@section('title')
    {{ $emailTemplate->name }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email_template.index') }}">{{ __('Email Templates') }}</a></li>
    <li class="breadcrumb-item">{{ __('Email Templates') }}</li>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        if ($(".pc-tinymce-2").length) {
            tinymce.init({
                selector: '.pc-tinymce-2',
                height: "400",
                content_style: 'body { font-family: "Inter", sans-serif; }',
                menu: {
                    file: {
                        title: 'File',
                        items: ''
                    },
                    edit: {
                        title: 'Edit',
                        items: 'undo redo | cut copy paste pastetext | selectall'
                    },
                    insert: {
                        title: 'Insert',
                        items: 'link media | template hr'
                    },
                    view: {
                        title: 'View',
                        items: 'visualaid'
                    },
                    format: {
                        title: 'Format',
                        items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'
                    },
                    table: {
                        title: 'Table',
                        items: 'inserttable tableprops deletetable | cell row column'
                    },
                    tools: {
                        title: 'Tools',
                        items: 'spellchecker code'
                    }
                }
            });
        }
        $(document).ready(function() {
            $('.summernote').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                    ['list', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'unlink']],
                ],
                height: 250,
            });
        });
    </script>
@endpush
@php
    $lang = isset($users->lang) ? $users->lang : 'en';
    if ($lang == null) {
        $lang = 'en';
    }
    $LangName = $currEmailTempLang->language;

@endphp
@section('action-btn')
    <div class="text-end mb-3">
        <div class="d-flex justify-content-end drp-languages">
            <ul class="list-unstyled mb-0 m-2">

            </ul>
            <ul class="list-unstyled mb-0 m-2">
                <li class="dropdown dash-h-item drp-language" style="list-style-type: none;">
                    <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true"
                        data-size="md" data-title="{{ __('Generate content with AI') }}"
                        data-url="{{ route('generate', ['email template']) }}" data-toggle="tooltip"
                        title="{{ __('Generate') }}">
                        <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection
@section('content')
    <div class="row invoice-row">
        <div class="col-md-4 col-12">
            <div class="card mb-0 h-100">
                <div class="card-header card-body">
                    <h5></h5>
                    {{ Form::model($emailTemplate, ['route' => ['emailupdate.form', $emailTemplate->id], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('name', null, ['class' => 'form-control font-style', 'disabled' => 'disabled']) }}
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('from', __('From'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('from', null, ['class' => 'form-control font-style', 'required' => 'required','placeholder'=>__('Enter From Name')]) }}
                        </div>
                        {{ Form::hidden('lang', $currEmailTempLang->lang, ['class' => '']) }}
                        <div class="col-12 text-end">
                            <input type="submit" value="{{ __('Save') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-8 col-12">
            <div class="card mb-0 h-100">
                <div class="card-header card-body">
                    <h5></h5>
                    <div class="row text-xs">

                        <h6 class="font-weight-bold mb-4">{{ __('Variables') }}</h6>

                        <div class="col-6 pb-1">
                            @if ($emailTemplate->slug == 'new_user')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Email') }} : <span
                                            class="pull-right text-primary">{email}</span></p>
                                    <p class="mb-1">{{ __('Password') }} : <span
                                            class="pull-right text-primary">{password}</span></p>
                                </div>
                            @elseif($emailTemplate->slug == 'lead_assigned')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Lead Name') }} : <span
                                            class="pull-right text-primary">{lead_name}</span></p>
                                    <p class="mb-1">{{ __('Lead Email') }} : <span
                                            class="pull-right text-primary">{lead_email}</span></p>
                                    <p class="mb-1">{{ __('Lead Assign User') }} : <span
                                            class="pull-right text-primary">{lead_assign_user}</span></p>
                                    <p class="mb-1">{{ __('Lead Description') }} : <span
                                            class="pull-right text-primary">{lead_description}</span></p>
                                    <p class="mb-1">{{ __('Lead Source') }} : <span
                                            class="pull-right text-primary">{lead_source}</span></p>
                                </div>
                            @elseif($emailTemplate->slug == 'task_assigned')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Task Name') }} : <span
                                            class="pull-right text-primary">{task_name}</span></p>
                                    <p class="mb-1">{{ __('Task Start Date') }} : <span
                                            class="pull-right text-primary">{task_start_date}</span></p>
                                    <p class="mb-1">{{ __('Task Due Date') }} : <span
                                            class="pull-right text-primary">{task_due_date}</span></p>
                                    <p class="mb-1">{{ __('Task Stage') }} : <span
                                            class="pull-right text-primary">{task_stage}</span></p>
                                    <p class="mb-1">{{ __('Task Assign User') }} : <span
                                            class="pull-right text-primary">{task_assign_user}</span></p>
                                    <p class="mb-1">{{ __('Task Description') }} : <span
                                            class="pull-right text-primary">{task_description}</span></p>
                                </div>
                            @elseif($emailTemplate->slug == 'quote_created')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Quote Number') }} : <span
                                            class="pull-right text-primary">{quote_number}</span></p>
                                    <p class="mb-1">{{ __('Billing Address') }} : <span
                                            class="pull-right text-primary">{billing_address}</span></p>
                                    <p class="mb-1">{{ __('Shipping Address') }} : <span
                                            class="pull-right text-primary">{shipping_address}</span></p>
                                    <p class="mb-1">{{ __('Quotation Description') }} : <span
                                            class="pull-right text-primary">{description}</span></p>
                                    <p class="mb-1">{{ __('Quote Assign User') }} : <span
                                            class="pull-right text-primary">{quote_assign_user}</span></p>
                                    <p class="mb-1">{{ __('Quoted Date') }} : <span
                                            class="pull-right text-primary">{date_quoted}</span></p>
                                </div>
                            @elseif($emailTemplate->slug == 'new_sales_order')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Quote Number') }} : <span
                                            class="pull-right text-primary">{quote_number}</span></p>
                                    <p class="mb-1">{{ __('Billing Address') }} : <span
                                            class="pull-right text-primary">{billing_address}</span></p>
                                    <p class="mb-1">{{ __('Shipping Address') }} : <span
                                            class="pull-right text-primary">{shipping_address}</span></p>
                                    <p class="mb-1">{{ __('Quotation Description') }} : <span
                                            class="pull-right text-primary">{description}</span></p>
                                    <p class="mb-1">{{ __('Quoted Date') }} : <span
                                            class="pull-right text-primary">{date_quoted}</span></p>
                                    <p class="mb-1">{{ __('Salesorder Assign User') }} : <span
                                            class="pull-right text-primary">{salesorder_assign_user}</span>
                                    </p>

                                </div>
                            @elseif($emailTemplate->slug == 'new_invoice' || $emailTemplate->slug == 'invoice_payment_recored')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Invoice Number') }} : <span
                                            class="pull-right text-primary">{invoice_id}</span></p>
                                    <p class="mb-1">{{ __('Invoice Client') }} : <span
                                            class="pull-right text-primary">{invoice_client}</span></p>
                                    <p class="mb-1">{{ __('Invoice Issue Date') }} : <span
                                            class="pull-right text-primary">{created_at}</span></p>
                                    <p class="mb-1">{{ __('Invoice Status') }} : <span
                                            class="pull-right text-primary">{invoice_status}</span></p>
                                    <p class="mb-1">{{ __('Invoice Total') }} : <span
                                            class="pull-right text-primary">{invoice_total}</span></p>
                                    <p class="mb-1">{{ __('Invoice Sub Total') }} : <span
                                            class="pull-right text-primary">{invoice_sub_total}</span></p>

                                </div>
                            @elseif($emailTemplate->slug == 'meeting_assigned')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Attendees User') }} : <span
                                            class="pull-right text-primary">{attendees_user}</span></p>
                                    <p class="mb-1">{{ __('Attendees Contact') }} : <span
                                            class="pull-right text-primary">{attendees_contact}</span></p>
                                    <p class="mb-1">{{ __('Meeting Title') }} : <span
                                            class="pull-right text-primary">{meeting_name}</span></p>
                                    <p class="mb-1">{{ __('Meeting Start Date') }} : <span
                                            class="pull-right text-primary">{meeting_start_date}</span></p>
                                    <p class="mb-1">{{ __('Meeting Due Date') }} : <span
                                            class="pull-right text-primary">{meeting_due_date}</span></p>
                                    <p class="mb-1">{{ __('Meeting Assign User') }} : <span
                                            class="pull-right text-primary">{meeting_assign_user}</span>
                                    </p>
                                    <p class="mb-1">{{ __('Meeting Description') }} : <span
                                            class="pull-right text-primary">{meeting_description}</span>
                                    </p>
                                </div>
                            @elseif($emailTemplate->slug == 'campaign_assigned')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Campaign Title') }} : <span
                                            class="pull-right text-primary">{campaign_title}</span></p>
                                    <p class="mb-1">{{ __('Campaign Status') }} : <span
                                            class="pull-right text-primary">{campaign_status}</span></p>
                                    <p class="mb-1">{{ __('Campaign Start Date') }} : <span
                                            class="pull-right text-primary">{campaign_start_date}</span>
                                    </p>
                                    <p class="mb-1">{{ __('Campaign Due Date') }} : <span
                                            class="pull-right text-primary">{campaign_due_date}</span></p>
                                    <p class="mb-1">{{ __('Campaign Assign User') }} : <span
                                            class="pull-right text-primary">{campaign_assign_user}</span>
                                    </p>
                                    <p class="mb-1">{{ __('Campaign Description') }} : <span
                                            class="pull-right text-primary">{campaign_description}</span>
                                    </p>
                                </div>
                            @elseif($emailTemplate->slug == 'new_contract')
                                <div class="row">
                                    <p class="mb-1">{{ __('App Name') }} : <span
                                            class="pull-end text-primary">{app_name}</span></p>
                                    <p class="mb-1">{{ __('Company Name') }} : <span
                                            class="pull-right text-primary">{company_name}</span></p>
                                    <p class="mb-1">{{ __('App Url') }} : <span
                                            class="pull-right text-primary">{app_url}</span></p>
                                    <p class="mb-1">{{ __('Contract Client') }} : <span
                                            class="pull-right text-primary">{contract_client}</span></p>
                                    <p class="mb-1">{{ __('Contract Subject') }} : <span
                                            class="pull-right text-primary">{contract_subject}</span></p>
                                    <p class="mb-1">{{ __('Contract Start_Date') }} : <span
                                            class="pull-right text-primary">{contract_start_date}</span>
                                    </p>
                                    <p class="mb-1">{{ __('Contract End_Date') }} : <span
                                            class="pull-right text-primary">{contract_end_date}</span></p>


                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <h5></h5>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="card sticky-top language-sidebar mb-0">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $key => $lang)
                                <a class="list-group-item list-group-item-action border-0 {{ $currEmailTempLang->lang == $key ? 'active' : '' }}"
                                    href="{{ route('manage.email.language', [$emailTemplate->id, $key]) }}">
                                    {{ Str::ucfirst($lang) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-9 ">
                    <div class="card h-100 p-3">
                        {{ Form::model($currEmailTempLang, ['route' => ['email_template.update', $currEmailTempLang->parent_id], 'method' => 'PUT']) }}
                        <div class="form-group col-12">
                            {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('subject', null, ['class' => 'form-control font-style', 'required' => 'required','placeholder'=>__('Enter Subject')]) }}
                        </div>
                        <div class="form-group col-12">
                            {{ Form::label('content', __('Email Message'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::textarea('content', $currEmailTempLang->content, ['class' => 'summernote', 'id' => 'content', 'required' => 'required','placeholder'=>__('Write Here ..')]) }}
                        </div>

                        <div class="col-md-12 text-end mb-3">
                            {{ Form::hidden('lang', null) }}
                            <input type="submit" value="{{ __('Save') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
