@extends('layouts.admin')
@section('page-title')
    {{ __('Document') }}
@endsection
@section('title')
    {{ __('Document') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Document') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('document.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>

    @can('Create Document')
        <a href="#" data-size="lg" data-url="{{ route('document.create', ['document', 0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Document') }}"title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach ($documents as $document)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            @if ($document->status == 0)
                                <span
                                    class="badge bg-success p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                            @elseif($document->status == 1)
                                <span
                                    class="badge bg-warning p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                            @elseif($document->status == 2)
                                <span
                                    class="badge bg-danger p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                            @elseif($document->status == 3)
                                <span
                                    class="badge bg-danger p-2 px-3">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                            @endif
                        </div>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if (Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                                        @can('Edit Document')
                                            <a href="{{ route('document.edit', $document->id) }}" class="dropdown-item"
                                                data-bs-whatever="{{ __('Edit Document') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Edit Document') }}"><i class="ti ti-pencil"></i>
                                                {{ __('Edit') }}</a>
                                        @endcan
                                        @can('Show Document')
                                            <a href="#" data-url="{{ route('document.show', $document->id) }}"
                                                data-ajax-popup="true" data-size="md"class="dropdown-item"
                                                data-bs-whatever="{{ __('Document Details') }}" data-bs-toggle="tooltip"
                                                data-title="{{ __('Document Details') }}"><i class="ti ti-eye"></i>
                                                {{ __('Details') }}</a>
                                        @endcan

                                        @can('Delete Document')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id]]) !!}
                                            <a href="#!" class="dropdown-item show_confirm" data-bs-toggle="tooltip">
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
                                        <img alt="user-image" class="rounded border-2 border border-primary"
                                            @if (!empty($document->avatar)) src="{{ !empty($document->avatar) ? asset(Storage::url('upload/profile/' . $document->avatar)) : asset(url('./assets/img/clients/160x160/img-1.png')) }}" @else  avatar="{{ $document->name }}" @endif>
                                    </div>
                                    <h5 class="h6  mb-1 mt-2 text-primary">{{ ucfirst($document->name) }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="float-end">
                            @php
                                $profile = \App\Models\Utility::get_file('upload/profile/');
                            @endphp
                            @if (!empty($document->attachment))
                                <div class="action-btn me-2">
                                    <a class="mx-3 bg-primary  btn btn-sm align-items-center"
                                        href="{{ $profile . '/' . $document->attachment }}" download=""
                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Download') }}">
                                        <i class="ti ti-download text-white"></i>
                                    </a>
                                </div>
                                <div class="action-btn">
                                    <a class="mx-3 bg-secondary btn btn-sm align-items-center"
                                        href="{{ $profile . '/' . $document->attachment }}" target="_blank">
                                        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                            data-bs-original-title="{{ __('Preview') }}"></i>
                                    </a>
                                </div>
                            @else
                                <span>
                                    {{ __('No File') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-md-3">

            <a href="#" class="btn-addnew-project" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create New Document') }}" data-url="{{ route('document.create', ['document', 0]) }}">
                <div class="badge bg-primary proj-add-icon">
                    <i class="ti ti-plus"></i>
                </div>
                <h6 class="mt-4 mb-2">{{__('New Document')}}</h6>
                <p class="text-muted text-center">{{__('Click here to add New Document')}}</p>
            </a>
        </div>
    </div>
@endsection
