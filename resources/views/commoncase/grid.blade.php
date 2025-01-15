 @extends('layouts.admin')
@section('page-title')
    {{ __('Common case') }}
@endsection
@section('title')
   {{ __('Common case') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Case')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('commoncases.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>

    @can('Create CommonCase')
            <a href="#" data-size="lg" data-url="{{ route('commoncases.create', ['commoncases', 0]) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Common case') }}"title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
<div class="row">
    @foreach ($commonCases as $commonCase)
        <div class="col-md-3">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex align-items-center">
                        {{-- @if ($product->status == 0)
                            <span class="badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                        @elseif($product->status == 1)
                            <span class="badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                        @endif --}}
                    </div>
                    <div class="card-header-right">
                        <div class="btn-group card-option">
                            <button type="button" class="btn dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if (Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))

                                @can('Edit CommonCase')
                                    <a href="{{ route('commoncases.edit', $commonCase->id) }}"
                                        class="dropdown-item" data-bs-whatever="{{ __('Edit Common case') }}"
                                        data-bs-toggle="tooltip" data-title="{{ __('Edit Common case') }}"><i class="ti ti-pencil"></i>
                                        {{ __('Edit') }}</a>
                                @endcan
                                @can('Show CommonCase')
                                    <a href="#" data-url="{{ route('commoncases.show', $commonCase->id) }}"
                                        data-ajax-popup="true" data-size="md"class="dropdown-item"
                                        data-bs-whatever="{{ __('Common case Details') }}"
                                        data-bs-toggle="tooltip" data-title="{{ __('Common case Details') }}"><i class="ti ti-eye"></i>
                                        {{ __('Details') }}</a>
                                @endcan

                                @can('Delete CommonCase')
                                {!! Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $commonCase->id]]) !!}
                                <a href="#!" class="dropdown-item  show_confirm" data-bs-toggle="tooltip">
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
                                    @php
                                        $profile=\App\Models\Utility::get_file('upload/profile/');
                                    @endphp
                                    <img alt="user-image" class="rounded border-2 border border-primary" @if (!empty($commonCase->avatar)) src="{{ !empty($commonCase->avatar) ? $profile . $commonCase->avatar : asset(url('./assets/img/clients/160x160/img-1.png')) }}" @else  avatar="{{ $commonCase->name }}" @endif>
                                    </div>
                                    <h5 class="h6 mt-4 mb-1 text-primary">{{ $commonCase->name }}</h5>
                                    <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right"
                                        title="Account Name">{{ !empty($commonCase->accounts) ? $commonCase->accounts->name : '-' }}</a>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="col-md-3">

        <a href="#" class="btn-addnew-project  border border-primary"  data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Common case') }}" data-url="{{ route('commoncases.create', ['commoncases', 0]) }}">
             <div class="badge bg-primary proj-add-icon">
                <i class="ti ti-plus"></i>
            </div>
            <h6 class="mt-4 mb-2">{{ __('New Common case') }}</h6>
            <p class="text-muted text-center"> {{ __('Click here to add New Common case') }}</p>
        </a>
     </div>
</div>




@endsection
