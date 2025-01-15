@extends('layouts.admin')
@section('page-title')
    {{ __('Task Edit') }}
@endsection
@section('title')
    {{ __('Edit Task') }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
@section('action-btn')
    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('task.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @else
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip"
                    title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @endif
        @if (!empty($next))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('task.edit', $next) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @else
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip"
                    title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('task.index') }}">{{ __('Task') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action border-0">{{ __('Overview') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-2"
                                class="list-group-item list-group-item-action border-0">{{ __('Stream') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($task, ['route' => ['task.update', $task->id], 'method' => 'PUT','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <div class="float-end">
                                    <a href="#" data-size="md" class="btn btn-sm btn-primary"
                                        data-ajax-popup-over="true" data-size="md"
                                        data-title="{{ __('Generate content with AI') }}"
                                        data-url="{{ route('generate', ['task']) }}" data-toggle="tooltip"
                                        title="{{ __('Generate') }}">
                                        <i class="fas fa-robot"></span><span
                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                    </a>
                                </div>
                            @endif
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your task information') }}</small>
                        </div>

                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
                                            @error('name')
                                                <span class="invalid-name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('stage', __('Task Stage'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('stage', $stage, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('stage')
                                                <span class="invalid-stage" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::date('start_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required' => 'required']) !!}
                                            @error('start_date')
                                                <span class="invalid-start_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::date('due_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required' => 'required']) !!}
                                            @error('due_date')
                                                <span class="invalid-due_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('priority', $priority, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('priority')
                                                <span class="invalid-priority" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                            {!! Form::select('account', $account_name, null, ['class' => 'form-control']) !!}
                                        </div>
                                        @error('account')
                                            <span class="invalid-account" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('user', __('Assigned User'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('user', $user, $task->user_id, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('user')
                                                <span class="invalid-user" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Description'), 'required' => 'required']) }}
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>



                                    <div class="text-end">
                                        {{ Form::submit(__('Update'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>


                                </div>
                            </form>
                        </div>
                        {{ Form::close() }}
                    </div>
                    <div id="useradd-2" class="card">
                        {{ Form::open(['route' => ['streamstore', ['task', $task->name, $task->id]], 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            <h5>{{ __('Stream') }}</h5>
                            <small class="text-muted">{{ __('Add stream comment') }}</small>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('stream', __('Stream'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('stream_comment', null, ['class' => 'form-control', 'placeholder' => __('Enter Stream Comment'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <input type="hidden" name="log_type" value="task comment">
                                    <div class="col-12 mb-3 field" data-name="attachments">
                                        <div class="attachment-upload">
                                            <div class="attachment-button">
                                                <div class="pull-left">
                                                    <div class="form-group">
                                                        {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
                                                        {{-- {{Form::file('attachment',array('class'=>'form-control'))}} --}}
                                                        <input type="file"name="attachment" class="form-control mb-3"
                                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                        <img id="blah" width="20%" height="20%" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="attachments"></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="col-12">
                            <div class="card-header">
                                <h5>{{ __('Latest comments') }}</h5>
                            </div>
                            @foreach ($streams as $stream)
                                @php
                                    $remark = json_decode($stream->remark);
                                @endphp
                                @if ($remark->data_id == $task->id)
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-xl-12">
                                                <ul class="list-group team-msg">
                                                    <li class="list-group-item border-0 d-flex align-items-start mb-2">
                                                        <div class="avatar me-3">
                                                            @php
                                                                $profile = \App\Models\Utility::get_file('upload/profile/');
                                                            @endphp
                                                            <a href="{{ !empty($stream->file_upload) ? $profile . $stream->file_upload : asset(url('./assets/images/user/avatar-5.jpg')) }}"
                                                                target="_blank">
                                                                <img alt="" class="rounded border-2 border border-primary"
                                                                    @if (!empty($stream->file_upload)) src="{{ !empty($stream->file_upload) ? $profile . $stream->file_upload : asset(url('./assets/images/user/avatar-5.jpg')) }}" @else  avatar="{{ $remark->user_name }}" @endif>
                                                            </a>
                                                        </div>
                                                        <div class="d-block d-sm-flex align-items-center right-side">
                                                            <div
                                                                class="d-flex align-items-start flex-column justify-content-center mb-3 mb-sm-0">
                                                                <div class="h6 mb-1">{{ $remark->user_name }}
                                                                </div>
                                                                <span class="text-sm lh-140 mb-0">
                                                                    posted to <a href="#">{{ $remark->title }}</a> ,
                                                                    {{ $stream->log_type }} <a
                                                                        href="#">{{ $remark->stream_comment }}</a>
                                                                </span>
                                                            </div>
                                                            <div class=" ms-2  d-flex align-items-center ">
                                                                <small
                                                                    class="float-end ">{{ $stream->created_at }}</small>
                                                            </div>
                                                        </div>

                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>



                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection
@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
