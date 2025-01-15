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
        title=" {{ __('Gantt Chart') }}">
        <i class="ti ti-trending-up text-white"></i>
    </a>

    <a href="{{ route('task.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>

    <a href="{{ route('task.export') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        data-title=" {{ __('Export') }}" title="{{ __('Export') }}">
        <i class="ti ti-file-export"></i>
    </a>

    @can('Create Task')
        <a href="#" data-size="lg" data-url="{{ route('task.create',['task',0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip"data-title="{{ __('Create New Task') }}"
            title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon ">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="datatable" class="table datatable align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Parent') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Stage') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Date Start') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Assigned User') }}</th>
                                    @if (Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('task.edit', $task->id) }}" data-size="md"
                                                data-title="{{ __('Task Details') }}" class="action-item text-primary">
                                                {{ ucfirst($task->name) }}</a>
                                        </td>
                                        <td class="budget">
                                            {{ ucfirst($task->parent) }}
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ ucfirst(!empty($task->stages) ? $task->stages->name : '') }}</span>
                                        </td>
                                        <td>
                                            <span class="budget">{{ \Auth::user()->dateFormat($task->start_date) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ ucfirst(!empty($task->assign_user) ? $task->assign_user->name : '') }}</span>
                                        </td>
                                        @if (Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                            <td class="text-end">
                                                @can('Show Task')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('task.show', $task->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Task Details') }}" title="{{ __('Details') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Task')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('task.edit', $task->id) }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                            data-bs-toggle="tooltip"data-title="{{ __('Task Edit ') }}"
                                                            title="{{ __('Edit') }}"><i class="ti ti-pencil "></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Task')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id]]) !!}
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
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#parents', function() {
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
@endpush
