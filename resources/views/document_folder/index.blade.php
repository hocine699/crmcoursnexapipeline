@extends('layouts.admin')
@section('page-title')
    {{ __('Document Folders') }}
@endsection
@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Document Folders') }}</h4>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Constant') }}</li>
    <li class="breadcrumb-item">{{ __('Document Folders') }}</li>
@endsection
@section('action-btn')
    @can('Create DocumentFolder')
        <div class="action-btn ms-2">
            <a href="#" data-size="md" data-url="{{ route('document_folder.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create Document Folders') }}" title="{{ __('Create') }}"
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
                                    <th scope="col" class="sort" data-sort="name">{{ __('Folder Name') }}</th>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Folder Name') }}</th>

                                    @if (Gate::check('Edit DocumentFolder') || Gate::check('Delete DocumentFolder'))
                                        <th class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($folders as $folder)
                                    <tr>
                                        <td class="sortingx_1">{{ $folder->name }}</td>
                                        <td class="sortingx_1">
                                            @if (!$folder->children->isEmpty())
                                                @foreach ($folder->children as $folder1)
                                                    {{ $folder1->name }}
                                                @endforeach
                                            @else
                                                {{ 'parent Category' }}
                                            @endif
                                        </td>
                                        @if (Gate::check('Edit DocumentFolder') || Gate::check('Delete DocumentFolder'))
                                            <td class="action text-end">
                                                @can('Edit DocumentFolder')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('document_folder.edit', $folder->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}" data-title="{{ __('Edit  Document Folders') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete DocumentFolder')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['document_folder.destroy', $folder->id]]) !!}
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
@push('scrip-page')
    <script>
        $(document).delegate("li .li_title", "click", function(e) {
            $(this).closest("li").find("ul:first").slideToggle(300);
            $(this).closest("li").find(".location_picture_row:first").slideToggle(300);
            if ($(this).find("i").attr('class') == 'glyph-icon simple-icon-arrow-down') {
                $(this).find("i").removeClass("simple-icon-arrow-down").addClass("simple-icon-arrow-right");
            } else {
                $(this).find("i").removeClass("simple-icon-arrow-right").addClass("simple-icon-arrow-down");
            }
        });
    </script>
@endpush
