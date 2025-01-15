@extends('layouts.admin')
@section('page-title')
    {{ __('User Edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ __('User') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="{{ route('user.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon m-1"
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
                <a href="{{ route('user.edit', $next) }}" class="btn btn-sm btn-primary btn-icon m-1"
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
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{ __('Edit User (') }} {{ $user->name }}
            {{ ')' }}</h5>
    </div>
@endsection
@section('content')
    <div class="row mt-4">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action border-0">{{ __('Overview') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            @if (\Auth::user()->type != 'super admin')
                                <a href="#useradd-2"
                                    class="list-group-item list-group-item-action border-0">{{ __('Stream') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                            @if (\Auth::user()->type != 'super admin')
                                <a href="#useradd-3"
                                    class="list-group-item list-group-item-action border-0">{{ __('Tasks') }} <div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($user, ['route' => ['user.update', $user->id], 'method' => 'PUT','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your user information') }}</small>
                        </div>

                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {{ Form::label('username', __('User Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
                                            @error('username')
                                                <span class="invalid-name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
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
                                            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Title'), 'required' => 'required']) }}
                                            @error('title')
                                                <span class="invalid-title" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required', 'disabled']) }}
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
                                            {{ Form::label('name', __('Gender'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('gender', $gender ?? '', $user->gender, ['class' => 'form-control', 'required' => 'required']) !!}
                                            @error('gender')
                                                <span class="invalid-name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('user_roles', __('Roles'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('user_roles', $roles, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('Is Active'), ['class' => 'form-label']) }}
                                            <div>
                                                <input type="checkbox" class="form-check-input" name="is_active"
                                                    {{ $user->is_active == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>


                                </div>
                            </form>
                        </div>
                        {{ Form::close() }}
                    </div>

                    @if (\Auth::user()->type != 'super admin')
                        <div id="useradd-2" class="card">
                            {{ Form::open(['route' => ['streamstore', ['user', $user->name, $user->id]], 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
                            <div class="card-header">
                                <h5>{{ __('Stream') }}</h5>
                                <small class="text-muted">{{ __('Add stream comment') }}</small>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    {{ Form::label('stream', __('Stream'), ['class' => 'form-label']) }}<x-required></x-required>
                                                    {{ Form::text('stream_comment', null, ['class' => 'form-control', 'placeholder' => __('Enter Stream Comment'), 'required' => 'required']) }}
                                                </div>
                                            </div>

                                            <input type="hidden" name="log_type" value="user comment">
                                            <div class="col-12 mb-3 field" data-name="attachments">
                                                <div class="attachment-upload">
                                                    <div class="attachment-button">
                                                        <div class="pull-left">
                                                            <div class="form-group">
                                                            {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
                                                            {{-- {{ Form::file('attachment', ['class' => 'form-control']) }} --}}
                                                            <input type="file"name="attachment" class="form-control mb-3" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                            <img id="blah" width="20%" height="20%"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="attachments"></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-12">
                                                <div class="text-end">
                                                    {{ Form::submit(__('Save'), ['class' => 'btn-submit btn btn-primary']) }}
                                                </div>

                                            </div>
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
                                    @if ($remark->data_id == $user->id)
                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-xl-12">
                                                    <ul class="list-group team-msg">
                                                        <li class="list-group-item border-0 d-flex align-items-start mb-2">
                                                            <div class="avatar me-3">
                                                                @php
                                                                    $profile=\App\Models\Utility::get_file('upload/profile/');
                                                                @endphp
                                                                <a href="{{(!empty($stream->file_upload))? ($profile.$stream->file_upload): asset(url("./assets/images/user/avatar-5.jpg"))}}" target="_blank">
                                                                    <img alt="" class="img-user rounded border-2 border border-primary" @if(!empty($stream->file_upload)) src="{{(!empty($stream->file_upload))? ($profile.$stream->file_upload): asset(url("./assets/images/user/avatar-5.jpg"))}}" @else  avatar="{{$remark->user_name}}" @endif>
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
                    @endif

                    @if (\Auth::user()->type != 'super admin')
                        <div id="useradd-3" class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col">
                                        <h5>{{ __('Tasks') }}</h5>
                                        <small class="text-muted">{{ __('Assigned tasks of this user') }}</small>
                                    </div>
                                    <div class="col">
                                        <div class="float-end">
                                            <a href="#" data-size="md" data-url="{{ route('task.create',['task',0]) }}"
                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                data-title="{{ __('Create New Task') }}" title="{{ __('Create') }}"
                                                class="btn btn-sm btn-primary theme bg-primary btn-icon-only ">
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
                                                <th scope="col" class="sort" data-sort="name">
                                                    {{ __('Name') }}</th>
                                                <th scope="col" class="sort" data-sort="budget">
                                                    {{ __('Assign') }}</th>
                                                <th scope="col" class="sort" data-sort="status">
                                                    {{ __('Stage') }}</th>
                                                <th scope="col" class="sort" data-sort="completion">
                                                    {{ __('Date Start') }}</th>
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tasks as $task)
                                                <tr>
                                                    <td>
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('task.show', $task->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Task Details') }}"
                                                            class="action-item text-primary"> {{ $task->name }}</a>
                                                    </td>
                                                    <td>
                                                        {{ $task->parent }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-success p-2 px-3">{{ !empty($task->stages) ? $task->stages->name : '' }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-success p-2 px-3">{{ \Auth::user()->dateFormat($task->start_date) }}</span>
                                                    </td>
                                                    <td class="text-end">

                                                            @can('Show Task')
                                                                <div class="action-btn me-2">
                                                                    <a href="#" data-size="md"
                                                                        data-url="{{ route('task.show', $task->id) }}"
                                                                        data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                        data-title="{{ __('Task Details') }}"
                                                                        title="{{ __('Details') }}"
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
                                                                <div class="action-btn">
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id]]) !!}
                                                                    <a href="#!"
                                                                        class="mx-3 bg-danger btn btn-sm align-items-center text-white show_confirm"
                                                                        data-bs-toggle="tooltip" title='Delete'>
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            @endcan

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    @endif
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
@endpush
