@extends('layouts.admin')
@section('page-title')
{{ __('Landing Page') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Landing Page')}}</li>
@endsection
@section('title')
{{ __('Landing Page') }}
@endsection
@php
$logo=\App\Models\Utility::get_file('uploads/logo');
$settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
@endphp

@push('css-page')
<link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">
@endpush

@push('script-page')

{{-- <script src="{{ asset('Modules/LandingPage/Resources/assets/js/plugins/tinymce.min.js')}}" referrerpolicy="origin"></script> --}}
<script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
<script>
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

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Landing Page')}}</li>
@endsection


@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        @include('landingpage::layouts.tab')
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                {{-- Start for all settings tab --}}
                {{Form::model(null, array('route' => array('landingpage.store'), 'method' => 'POST', 'class'=>'needs-validation', 'novalidate')) }}
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-9 col-md-9 col-sm-9">
                                <h5>{{ __('Top Bar') }}</h5>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 justify-content-end d-flex">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" data-toggle="switchbutton" data-onstyle="primary" class="" name="topbar_status"   id="topbar_status" {{ $settings['topbar_status'] == 'on' ? 'checked="checked"' : '' }}>
                                    <label class="custom-control-label" for="topbar_status"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="form-group col-12">
                                {{ Form::label('content', __('Message'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                {{ Form::textarea('topbar_notification_msg',$settings['topbar_notification_msg'], ['class' => 'summernote form-control', 'required' => 'required', 'id'=>'mytextarea','placeholder'=>__('Write Here..')]) }}
                            </div>

                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <input class="btn btn-print-invoice btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
                    </div>
                </div>
                {{ Form::close() }}

                {{-- End for all settings tab --}}
            </div>
        </div>
    </div>
</div>
@endsection
