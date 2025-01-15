@extends('layouts.admin')
@section('page-title')
    {{ __('Target Lists') }}
@endsection
@section('title')
    {{ __('Target List') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Target List') }}</li>
@endsection
@section('action-btn')
    @can('Create TargetList')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('target_list.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Target Lists') }}" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon m-1">
                <i class="ti ti-plus"></i>
            </a>
        </div>
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
                                    <th scope="col" class="sort" data-sort="name">{{ __('Type') }}</th>
                                    @if (Gate::check('Edit TargetList') || Gate::check('Delete TargetList'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($targetlists as $targetlist)
                                    <tr>
                                        <td class="sorting_1">{{ $targetlist->name }}</td>
                                        @if (Gate::check('Edit TargetList') || Gate::check('Delete TargetList'))
                                            <td class="action text-end">
                                                @can('Edit TargetList')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('target_list.edit', $targetlist->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Target Lists') }}" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete TargetList')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['target_list.destroy', $targetlist->id]]) !!}
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
@endsection
