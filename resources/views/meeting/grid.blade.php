@extends('layouts.admin')
@section('page-title')
    {{ __('Meeting') }}
@endsection
@section('title')
    {{ __('Meeting') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Meeting') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('meeting.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>

    @can('Create Meeting')
        <a href="#" data-size="lg" data-url="{{ route('meeting.create', ['meeting', 0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Meeting') }}" title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach ($meetings as $meeting)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            @if ($meeting->status == 0)
                                <span
                                    class="badge bg-success p-2 px-3">{{ __(\App\Models\Meeting::$status[$meeting->status]) }}</span>
                            @elseif($meeting->status == 1)
                                <span
                                    class="badge bg-warning p-2 px-3">{{ __(\App\Models\Meeting::$status[$meeting->status]) }}</span>
                            @elseif($meeting->status == 2)
                                <span
                                    class="badge bg-danger p-2 px-3">{{ __(\App\Models\Meeting::$status[$meeting->status]) }}</span>
                            @endif
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Gate::check('Show Meeting') || Gate::check('Edit Meeting') || Gate::check('Delete Meeting'))
                                        @can('Edit Meeting')
                                            <a href="{{ route('meeting.edit', $meeting->id) }}" class="dropdown-item"
                                                data-bs-whatever="{{ __('Edit Meeting') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Edit Meeting') }}"><i class="ti ti-pencil"></i>
                                                {{ __('Edit') }}</a>
                                        @endcan
                                        @can('Show Meeting')
                                            <a href="#" data-url="{{ route('meeting.show', $meeting->id) }}"
                                                data-ajax-popup="true"data-size="md" class="dropdown-item"
                                                data-bs-whatever="{{ __('Meeting Details') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Meeting Details') }}"><i class="ti ti-eye"></i>
                                                {{ __('Details') }}</a>
                                        @endcan

                                        @can('Delete Meeting')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['meeting.destroy', $meeting->id]]) !!}
                                            <a href="#!"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm"
                                                data-bs-toggle="tooltip">
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
                                        {{-- <img @if (!empty($employee->avatar)) src="{{ $profile . '/' . $employee->avatar }}" @else
                                                        avatar="{{ $employee->name }}" @endif
                                                    class="avatar-circle avatar-lg"> --}}
                                        <img alt="user-image" class= "rounded border-2 border border-primary"
                                            @if (!empty($meeting->avatar)) src="{{ !empty($meeting->avatar) ? asset(Storage::url('upload/profile/' . $meeting->avatar)) : asset(url('./assets/img/clients/160x160/img-1.png')) }}" @else  avatar="{{ $meeting->name }}" @endif>
                                    </div>
                                    <h5 class="h6 mt-4 mb-1 text-primary">
                                        {{ ucfirst($meeting->name) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-md-3">

            <a href="#" class="btn-addnew-project border border-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create New Meeting') }}" data-url="{{ route('meeting.create',['meeting',0]) }}">
                <div class="badge bg-primary proj-add-icon">
                    <i class="ti ti-plus"></i>
                </div>
                <h6 class="mt-4 mb-2">{{ __(' New Meeting') }}</h6>
                <p class="text-muted text-center">{{ __(' Click here to add New Meeting') }}</p>
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

            $.ajax({
                url: '{{ route('meeting.getparent') }}',
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
                }
            });
        }
    </script>
@endpush
