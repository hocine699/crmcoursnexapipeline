@extends('layouts.admin')
@section('page-title')
    {{ __('Call') }}
@endsection
@section('title')
    {{ __('Call') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Call') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('call.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>

    @can('Create Call')
        <a href="#" data-size="lg" data-url="{{ route('call.create', ['call', 0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Call') }}"
            title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon">
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
                                    <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Date Start') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Assigned User') }}</th>
                                    @if (Gate::check('Show Call') || Gate::check('Edit Call') || Gate::check('Delete Call'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calls as $call)
                                    <tr>
                                        <td>
                                            <a href="{{ route('call.edit', $call->id) }}" data-size="md"
                                                data-title="{{ __('show Call') }}" class="action-item text-primary">
                                                {{ ucfirst($call->name) }}
                                            </a>
                                        </td>
                                       <td class="budget">
                                            {{ ucfirst($call->parent) }}
                                        </td>
                                        <td>
                                            @if ($call->status == 0)
                                                <span class="badge bg-success p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                                            @elseif($call->status == 1)
                                                <span class="badge bg-warning p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                                            @elseif($call->status == 2)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="budget">{{ \Auth::user()->dateFormat($call->start_date) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ ucfirst(!empty($call->assign_user) ? $call->assign_user->name : '') }}</span>
                                        </td>
                                        @if (Gate::check('Show Call') || Gate::check('Edit Call') || Gate::check('Delete Call'))
                                            <td class="text-end">
                                                @can('Show Call')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('call.show', $call->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Show Call') }}"
                                                            title="{{ __('Details') }}"class="mx-3 bg-warning btn btn-sm  align-items-center text-white  ">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Call')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('call.edit', $call->id) }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white "
                                                            data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Call') }}"title="{{ __('Edit') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Call')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['call.destroy', $call->id]]) !!}
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
                url: '{{ route('call.getparent') }}',
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
