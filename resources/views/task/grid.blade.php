@extends('layouts.admin')
@section('page-title')
    {{ __('Task') }}
@endsection
@section('title')
    {{ __('Task') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Task') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('task.gantt.chart') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Gantt Chart') }}">
        <i class="ti ti-trending-up text-white"></i>
    </a>

    <a href="{{ route('task.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>

    @can('Create Task')
        <a href="#" data-size="lg" data-url="{{ route('task.create', ['task', 0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Task') }}" title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach ($tasks as $task)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex align-items-center">


                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                        @can('Edit Task')
                                            <a href="{{ route('task.edit', $task->id) }}" class="dropdown-item"
                                                data-bs-whatever="{{ __('Edit Task') }}" data-size="md"
                                                data-bs-toggle="tooltip" data-title="{{ __('Edit Task') }}"><i
                                                    class="ti ti-pencil"></i>
                                                {{ __('Edit') }}</a>
                                        @endcan
                                        @can('Show Task')
                                            <a href="#" data-url="{{ route('task.show', $task->id) }}"
                                                data-ajax-popup="true" class="dropdown-item"
                                                data-bs-whatever="{{ __('Task Details') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Task Details') }}"><i class="ti ti-eye"></i>
                                                {{ __('Details') }}</a>
                                        @endcan

                                        @can('Delete Task')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id]]) !!}
                                            <a href="#!" class="dropdown-item show_confirm" data-bs-toggle="tooltip">
                                                <i class="ti ti-trash"></i>{{ __('Delete') }}
                                            </a>
                                            {!! Form::close() !!}
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 justify-content-between">
                            <div class="col-12">
                                <div class="text-center client-box">
                                    <div class="avatar-parent-child">
                                        @php
                                            $profile = \App\Models\Utility::get_file('upload/profile/');
                                        @endphp
                                        <img alt="user-image" class="rounded border-2 border border-primary"
                                            @if (!empty($task->avatar)) src="{{ !empty($task->avatar) ? $profile . $task->avatar : asset(url('./assets/img/clients/160x160/img-1.png')) }}" @else  avatar="{{ $task->name }}" @endif>
                                    </div>
                                    <h5 class="h6 mt-3 mb-1 text-primary">
                                        {{ ucfirst($task->name) }}
                                    </h5>
                                    <div class="mb-1">
                                        {{ ucfirst($task->stages->name) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-md-3">

            <a href="#" class="btn-addnew-project  border border-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create New Task') }}" data-url="{{ route('task.create', ['task', 0]) }}">
                <div class="badge bg-primary proj-add-icon">
                    <i class="ti ti-plus"></i>
                </div>
                <h6 class="mt-4 mb-2">{{ __(' New Task') }}</h6>
                <p class="text-muted text-center"> {{ __(' Click here to add New Task') }}</p>
            </a>
        </div>
    </div>
@endsection
@push('script-page')
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
