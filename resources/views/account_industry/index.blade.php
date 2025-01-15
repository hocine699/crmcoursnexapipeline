@extends('layouts.admin')
@section('page-title')
    {{ __('Account Industry') }}
@endsection
@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Account Industry') }}</h4>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Account Industry') }}</li>
@endsection
@section('action-btn')
    @can('Create AccountIndustry')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('account_industry.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Account Industry') }}" title="{{ __('Create') }}"
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
                                    <th scope="col" class="sort" data-sort="name">{{ __('industry') }}</th>
                                    @if (Gate::check('Edit AccountIndustry') || Gate::check('Delete AccountIndustry'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($industrys as $industry)
                                    <tr>
                                        <td class="sorting_1">{{ $industry->name }}</td>
                                        @if (Gate::check('Edit AccountIndustry') || Gate::check('Delete AccountIndustry'))
                                            <td class="action text-end">
                                                @can('Edit AccountIndustry')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('account_industry.edit', $industry->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Account Industry') }}" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete AccountIndustry')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['account_industry.destroy', $industry->id]]) !!}
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
