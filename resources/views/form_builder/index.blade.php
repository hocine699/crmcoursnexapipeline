    @extends('layouts.admin')
    @push('script-page')
    <script>
        $(document).ready(function() {
            $('.cp_link').on('click', function() {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                show_toastr('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
            });
        });
    </script>
    @endpush
    @section('page-title')
        {{ __('Form Builder') }}
    @endsection
    @section('title')
        {{ __('Form Builder') }}
    @endsection
    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Form Builder') }}</li>
    @endsection
    @section('action-btn')
        @if (\Auth::user()->can('Create Form Builder'))
            <div class="action-btn bg-warning me-2">
                <a href="#" data-url="{{ route('form_builder.create') }}" data-size="md" data-ajax-popup="true"
                    data-title="{{ __('Create New Form') }}"
                    title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip">
                    <i class="ti ti-plus"></i>
                </a>
            </div>
        @endif
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
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Response') }}</th>
                                        @if (\Auth::user()->can('Edit Form Builder') || \Auth::user()->can('Delete Form Builder'))
                                            <th class="text-end" width="200px">{{ __('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($forms as $form)
                                    <tr>
                                        <td>{{ $form->name }}</td>
                                        <td>
                                            {{ $form->response->count() }}
                                        </td>
                                        @if (\Auth::user()->can('Edit Form Builder') || \Auth::user()->can('Delete Form Builder'))
                                            <td class="text-end">
                                                <div class="action-btn  me-2">
                                                    <a href="#" data-size="md"
                                                        data-url="{{ route('form.field.bind', $form->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Convert Setting') }}"
                                                        class="mx-3 btn btn-sm bg-dark align-items-center text-white"
                                                        data-bs-toggle="tooltip" title="{{ __('Convert Setting') }}"><i
                                                            class="ti ti-exchange"></i></a>
                                                </div>

                                                <div class="action-btn  me-2">
                                                    <a href="#"
                                                        class="mx-3 btn btn-sm bg-success align-items-center text-white cp_link"
                                                        data-link="{{ url('/form/' . $form->code) }}" data-bs-toggle="tooltip"
                                                        title="{{ __('copy') }}" data-title="{{ __('Click to copy link') }}"><i
                                                            class="ti ti-file"></i></a>
                                                </div>

                                                @can('Show Form Builder')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('form_builder.show', $form->id) }}"
                                                            class="mx-3 btn btn-sm bg-secondary align-items-center text-white"
                                                            data-bs-toggle="tooltip"
                                                            data-title="{{ __('Form field') }}"title="{{ __('Form field') }}"><i
                                                                class="ti ti-table"></i></a>
                                                    </div>
                                                @endcan
                                                <div class="action-btn  me-2">
                                                    <a href="{{ route('form.response', $form->id) }}"
                                                        class="mx-3 btn btn-sm bg-warning align-items-center text-white"
                                                        data-bs-toggle="tooltip" data-title="{{ __('View Response') }}"
                                                        title="{{ __('View Response') }}"><i class="ti ti-eye"></i></a>
                                                </div>

                                                @can('Edit Form Builder')
                                                    <div class="action-btn me-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                            data-size="md" data-url="{{ route('form_builder.edit', $form->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Form') }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan

                                                @can('Delete Form Builder')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['form_builder.destroy', $form->id]]) !!}
                                                        <a href="#!" title="{{ __('Delete') }}"
                                                            data-bs-toggle="tooltip"class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

