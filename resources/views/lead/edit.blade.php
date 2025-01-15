@extends('layouts.admin')
@section('page-title')
    {{ __('Lead Edit') }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
@section('title')
    <div class="page-header-title">
        {{ __('Edit Lead') }} {{ '(' . $lead->name . ')' }}
    </div>
@endsection
@section('action-btn')
    @if ($lead->is_converted != 0)
        <a href="#" data-url="{{ route('account.show', $lead->is_converted) }}" data-title="{{ __('Account Details') }}"
            data-size="md" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Already Convert To Account') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-eye"></i>
        </a>
    @else
        <a href="#" data-url="{{ route('lead.convert.account', $lead->id) }}" data-size="lg" data-ajax-popup="true"
            data-title="{{ __('Convert [' . $lead->name . '] To Account') }}" data-bs-toggle="tooltip"
            title="{{ __('Convert To Account') }}" class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-exchange">
            </i>
        </a>
    @endif

    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="{{ route('lead.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon "
                    data-bs-toggle="tooltip" title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @else
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon  disabled" data-bs-toggle="tooltip"
                    title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @endif
        @if (!empty($next))
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="{{ route('lead.edit', $next) }}" class="btn btn-sm btn-primary btn-icon "
                    data-bs-toggle="tooltip" title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @else
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon  disabled" data-bs-toggle="tooltip"
                    title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lead.index') }}">{{ __('Lead') }}</a></li>
    <li class="breadcrumb-item">{{ __('Details') }}</li>
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
                                class="list-group-item list-group-item-action border-0">{{ __('Task') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($lead, ['route' => ['lead.update', $lead->id], 'method' => 'PUT','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <div class="float-end">
                                    <a href="#" data-size="md" class="btn btn-sm btn-primary "
                                        data-ajax-popup-over="true" data-size="md"
                                        data-title="{{ __('Generate content with AI') }}"
                                        data-url="{{ route('generate', ['lead']) }}" data-toggle="tooltip"
                                        title="{{ __('Generate') }}">
                                        <i class="fas fa-robot"></span><span
                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                    </a>
                                </div>
                            @endif
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit About Your Lead Information') }}</small>
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
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('account', $account, null, ['class' => 'form-control','placeholder'=>__('Select Account')]) !!}
                                            @error('account_id')
                                                <span class="invalid-account_id" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
                                            @error('email')
                                                <span class="invalid-email" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('phone', __('Phone'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone'), 'required' => 'required']) }}
                                            @error('phone')
                                                <span class="invalid-phone" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div> --}}
    <x-mobile divClass="col-6" name="phone" label="{{ __('Phone')}}" placeholder="{{__('Enter Phone')}}" required="required"></x-mobile>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Title'), 'required' => 'required']) }}
                                            @error('title')
                                                <span class="invalid-phone" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('website', __('Website'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('website', null, ['class' => 'form-control', 'placeholder' => __('Enter Website'), 'required' => 'required']) }}
                                            @error('website')
                                                <span class="invalid-website" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('lead_address', __('Lead Address'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('lead_address', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing Address'), 'required' => 'required']) }}
                                            @error('lead_address')
                                                <span class="invalid-lead_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('lead_city', __('Lead City'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('lead_city', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing City'), 'required' => 'required']) }}
                                            @error('lead_city')
                                                <span class="invalid-lead_city" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-group">
                                            {{ Form::label('lead_state', __('Lead State'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('lead_state', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing State'), 'required' => 'required']) }}
                                            @error('lead_state')
                                                <span class="invalid-lead_state" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            {{ Form::label('lead_postalcode', __('Lead Postal Code'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::number('lead_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Enter Postal Code'), 'required' => 'required']) }}
                                            @error('lead_postalcode')
                                                <span class="invalid-lead_postalcode" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            {{ Form::label('lead_country', __('Lead Country'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('lead_country', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing Country'), 'required' => 'required']) }}
                                            @error('lead_country')
                                                <span class="invalid-lead_country" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('status', $status, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('status')
                                                <span class="invalid-status" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('source', __('Source'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('source', $source, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('source')
                                                <span class="invalid-source" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('opportunity_amount', __('Opportunity Amount'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::number('opportunity_amount', null, ['class' => 'form-control', 'required' => 'required','placeholder'=>__('Enter Amount')]) !!}
                                            @error('source')
                                                <span class="invalid-opportunity_amount" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('campaign', __('Campaign'), ['class' => 'form-label']) }}
                                            {!! Form::select('campaign', $campaign, null, ['class' => 'form-control','placeholder'=>__('Select Campaign')]) !!}
                                            @error('campaign')
                                                <span class="invalid-campaign" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('industry', __('Industry'), ['class' => 'form-label']) }}
                                            {!! Form::select('industry', $industry, null, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('industry')
                                                <span class="invalid-industry" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('user', __(' Assigned User'), ['class' => 'form-label']) }}
                                            {!! Form::select('user', $user, $lead->user_id, ['class' => 'form-control','placeholder'=>__('Select User')]) !!}
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
                                            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required','placeholder'=>__('Enter Description')]) !!}
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
                        {{ Form::open(['route' => ['streamstore', ['lead', $lead->name, $lead->id]], 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
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
                                    <input type="hidden" name="log_type" value="lead comment">
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
                                @if ($remark->data_id == $lead->id)
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
                                    <h5>{{ __('Tasks') }}</h5>
                                    <small class="text-muted">{{ __('Assigned Tasks for this lead') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg" data-url="{{ route('task.create',['task',0]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create New Task') }}" title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only ">
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
                                                    <a href="#" data-size="md"
                                                        data-url="{{ route('task.show', $task->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Task Details') }}"
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
                                                        <div class="">
                                                            @can('Show Task')
                                                                <div class="action-btn me-2">
                                                                    <a href="#" data-size="md"
                                                                        data-url="{{ route('task.show', $task->id) }}"
                                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                        title="{{ __('Details') }}"
                                                                        data-title="{{ __('Task Details') }}"
                                                                        class="btn btn-sm  bg-warning align-items-center text-white">
                                                                        <i class="ti ti-eye"></i>
                                                                    </a>
                                                                </div>
                                                            @endcan
                                                            @can('Edit Task')
                                                                <div class="action-btn me-2">
                                                                    <a href="{{ route('task.edit', $task->id) }}"
                                                                        class="btn btn-sm bg-info align-items-center text-white"
                                                                        data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                        data-title="{{ __('Edit Task') }}"><i
                                                                            class="ti ti-pencil"></i></a>
                                                                </div>
                                                            @endcan
                                                            @can('Delete Task')
                                                                <div class="action-btn">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id]]) !!}
                                                                    <a href="#!"
                                                                        class="btn btn-sm bg-danger align-items-center text-white show_confirm"
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
            console.log('h');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '{{ route('task.getparent') }}',
                type: 'POST',
                data: {
                    "parent": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
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
        });
    </script>
@endpush
