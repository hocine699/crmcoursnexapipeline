@extends('layouts.admin')
@section('page-title')
    {{ __('Opportunities Edit') }}
@endsection
@section('title')
    {{ __('Edit Opportunities') }} {{ '(' . $opportunities->name . ')' }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
@section('action-btn')
    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('opportunities.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @else
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip"
                    title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @endif
        @if (!empty($next))
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="{{ route('opportunities.edit', $next) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @else
            <div class="action-btn ms-2" style="margin-right :4px;">
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
    <li class="breadcrumb-item"><a href="{{ route('opportunities.index') }}">{{ __('Opportunities') }}</a></li>
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
                            <a href="#useradd-3"
                                class="list-group-item list-group-item-action border-0">{{ __('Documents') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-4"
                                class="list-group-item list-group-item-action border-0">{{ __('Tasks') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-5"
                                class="list-group-item list-group-item-action border-0">{{ __('Quotes') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-6"
                                class="list-group-item list-group-item-action border-0">{{ __('Sales Orders') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-7"
                                class="list-group-item list-group-item-action border-0">{{ __('Invoices') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($opportunities, ['route' => ['opportunities.update', $opportunities->id], 'method' => 'PUT','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <div class="float-end">
                                    <a href="#" data-size="md" class="btn btn-sm btn-primary "
                                        data-ajax-popup-over="true" data-size="md"
                                        data-title="{{ __('Generate content with AI') }}"
                                        data-url="{{ route('generate', ['opportunities']) }}" data-toggle="tooltip"
                                        title="{{ __('Generate') }}">
                                        <i class="fas fa-robot"></span><span
                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                    </a>
                                </div>
                            @endif
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your opportunities information') }}</small>
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
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                            {!! Form::select('account', $account_name, null, ['class' => 'form-control ','placeholder'=>__('Select Account')]) !!}
                                        </div>
                                        @error('account')
                                            <span class="invalid-account" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('contact', __('Contact'), ['class' => 'form-label']) }}
                                            {!! Form::select('contact', $contact, null, ['class' => 'form-control ','placeholder'=>__('Select Contact')]) !!}
                                            @error('contacts')
                                                <span class="invalid-contacts" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('campaign', __('Campaign'), ['class' => 'form-label']) }}
                                            {!! Form::select('campaign', $campaign_id, null, ['class' => 'form-control ','placeholder'=>__('Select Campaign')]) !!}
                                        </div>
                                        @error('campaign_id')
                                            <span class="invalid-campaign_id" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('stage', __('Stage'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('stage', $stages, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                        </div>
                                        @error('stage')
                                            <span class="invalid-stage" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('Enter Amount'), 'required' => 'required']) }}
                                            @error('amount')
                                                <span class="invalid-amount" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('probability', __('Probability'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::number('probability', null, ['class' => 'form-control', 'placeholder' => __('Enter Probability'), 'required' => 'required']) }}
                                            @error('probability')
                                                <span class="invalid-probability" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('close_date', __('Close Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::date('close_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'placeholder' => __('Enter Phone'), 'required' => 'required']) }}
                                            @error('close_date')
                                                <span class="invalid-close_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('lead_source', __('Lead Source'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('lead_source', $lead_source, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                            @error('lead_source')
                                                <span class="invalid-lead_source" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('user', __(' Assigned User'), ['class' => 'form-label']) }}
                                            {!! Form::select('user', $user, $opportunities->user_id, ['class' => 'form-control ','placeholder'=>__('Select User')]) !!}
                                        </div>
                                        @error('user')
                                            <span class="invalid-user" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::textarea('description', null, ['class' => 'form-control ', 'rows' => 3,'required'=>'required','placeholder'=>__('Enter Description')]) !!}
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
                        {{ Form::open(['route' => ['streamstore', ['opportunities', $opportunities->name, $opportunities->id]], 'method' => 'post', 'enctype' => 'multipart/form-data' ,'class' => 'needs-validation', 'novalidate']) }}
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
                                    <input type="hidden" name="log_type" value="opportunities comment">

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
                                @if ($remark->data_id == $opportunities->id)
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
                                                            <div class=" me-2  d-flex align-items-center ">
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

                    <div id="useradd-3" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Documents') }}</h5>
                                    <small class="text-muted">{{ __('Assigned document for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('document.create', ['opportunities', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create New Documents') }}"title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only   ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="budget">{{ __('File') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}</th>
                                            <th scope="col" class="text-end">{{ __('Action') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $document)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('document.edit', $document->id) }}" data-size="md"
                                                        data-title="{{ __('Document Details') }}"
                                                        class="action-item text-primary">
                                                        {{ $document->name }}</a>
                                                </td>
                                                <td class="budget">
                                                    @if (!empty($document->attachment))
                                                        <a href="{{ asset(Storage::url('upload/profile')) . '/' . $document->attachment }}"
                                                            download=""><i class="ti ti-download"></i></a>
                                                    @else
                                                        <span>
                                                            {{ __('No File') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($document->status == 0)
                                                        <span
                                                            class="badge status_badge bg-success p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                                    @elseif($document->status == 1)
                                                        <span
                                                            class="badge status_badge bg-warning p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                                    @elseif($document->status == 2)
                                                        <span
                                                            class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                                    @elseif($document->status == 3)
                                                        <span
                                                            class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($document->created_at) }}</span>
                                                </td>
                                                @if (Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                                                    <td class="text-end">

                                                        @can('Show Document')
                                                            <div class="action-btn  me-2">
                                                                <a href="#" data-size="md"
                                                                    data-url="{{ route('document.show', $document->id) }}"
                                                                    data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                    title="{{ __('Details') }}"
                                                                    data-title="{{ __('Document Details') }}"
                                                                    class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit Document')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('document.edit', $document->id) }}"
                                                                    class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Document Edit') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Document')
                                                            <div class="action-btn ">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
                                                                    data-bs-toggle="tooltip" title='Delete'>
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div id="useradd-4" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Tasks') }}</h5>
                                    <small class="text-muted">{{ __('Assigned Tasks for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg" data-url="{{ route('task.create',['task',0]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            title="{{ __('Create') }}"data-title="{{ __('Create New Task') }}"
                                            class="btn btn-sm btn-primary btn-icon-only ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable1">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="budget">{{ __('Parent') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Stage') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Date Start') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assigned User') }}</th>
                                            @if (Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                                <th scope="col">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tasks as $task)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('task.edit', $task->id) }}" data-size="md"
                                                        data-title="{{ __('Task Details') }}"
                                                        class="action-item text-primary">
                                                        {{ $task->name }}
                                                    </a>
                                                </td>
                                                <td class="budget">
                                                    {{ $task->parent }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ !empty($task->stages) ? $task->stages->name : '' }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($task->start_date) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ !empty($task->assign_user) ? $task->assign_user->name : '-' }}</span>
                                                </td>
                                                @if (Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                                    <td>
                                                        <div class="d-flex">
                                                            @can('Show Task')
                                                                <div class="action-btn  me-2">
                                                                    <a href="#" data-size="md"
                                                                        data-url="{{ route('task.show', $task->id) }}"
                                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                        title="{{ __('Details') }}"
                                                                        data-title="{{ __('Task Details') }}"
                                                                        class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                                        <i class="ti ti-eye"></i>
                                                                    </a>
                                                                </div>
                                                            @endcan
                                                            @can('Edit Task')
                                                                <div class="action-btn  me-2">
                                                                    <a href="{{ route('task.edit', $task->id) }}"
                                                                        class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('Edit Task') }}"><i
                                                                            class="ti ti-pencil"></i></a>
                                                                </div>
                                                            @endcan
                                                            @can('Delete Task')
                                                                <div class="action-btn ">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id]]) !!}
                                                                    <a href="#!"
                                                                        class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
                                                                        data-bs-toggle="tooltip" title='Delete'>
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div id="useradd-5" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Quotes') }}</h5>
                                    <small class="text-muted">{{ __('Assigned Quotes for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('quote.create', ['opportunity', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create New Quote') }}" title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable4">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assign User') }}
                                            </th>
                                            @if (Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quotes as $quote)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('quote.edit', $quote->id) }}" data-size="md"
                                                        data-title="{{ __('Quote Details') }}"
                                                        class="action-item text-primary">
                                                        {{ $quote->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($quote->status == 0)
                                                        <span class="badge status_badge bg-secondary p-2 px-3"
                                                            style="width: 79px;">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                                    @elseif($quote->status == 1)
                                                        <span class="badge status_badge bg-info p-2 px-3"
                                                            style="width: 79px;">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($quote->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->priceFormat($quote->getTotal()) }}</span>
                                                </td>
                                                <td>
                                                    <span class="col-sm-12"><span
                                                            class="text-sm">{{ ucfirst(!empty($quote->assign_user) ? $quote->assign_user->name : '-') }}</span></span>
                                                </td>
                                                @if (Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                                                    <td class="text-end">
                                                        @can('Show Quote')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('quote.show', $quote->id) }}"
                                                                    data-size="md"class="mx-3 bg-warning btn btn-sm align-items-center text-white "
                                                                    data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                    data-title="{{ __('Quote Details') }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit Quote')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('quote.edit', $quote->id) }}"
                                                                    class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Quote') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Quote')
                                                            <div class="action-btn ">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm  bg-danger align-items-center text-white show_confirm"
                                                                    data-bs-toggle="tooltip" title='Delete'>
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </td>
                                                @endif

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div id="useradd-6" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Sales Orders') }}</h5>
                                    <small
                                        class="text-muted">{{ __('Assigned SalesOrder for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('salesorder.create', ['opportunity', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create New SalesOrder') }}" title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable3">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }} </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assigned User') }}</th>
                                            @if (Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesorders as $salesorder)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                        data-size="md" data-title="{{ __('SalesOrder') }}"
                                                        class="action-item text-primary">
                                                        {{ $salesorder->name }}</a>
                                                </td>
                                                <td>
                                                    @if ($salesorder->status == 0)
                                                        <span class="badge status_badge bg-secondary p-2 px-3"
                                                            style="width: 79px;">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 1)
                                                        <span class="badge status_badge bg-info p-2 px-3"
                                                            style="width: 79px;">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($salesorder->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->priceFormat($salesorder->getTotal()) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ ucfirst(!empty($salesorder->assign_user) ? $salesorder->assign_user->name : '-') }}</span>
                                                </td>
                                                @if (Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                                                    <td class="text-end">
                                                        @can('Show SalesOrder')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('salesorder.show', $salesorder->id) }}"
                                                                    data-size="md"
                                                                    class="mx-3 btn btn-sm bg-warning align-items-center text-white"
                                                                    data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                    data-title="{{ __('SalesOrders Details') }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit SalesOrder')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                                    class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit SalesOrders') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete SalesOrder')
                                                            <div class="action-btn ">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm bg-danger  align-items-center text-white show_confirm"
                                                                    data-bs-toggle="tooltip" title='Delete'>
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div id="useradd-7" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Invoices') }}</h5>
                                    <small class="text-muted">{{ __('Assigned SalesInvoice for this opportunities') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('invoice.create', ['opportunity', $opportunities->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create New SalesInvoice') }}"
                                            title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon-only ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable3">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assigned User') }}
                                            </th>
                                            @if (Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesinvoices as $salesinvoice)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('invoice.edit', $salesinvoice->id) }}"
                                                        data-size="md" data-title="{{ __('SalesInvoice') }}"
                                                        class="action-item text-primary">
                                                        {{ $salesinvoice->name }}</a>
                                                </td>
                                                <td>
                                                    @if ($salesinvoice->status == 0)
                                                        <span class="badge status_badge bg-secondary p-2 px-3"
                                                            style="width: 91px;">{{ __(\App\Models\Invoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 1)
                                                        <span class="badge status_badge bg-danger p-2 px-3"
                                                            style="width: 91px;">{{ __(\App\Models\Invoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 2)
                                                        <span class="badge status_badge bg-warning p-2 px-3"
                                                            style="width: 91px;">{{ __(\App\Models\Invoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 3)
                                                        <span class="badge status_badge bg-success p-2 px-3"
                                                            style="width: 91px;">{{ __(\App\Models\Invoice::$status[$salesinvoice->status]) }}</span>
                                                    @elseif($salesinvoice->status == 4)
                                                        <span class="badge status_badge bg-info p-2 px-3"
                                                            style="width: 91px;">{{ __(\App\Models\Invoice::$status[$salesinvoice->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($salesinvoice->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->priceFormat($salesinvoice->getTotal()) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ ucfirst(!empty($salesinvoice->assign_user) ? $salesinvoice->assign_user->name : '-') }}</span>
                                                </td>
                                                @if (Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
                                                    <td class="text-end">
                                                        @can('Show Invoice')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('invoice.show', $salesinvoice->id) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                    class="mx-3 btn btn-sm bg-warning align-items-center text-white "
                                                                    data-title="{{ __('Invoice Details') }}">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit Invoice')
                                                            <div class="action-btn  me-2">
                                                                <a href="{{ route('invoice.edit', $salesinvoice->id) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    class="mx-3 btn btn-sm bg-info align-items-center text-white "
                                                                    data-title="{{ __('Edit Invoice') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Invoice')
                                                            <div class="action-btn ">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $salesinvoice->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm  bg-danger align-items-center text-white show_confirm"
                                                                    data-bs-toggle="tooltip" title='Delete'>
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan

                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    <script>
        $(document).on('change', 'select[name=parent]', function() {
            console.log('click');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log('getparent', bid);
            $.ajax({
                url: '{{ route('task.getparent') }}',
                type: 'POST',
                data: {
                    "parent": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log('get data');
                    console.log(data);
                    $('#parent_id').empty();
                    {{-- $('#parent_id').append('<option value="">{{__('Select Parent')}}</option>'); --}}

                    $.each(data, function(key, value) {
                        $('#parent_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#parent_id').empty();
                    }
                }
            });
        }
    </script>

    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })

        $(document).on('change', 'select[name=opportunity]', function() {

            var opportunities = $(this).val();
            console.log(opportunities);
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '{{ route('quote.getaccount') }}',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data);
                    $('#amount').val(data.opportunitie.amount);
                    $('#name').val(data.opportunitie.name);
                    $('#account_name').val(data.account.name);
                    $('#account_id').val(data.account.id);
                    $('#billing_address').val(data.account.billing_address);
                    $('#shipping_address').val(data.account.shipping_address);
                    $('#billing_city').val(data.account.billing_city);
                    $('#billing_state').val(data.account.billing_state);
                    $('#shipping_city').val(data.account.shipping_city);
                    $('#shipping_state').val(data.account.shipping_state);
                    $('#billing_country').val(data.account.billing_country);
                    $('#billing_postalcode').val(data.account.billing_postalcode);
                    $('#shipping_country').val(data.account.shipping_country);
                    $('#shipping_postalcode').val(data.account.shipping_postalcode);

                }
            });
        }
    </script>
@endpush
