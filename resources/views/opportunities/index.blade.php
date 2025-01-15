@extends('layouts.admin')
@section('page-title')
    {{ __('Opportunities') }}
@endsection
@section('title')
    {{ __('Opportunities') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Opportunities') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('opportunities.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Kanban View') }}">
        <i class="ti ti-layout-kanban"></i>
    </a>
    @can('Create Opportunities')
        <a href="#" data-url="{{ route('opportunities.create', ['opportunities', 0]) }}" data-size="lg"
            data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{ __('Create New Opportunities') }}"
            title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon">
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
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Account') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Stage') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Amount') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Assigned User') }}</th>
                                    @if (Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($opportunitiess as $opportunities)
                                    <tr>
                                        <td>
                                            <a href="{{ route('opportunities.edit', $opportunities->id) }}" data-size="md"
                                                data-title="{{ __('Opportunities Details') }}"
                                                class="action-item text-primary">
                                                {{ ucfirst($opportunities->name) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ ucfirst(!empty($opportunities->accounts) ? $opportunities->accounts->name : '-') }}</span>
                                        </td>
                                        <td>
                                            <span class="budget">
                                                {{ ucfirst(!empty($opportunities->stages) ? $opportunities->stages->name : '-') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ \Auth::user()->priceFormat($opportunities->amount) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ ucfirst(!empty($opportunities->assign_user) ? $opportunities->assign_user->name : '-') }}</span>
                                        </td>
                                        @if (Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                            <td class="text-end">
                                                @can('Show Opportunities')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('opportunities.show', $opportunities->id) }}"
                                                            data-bs-toggle="tooltip"title="{{ __('Details') }}"
                                                            data-ajax-popup="true"
                                                            data-title="{{ __('opportunities Details') }}"
                                                            class="mx-3 btn btn-sm align-items-center text-white bg-warning ">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Opportunities')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('opportunities.edit', $opportunities->id) }}"
                                                            class="mx-3 btn btn-sm align-items-center text-white bg-info "
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-title="{{ __('Opportunities Edit') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Opportunities')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id]]) !!}
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
