@extends('layouts.admin')
@section('page-title')
    {{ __('Product Brand') }}
@endsection
@section('title')
    {{ __('Product Brand') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Product Brand') }}</li>
@endsection
@section('action-btn')
    @can('Create ProductBrand')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('product_brand.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create Product Brand') }}"
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
                                    <th scope="col" class="sort" data-sort="name">{{ __('name') }}</th>
                                    @if (Gate::check('Edit ProductBrand') || Gate::check('Delete ProductBrand'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_brands as $product_brand)
                                    <tr>
                                        <td class="sorting_1">{{ $product_brand->name }}</td>
                                        @if (Gate::check('Edit ProductBrand') || Gate::check('Delete ProductBrand'))
                                            <td class="action text-end">
                                                @can('Edit ProductBrand')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('product_brand.edit', $product_brand->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Product Brand') }}" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete ProductBrand')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['product_brand.destroy', $product_brand->id]]) !!}
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
