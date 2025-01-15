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
    <a href="{{ route('document.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>

    @can('Create Document')
        <a href="#" data-size="lg" data-url="{{ route('document.create', ['document', 0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Document') }}" title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
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
                                    <th scope="col" class="sort" data-sort="budget">{{ __('File') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">
                                        {{ __('Created At') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">
                                        {{ __('Assign User') }}</th>
                                    @if (Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr>
                                        <td>
                                            <a href="{{ route('document.edit', $document->id) }}" data-size="md"
                                                data-title="{{ __('Document Details') }}" class="action-item text-primary">
                                                {{ ucfirst($document->name) }}
                                            </a>
                                        </td>
                                        <td class="budget">
                                            @php
                                                $profile = \App\Models\Utility::get_file('upload/profile/');
                                            @endphp
                                            @if (!empty($document->attachment))
                                                <div class="action-btn me-2">
                                                    <a class="mx-3 bg-primary btn btn-sm align-items-center"
                                                        href="{{ $profile . '/' . $document->attachment }}" download="" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Download') }}">
                                                        <i class="ti ti-download text-white"></i>
                                                    </a>
                                                </div>
                                                <div class="action-btn">
                                                    <a class="mx-3 bg-secondary btn btn-sm align-items-center"
                                                        href="{{ $profile . '/' . $document->attachment }}"
                                                        target="_blank">
                                                        <i class="ti ti-crosshair text-white" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Preview') }}"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <span>
                                                    {{ __('No File') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->status == 0)
                                                <span class="badge bg-success p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                            @elseif($document->status == 1)
                                                <span class="badge bg-warning p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                            @elseif($document->status == 2)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                            @elseif($document->status == 3)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ \Auth::user()->dateFormat($document->created_at) }}</span>
                                        </td>
                                        <td>
                                            <span class="col-sm-12"><span
                                                    class="text-sm">{{ ucfirst(!empty($document->assign_user) ? $document->assign_user->name : '-') }}</span></span>
                                        </td>
                                        @if (Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                                            <td class="text-end">
                                                @can('Show Document')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('document.show', $document->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            title="{{ __('Details') }}"
                                                            data-title="{{ __('Document Details') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Document')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('document.edit', $document->id) }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white "
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-title="{{ __('Edit Document') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Document')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
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
