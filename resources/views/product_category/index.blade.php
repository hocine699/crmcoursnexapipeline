@extends('layouts.admin')
@section('page-title')
    {{ __('Product Category') }}
@endsection
@section('title')
    {{ __('Product Category') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Product Category') }}</li>
@endsection
@section('action-btn')
    @can('Create ProductCategory')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('product_category.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Product Category') }}" title="{{ __('Create') }}"
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
                                    <th scope="col" class="sort" data-sort="name">{{ __('type') }}</th>
                                    @if (Gate::check('Edit ProductCategory') || Gate::check('Delete ProductCategory'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_categorys as $product_category)
                                    <tr>
                                        <td class="sorting_1">{{ $product_category->name }}</td>
                                        @if (Gate::check('Edit ProductCategory') || Gate::check('Delete ProductCategory'))
                                            <td class="action text-end">
                                                @can('Edit ProductCategory')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('product_category.edit', $product_category->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Product Category') }}" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white ">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete ProductCategory')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['product_category.destroy', $product_category->id]]) !!}
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
