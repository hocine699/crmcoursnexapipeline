@extends('layouts.admin')
@section('page-title')
    {{ __('Shipping Provider') }}
@endsection
@section('title')
    {{ __('Shipping Provider') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Shipping Provider') }}</li>
@endsection
@section('action-btn')
    @can('Create ShippingProvider')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('shipping_provider.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Shipping Provider') }}" title="{{ __('Create') }}"
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
                                        {{ __('Shipping Provider') }}</th>
                                    @if (Gate::check('Edit ProductTax') || Gate::check('Delete ProductTax'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shipping_providers as $shipping_provider)
                                    <tr>
                                        <td class="sorting_1">{{ $shipping_provider->name }}</td>
                                        @if (Gate::check('Edit ProductTax') || Gate::check('Delete ProductTax'))
                                            <td class="action text-end">
                                                @can('Edit ShippingProvider')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('shipping_provider.edit', $shipping_provider->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Shipping Provider') }}" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info                           align-items-center text-white">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete ShippingProvider')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['shipping_provider.destroy', $shipping_provider->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 bg-danger btn btn-sm align-items-center text-white show_confirm"
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
