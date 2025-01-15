@extends('layouts.admin')
@section('page-title')
    {{ __('Opportunities Stage') }}
@endsection
@section('title')
    {{ __('Opportunities Stage') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Opportunities Stage') }}</li>
@endsection
@section('action-btn')
    @can('Create OpportunitiesStage')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('opportunities_stage.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Opportunities Stage') }}" title="{{ __('Create') }}"
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
                                    <th scope="col" class="sort" data-sort="name">
                                        {{ __('Opportunities Stage') }}</th>
                                    @if (Gate::check('Edit OpportunitiesStage') || Gate::check('Delete OpportunitiesStage'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($opportunities_stages as $opportunities_stage)
                                    <tr>
                                        <td class="sorting_1">{{ $opportunities_stage->name }}</td>
                                        @if (Gate::check('Edit OpportunitiesStage') || Gate::check('Delete OpportunitiesStage'))
                                            <td class="action text-end">
                                                @can('Edit OpportunitiesStage')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('opportunities_stage.edit', $opportunities_stage->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}"
                                                            data-title="{{ __('Edit Opportunities Stage') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete OpportunitiesStage')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities_stage.destroy', $opportunities_stage->id]]) !!}
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
