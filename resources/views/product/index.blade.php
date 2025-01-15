@extends('layouts.admin')
@section('page-title')
    {{ __('Product') }}
@endsection
@section('title')
    {{ __('Product') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Product') }}</li>
@endsection
@section('action-btn')
    <div class="action-btn me-2">
        <a href="{{ route('product.grid') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
            title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
    </div>

    <div class="action-btn me-2">
        <a href="{{ route('product.export') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
            title="{{ __('Export') }}">
            <i class="ti ti-file-export"></i>
        </a>
    </div>

    <div class="action-btn me-2">
        <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-url="{{ route('product.file.import') }}"
            data-ajax-popup="true" data-title="{{ __('Import Product CSV File') }}" data-bs-toggle="tooltip"
            title=" {{ __('Import') }}">
            <i class="ti ti-file-import"></i>
        </a>
    </div>

    @can('Create Product')
        <div class="action-btn me-2">
            <a href="#" data-url="{{ route('product.create', ['product', 0]) }}" data-size="lg" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Product') }}" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon m-1">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan
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
                                    <th scope="col" class="sort" data-sort="Brand">{{ __('Brand') }}</th>
                                    <th scope="col" class="sort" data-sort="Status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="Price">{{ __('Price') }}</th>
                                    <th scope="col" class="sort" data-sort="assign User">
                                        {{ __('assign User') }}</th>
                                    <th scope="col" class="sort" data-sort="barcode">{{ __('BarCode') }}</th>
                                    @if (Gate::check('Show Product') || Gate::check('Edit Product') || Gate::check('Delete Product'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>
                                            <a href="{{ route('product.edit', $product->id) }}" data-size="md"
                                                data-title="{{ __('Product Details') }}" class="action-item text-primary">
                                                {{ ucfirst($product->name) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ ucfirst(!empty($product->brands->name) ? $product->brands->name : '-') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($product->status == 0)
                                                <span class="badge bg-success p-2 px-3"
                                                    style="width: 88px;">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                                            @elseif($product->status == 1)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 88px;">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="budget">{{ \Auth::user()->priceFormat($product->price) }}</span>
                                        </td>
                                        <td>
                                            <span class="col-sm-12"><span
                                                    class="text-sm">{{ ucfirst(!empty($product->assign_user) ? $product->assign_user->name : '-') }}</span></span>
                                        </td>
                                        <td class="barcode">
                                            {!! DNS1D::getBarcodeHTML("$product->sku", 'C128', 1.4, 22) !!}
                                        </td>
                                        @if (Gate::check('Show Product') || Gate::check('Edit Product') || Gate::check('Delete Product'))
                                            <td class="text-end">
                                                @can('Show Product')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('product.show', $product->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-ajax-popup="true" data-title="{{ __('Product Details') }}"
                                                            class="mx-3 bg-warning btn btn-sm align-items-center text-white">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Product')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('product.edit', $product->id) }}"
                                                            class="mx-3 bg-info btn btn-sm align-items-center text-white"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-title="{{ __('Edit Product') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Product')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm  bg-danger align-items-center text-white show_confirm"
                                                            data-bs-toggle="tooltip" title='Delete'>
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>

                                                    {{-- <a href="#" class="btn  btn-icon btn-danger px-1  " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$product->id}}').submit();">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id],'id'=>'delete-form-'.$product->id]) !!}
                                                    {!! Form::close() !!} --}}
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
