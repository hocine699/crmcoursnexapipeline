@extends('layouts.admin')
@section('page-title')
    {{ __('Product') }}
@endsection
@section('title')
    {{ __('Product') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Product')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('product.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>

    @can('Create Product')
            <a href="#" data-size="lg" data-url="{{ route('product.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Product') }}" title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon ">
                <i class="ti ti-plus"></i>
            </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
<div class="row">
    @foreach ($products as $product)
        <div class="col-md-3">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex align-items-center">
                
                            @if ($product->status == 0)
                            <span
                                class="badge bg-success p-2 px-3">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                            @elseif($product->status == 1)
                                <span
                                    class="badge bg-danger p-2 px-3">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                            @endif
                     
                    </div>
                    <div class="card-header-right">
                        <div class="btn-group card-option">
                            <button type="button" class="btn dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if (Gate::check('Show Product') || Gate::check('Edit Product') || Gate::check('Delete Product'))

                                @can('Edit Product')
                                    <a href="{{ route('product.edit', $product->id) }}" data-size="md"class="dropdown-item"
                                        data-bs-whatever="{{ __('Edit Product') }}" data-bs-toggle="tooltip"
                                        data-title="{{ __('Edit Product') }}"><i class="ti ti-pencil"></i>
                                        {{ __('Edit') }}</a>
                                @endcan
                                @can('Show Product')
                                    <a href="#" data-url="{{ route('product.show', $product->id) }}"
                                        data-ajax-popup="true"data-size="md" class="dropdown-item"
                                        data-bs-whatever="{{ __('Product Details') }}"
                                        data-bs-toggle="tooltip" 
                                        data-title="{{ __('Product Details') }}"><i class="ti ti-eye"></i>
                                        {{ __('Details') }}</a>
                                @endcan

                                @can('Delete Product')
                                {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id]]) !!}
                                <a href="#!" class="dropdown-item  show_confirm" data-bs-toggle="tooltip" >
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
                                    <img alt="user-image" class="img-fluid-circle rounded border-2 border border-primary" @if (!empty($product->avatar)) src="{{ !empty($product->avatar) ? asset(Storage::url('upload/profile/' . $product->avatar)) : asset(url('./assets/img/clients/160x160/img-1.png')) }}" @else  avatar="{{ $product->name }}" @endif>
                                </div>
                                <h5 class="h6 mt-2 mb-1 text-primary">{{ ucfirst($product->name) }}</h5>
                                        
                                <div class="mb-1"><a href="#" class="text-sm small text-muted">{{ $product->email }}</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="col-md-3">
                    
        <a href="#" class="btn-addnew-project border border-primary"  data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Product') }}" data-url="{{ route('product.create') }}">
             <div class="badge bg-primary proj-add-icon">
                <i class="ti ti-plus"></i>
            </div>
            <h6 class="mt-4 mb-2"> {{ __(' New Product') }}</h6>
            <p class="text-muted text-center"> {{ __('Click here to add New Product') }}</p>
        </a>
     </div>
</div>


   


@endsection
