@extends('layouts.admin')

@section('page-title')
    {{ 'Contract' }}
@endsection

@section('title')
    {{ 'Contract' }} {{ '(' . $contract->name . ')' }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('contract.index') }}">{{ __('Contract') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'Show' }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('css/summernote/summernote-bs4.css') }}">

    <style>
        .nav-tabs .nav-link-tabs.active {
            background: none;
        }
    </style>
@endpush

@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
    <script>
        Dropzone.autoDiscover = true;
        myDropzone = new Dropzone("#my-dropzone", {
            url: "{{ route('contracts.file.upload', [$contract->id]) }}",
            success: function(file, response) {
                location.reload();
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    show_toastr('{{ __('Success') }}', 'Attachment Create Successfully!', 'success');
                } else {
                    myDropzone.removeFile(file);
                    show_toastr('{{ __('Error') }}', 'File type must be match with Storage setting.',
                        'error');

                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{ __('Error') }}', response.error, 'error');
                } else {
                    show_toastr('{{ __('Error') }}', response.error, 'error');
                }
            }
        });
        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("contract_id", {{ $contract->id }});
        });

        function dropzoneBtn(file, response) {
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "tooltip");
            download.setAttribute('data-original-title', "{{ __('Download') }}");
            download.innerHTML = "<i class='fas fa-download'></i>";

            var del = document.createElement('a');
            del.setAttribute('href', response.delete);
            del.setAttribute('class', "action-btn btn-danger mx-1 mt-1 btn btn-sm d-inline-flex align-items-center");
            del.setAttribute('data-toggle', "tooltip");
            del.setAttribute('data-original-title', "{{ __('Delete') }}");
            del.innerHTML = "<i class='ti ti-trash'></i>";

            del.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm("Are you sure ?")) {
                    var btn = $(this);
                    $.ajax({
                        url: btn.attr('href'),
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'DELETE',
                        success: function(response) {
                            if (response.is_success) {
                                btn.closest('.dz-image-preview').remove();
                            } else {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', response.error, 'error');
                            }
                        }
                    })
                }
            });

            var html = document.createElement('div');
            html.setAttribute('class', "text-center mt-10");
            html.appendChild(download);
            html.appendChild(del);

            file.previewTemplate.appendChild(html);
        }

        @foreach ($contract->files as $file)
        @endforeach
    </script>


    <script>
        // $('.summernote').on('summernote.blur', function () {
        //     alert();
        //     $.ajax({
        //         url: "{{ route('contracts.note.store', $contract->id) }}",
        //         data: {_token: $('meta[name="csrf-token"]').attr('content'), notes: $(this).val()},
        //         type: 'POST',
        //         success: function (response) {
        //             if (response.is_success) {
        //                 // show_toastr('Success', response.success,'success');
        //             } else {
        //                 show_toastr('Error', response.error, 'error');
        //             }
        //         },
        //         error: function (response) {
        //             response = response.responseJSON;
        //             if (response.is_success) {
        //                 show_toastr('Error', response.error, 'error');
        //             } else {
        //                 show_toastr('Error', response, 'error');
        //             }
        //         }
        //     })
        // });



        $(document).ready(function() {
            $('.summernote').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                    ['list', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'unlink']],
                ],
                height: 250,
            });
        });
    </script>


    <script>
        $(document).on('click', '#comment_submit', function(e) {
            var curr = $(this);

            var comment = $.trim($("#form-comment textarea[name='comment']").val());

            if (comment != '') {
                $.ajax({
                    url: $("#form-comment").data('action'),
                    data: {
                        comment: comment,
                        "_token": "{{ csrf_token() }}",
                    },
                    type: 'POST',
                    success: function(data) {

                        location.reload();
                        data = JSON.parse(data);
                        console.log(data);
                        var html = "<div class='list-group-item px-0'>" +
                            "                    <div class='row align-items-center'>" +
                            "                        <div class='col-auto'>" +
                            "                            <a href='#' class='avatar avatar-sm rounded-circle ms-2'>" +
                            "                                <img src=" + data.default_img +
                            " alt='' class='avatar-sm rounded-circle'>" +
                            "                            </a>" +
                            "                        </div>" +
                            "                        <div class='col ml-n2'>" +
                            "                            <p class='d-block h6 text-sm font-weight-light mb-0 text-break'>" +
                            data.comment + "</p>" +
                            "                            <small class='d-block'>" + data.current_time +
                            "</small>" +
                            "                        </div>" +
                            "                        <div class='action-btn bg-danger me-4'><div class='col-auto'><a href='#' class='mx-3 btn btn-sm  align-items-center delete-comment' data-url='" +
                            data.deleteUrl +
                            "'><i class='ti ti-trash text-white'></i></a></div></div>" +
                            "                    </div>" +
                            "                </div>";

                        $("#comments").prepend(html);
                        $("#form-comment textarea[name='comment']").val('');
                        load_task(curr.closest('.task-id').attr('id'));
                        show_toastr('is_success', 'Comment Added Successfully!');
                    },
                    error: function(data) {
                        show_toastr('error', 'Some Thing Is Wrong!');
                    }
                });
            } else {
                show_toastr('error', 'Please write comment!');
            }
        });






        $(document).on("click", ".delete-comment", function() {
            var btn = $(this);

            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                data: {
                    comment: comment,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    load_task(btn.closest('.task-id').attr('id'));
                    show_toastr('success', 'Comment Deleted Successfully!');
                    btn.closest('.list-group-item').remove();
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('error', data.message);
                    } else {
                        show_toastr('error', 'Some Thing Is Wrong!');
                    }
                }
            });
        });
    </script>


    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

    <script>
        $(document).on("click", ".status", function() {

            var status = $(this).attr('data-id');
            var url = $(this).attr('data-url');
            $.ajax({
                url: url,
                type: 'POST',
                data: {

                    "status": status,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    show_toastr('{{ __('Success') }}', 'Status Update Successfully!', 'success');
                    location.reload();
                }

            });
        });
    </script>
@endpush

@section('action-btn')
    <div class="col-md-6 float-end align-items-center justify-content-end">
        @if (\Auth::user()->type == 'owner' && \Auth::user()->type != 'Manager')
            @if ($contract->status == 'accept')
                <div class="action-btn me-2">
                    <a href="{{ route('send.mail.contract', $contract->id) }}" class="btn btn-sm btn-primary btn-icon"
                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Send Email') }}">
                        <i class="ti ti-mail text-white"></i>
                    </a>
                </div>
            @endif
        @endif

        @if (\Auth::user()->type == 'owner' && \Auth::user()->type != 'Manager')
            @if ($contract->status == 'accept')
                <div class="action-btn me-2">
                    <a href="#" data-size="lg" data-url="{{ route('contracts.copy', $contract->id) }}"
                        data-ajax-popup="true" data-title="{{ __('Duplicate Contract') }}"
                        class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Duplicate') }}"><i class="ti ti-copy text-white"></i></a>
                </div>
            @endif
        @endif

        <div class="action-btn me-2">

            <a href="{{ route('contract.download.pdf', \Crypt::encrypt($contract->id)) }}"
                class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Download') }}" target="_blanks"><i class="ti ti-download text-white"></i></a>
        </div>

        <div class="action-btn me-2">
            <a href="{{ route('get.contract', $contract->id) }}" target="_blank" class="btn btn-sm btn-primary btn-icon"
                title="{{ __('Preview') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                <i class="ti ti-eye"></i>
            </a>
        </div>

        @if (
            ((\Auth::user()->type == 'owner' && $contract->owner_signature == '') ||
                (\Auth::user()->type == 'Manager' && $contract->client_signature == '')) &&
                $contract->status == 'accept')
            <div class="action-btn">
                <a href="#" data-size="md"class="btn btn-sm btn-primary btn-icon"
                    data-url="{{ route('signature', $contract->id) }}" data-ajax-popup="true"
                    data-title="{{ __('Signature') }}" data-size="lg" title="{{ __('Signature') }}"
                    data-bs-toggle="tooltip" data-bs-placement="top">
                    <i class="ti ti-writing-sign"></i>
                </a>
            </div>
        @endif

        @php
            $status = App\Models\Contract::status();
        @endphp
        @if (\Auth::user()->type != 'owner')
            <ul class="list-unstyled mb-0 ms-1">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0 ms-0 p-2 rounded-1" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="drp-text hide-mob">
                            <i class=" drp-arrow nocolor hide-mob">{{ ucfirst($contract->status) }}<span
                                    class="ti ti-chevron-down"></span></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        @foreach ($status as $k => $status)
                            <a class="dropdown-item status" data-id="{{ $k }}"
                                data-url="{{ route('contract.status', $contract->id) }}"
                                href="#">{{ ucfirst($status) }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>
        @endif
    </div>

@endsection


@section('content')
    <div class="row mt-4">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#general" class="list-group-item list-group-item-action border-0">{{ __('General') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#attachments"
                                class="list-group-item list-group-item-action border-0">{{ __('Attachment') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#comment" class="list-group-item list-group-item-action border-0">{{ __('Comment') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#notes" class="list-group-item list-group-item-action border-0">{{ __('Notes') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">


                    <div id="general">
                        <?php
                        // $products = $contract->products();
                        // $sources = $contract->sources();
                        // $calls = $contract->calls;
                        // $emails = $contract->emails;
                        ?>
                        <div class="row">
                            <div class="col-xl-7">
                                <div class="row">
                                    <div class="col-lg-4 col-6">
                                        <div class="card">
                                            <div class="card-body" style="min-height: 167px;">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti ti-user-plus"></i>
                                                </div>
                                                <h6 class="mb-3 mt-4">{{ __('Attachment') }}</h6>
                                                <h3 class="mb-0">{{ count($contract->files) }}</h3>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="card">
                                            <div class="card-body" style="min-height: 167px;">
                                                <div class="theme-avtar bg-info">
                                                    <i class="ti ti-click"></i>
                                                </div>
                                                <h6 class="mb-3 mt-4">{{ __('Comment') }}</h6>
                                                <h3 class="mb-0">{{ count($contract->comment) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <div class="card">
                                            <div class="card-body" style="min-height: 167px;">
                                                <div class="theme-avtar bg-warning">
                                                    <i class="ti ti-file"></i>
                                                </div>
                                                <h6 class="mb-3 mt-4 ">{{ __('Notes') }}</h6>
                                                <h3 class="mb-0">{{ count($contract->note) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-5">
                                <div class="card report_card total_amount_card">
                                    <div class="card-body pt-0 pb-0">
                                        <dl class="row mt-2  align-items-center">
                                            <dt class="col-sm-5 h6 text-sm">{{ __('Name') }}</dt>
                                            <dd class="col-sm-5 text-sm"> {{ $contract->name }}</dd>
                                            <dt class="col-sm-5 h6 text-sm">{{ __('Client Name') }}</dt>
                                            <dd class="col-sm-5 text-sm"> {{ $contract->client_name }}</dd>
                                            <dt class="col-sm-5 h6 text-sm">{{ __('Type') }}</dt>
                                            <dd class="col-sm-5 text-sm">{{ $contract->contract_type->name }}</dd>
                                            <dt class="col-sm-5 h6 text-sm">{{ __('Value') }}</dt>
                                            <dd class="col-sm-5 text-sm">
                                                {{ Auth::user()->priceFormat($contract->value) }}</dd>
                                            <dt class="col-sm-5 h6 text-sm">{{ __('Start Date') }}</dt>
                                            <dd class="col-sm-5 text-sm">
                                                {{ Auth::user()->dateFormat($contract->start_date) }}</dd>
                                            <dt class="col-sm-5 h6 text-sm">{{ __('End Date') }}</dt>
                                            <dd class="col-sm-5 text-sm">
                                                {{ Auth::user()->dateFormat($contract->end_date) }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $plansettings = App\Models\Utility::plansettings();
                        @endphp

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                            <div class="float-end">
                                                <a href="#" data-size="md" class="btn btn-sm btn-primary"
                                                    data-ajax-popup-over="true" data-size="md"
                                                    data-title="{{ __('Generate content with AI') }}"
                                                    data-url="{{ route('generate', ['contract show']) }}"
                                                    data-toggle="tooltip" title="{{ __('Generate') }}">
                                                    <i class="fas fa-robot"></span><span
                                                            class="robot">{{ __('Generate With AI') }}</span></i>
                                                </a>
                                            </div>
                                        @endif
                                        <h5>{{ __('Contract Description') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        {{ Form::open(['route' => ['contracts.description.store', $contract->id]]) }}
                                        <div class="form-group mt-3">
                                            <textarea class="tox-target pc-tinymce summernote" name="description" id="summernote" rows="8">{!! $contract->description !!}</textarea>
                                        </div>  
                                        @if ($contract->status == 'accept' && \Auth::user()->type == 'owner')
                                            <div class="col-md-12 text-end mb-0">
                                                {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                            </div>
                                        @endif
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="attachments">
                        <div class="row ">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('Attachments') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class=" ">
                                            <div class="col-md-12 dropzone browse-file" id="my-dropzone"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @foreach ($contract->files as $file)
                            <div class="card mb-3 border shadow-none">
                                <div class="px-3 py-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-sm mb-0">
                                                <a href="#!">{{ $file->files }}</a>
                                            </h6>
                                            <p class="card-text small text-muted">
                                                {{ number_format(\File::size(storage_path('contract_attechment/' . $file->files)) / 1048576, 2) . ' ' . __('MB') }}
                                            </p>
                                        </div>

                                        @php
                                            $attachments = \App\Models\Utility::get_file('contract_attechment');

                                        @endphp
                                        <div class="action-btn bg-warning p-0 w-auto    ">
                                            <a href="{{ $attachments . '/' . $file->files }}"
                                                class=" btn btn-sm d-inline-flex align-items-center" download=""
                                                data-bs-toggle="tooltip" title="Download">
                                                <span class="text-white"><i class="ti ti-download"></i></span>
                                            </a>
                                        </div>


                                        @if ($contract->status == 'accept')
                                            <div class="col-auto actions">
                                                @if (\Auth::user()->type == 'Manager' && \Auth::user()->id == $file->user_id)
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['contracts.file.delete', $contract->id, $file->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm  align-items-center show_confirm "data-bs-toggle="tooltip"
                                                            title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endif

                                                @if (\Auth::user()->type == 'owner')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['contracts.file.delete', $contract->id, $file->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm  align-items-center show_confirm "data-bs-toggle="tooltip"
                                                            title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endif


                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>


                    <div id="comments">
                        <div class="row pt-2">
                            <div class="col-12">
                                <div id="comment">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Comments') }}</h5>
                                        </div>
                                        <div class="card-footer">
                                            <div class="col-12 d-flex">
                                                <div class="form-group mb-0 form-send w-100">
                                                    <form method="post" class="card-comment-box" id="form-comment"
                                                        data-action="{{ route('comment.store', [$contract->id]) }}">
                                                        <textarea rows="1" class="form-control" name="comment" data-toggle="autosize" placeholder="Add a comment..."
                                                            spellcheck="false"></textarea>
                                                        <grammarly-extension data-grammarly-shadow-root="true"
                                                            style="position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                            class="cGcvT"></grammarly-extension>
                                                        <grammarly-extension data-grammarly-shadow-root="true"
                                                            style="mix-blend-mode: darken; position: absolute; top: 0px; left: 0px; pointer-events: none; z-index: 1;"
                                                            class="cGcvT"></grammarly-extension>
                                                    </form>
                                                </div>
                                                @if ($contract->status == 'accept')
                                                    <button id="comment_submit" class="btn btn-send"><i
                                                            class="f-16 text-primary ti ti-brand-telegram">
                                                        </i>
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="">
                                                <div class="list-group list-group-flush mb-0" id="comments">
                                                    @foreach ($contract->comment as $comment)
                                                        <div class="list-group-item px-0">
                                                            <div class="row align-items-center">
                                                                <div class="col-auto">
                                                                    @php
                                                                        $user = \App\Models\User::find($comment->user_id);
                                                                        $profile = \App\Models\Utility::get_file('upload/profile/');
                                                                    @endphp

                                                                    <a href="{{ !empty($user->avatar) ? $profile . '/' . $user->avatar : $profile . '/avatar.png' }}"
                                                                        target="_blank"
                                                                        class="avatar avatar-sm rounded-circle">
                                                                        <img class="rounded border-2 border border-primary" width="50"
                                                                            height="50"
                                                                            src="{{ !empty($user->avatar) ? $profile . '/' . $user->avatar : $profile . '/avatar.png' }}">
                                                                    </a>
                                                                </div>
                                                                <div class="col ml-n2">
                                                                    <p
                                                                        class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                                        {{ $comment->comment }}</p>
                                                                    <small
                                                                        class="d-block">{{ $comment->created_at->diffForHumans() }}</small>
                                                                </div>

                                                                @if ($contract->status == 'accept')
                                                                    <div class="col-auto">
                                                                        @if (\Auth::user()->type == 'Manager' && \Auth::user()->id == $comment->user_id)
                                                                            <div
                                                                                class="col-auto p-0 mx-3  action-btn ">
                                                                                {!! Form::open(['method' => 'GET', 'route' => ['comment.destroy', $comment->id]]) !!}
                                                                                <a href="#!"
                                                                                    class="btn btn-sm bg-danger align-items-center show_confirm"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span>
                                                                                </a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        @endif

                                                                        @if (\Auth::user()->type == 'owner')
                                                                            <div
                                                                                class="col-auto p-0 mx-3 action-btn ">
                                                                                {!! Form::open(['method' => 'GET', 'route' => ['comment.destroy', $comment->id]]) !!}
                                                                                <a href="#!"
                                                                                    class="btn btn-sm bg-danger align-items-center show_confirm"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span>
                                                                                </a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $plansettings = App\Models\Utility::plansettings();
                        @endphp
                        <div id="notes">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                                <div class="float-end">
                                                    <a href="#" data-size="md"
                                                        class="btn btn-primary btn-icon btn-sm"
                                                        data-ajax-popup-over="true" id="grammarCheck"
                                                        data-url="{{ route('grammar', ['grammar']) }}"
                                                        data-bs-placement="top"
                                                        data-title="{{ __('Grammar check with AI') }}">
                                                        <i class="ti ti-rotate"></i>
                                                        <span>{{ __('Grammar check with AI') }}</span>
                                                    </a>
                                                    <a href="#" data-size="md" class="btn btn-sm btn-primary"
                                                        data-ajax-popup-over="true" data-size="md"
                                                        data-title="{{ __('Generate content with AI') }}"
                                                        data-url="{{ route('generate', ['contract notes']) }}"
                                                        data-toggle="tooltip" title="{{ __('Generate') }}">
                                                        <i class="fas fa-robot"></span><span
                                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                                    </a>
                                                </div>
                                            @endif
                                            <h5>{{ __('Notes') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            {{ Form::open(['route' => ['contracts.note.store', $contract->id]]) }}
                                            <div class="form-group">
                                                <textarea class="tox-target pc-tinymce grammer_textarea" style="width:100%" name="note" id="summernote"></textarea>
                                            </div>
                                            @if ($contract->status == 'accept')
                                                <div class="col-md-12 text-end mb-0">
                                                    {{ Form::submit(__('Add'), ['class' => 'btn  btn-primary']) }}
                                                </div>
                                            @endif
                                            {{ Form::close() }}

                                            <div class="">
                                                <div class="list-group list-group-flush mb-0" id="comments">
                                                    @foreach ($contract->note as $note)
                                                        <div class="list-group-item ">
                                                            <div class="row align-items-center">
                                                                <div class="col-auto">
                                                                    @php
                                                                        $user = \App\Models\User::find($note->user_id);
                                                                        $profiles = \App\Models\Utility::get_file('upload/profile/');
                                                                    @endphp

                                                                    <a href="{{ !empty($user->avatar) ? $profiles . '/' . $user->avatar : $profiles . '/avatar.png' }}"
                                                                        target="_blank"
                                                                        class="avatar avatar-sm rounded-circle">
                                                                        <img class="rounded border-2 border border-primary" width="50"
                                                                            height="50"
                                                                            src="{{ !empty($user->avatar) ? $profiles . '/' . $user->avatar : $profiles . '/avatar.png' }}"
                                                                            title="{{ $contract->client_name }}">
                                                                    </a>
                                                                </div>
                                                                <div class="col ml-n2">
                                                                    <p
                                                                        class="d-block h6 text-sm font-weight-light mb-0 text-break">
                                                                        {{ $note->note }}</p>
                                                                    <small
                                                                        class="d-block">{{ $note->created_at->diffForHumans() }}</small>
                                                                </div>


                                                                @if ($contract->status == 'accept')
                                                                    <div class="col-auto">
                                                                        @if (\Auth::user()->type == 'Manager' && \Auth::user()->id == $note->user_id)
                                                                            <div
                                                                                class="col-auto p-0 action-btn ">
                                                                                {!! Form::open(['method' => 'GET', 'route' => ['contracts.note.destroy', $note->id]]) !!}
                                                                                <a href="#!"
                                                                                    class="btn btn-sm bg-danger align-items-center show_confirm"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span>
                                                                                </a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        @endif

                                                                        @if (\Auth::user()->type == 'owner')
                                                                            <div
                                                                                class="col-auto p-0  action-btn ">
                                                                                {!! Form::open(['method' => 'GET', 'route' => ['contracts.note.destroy', $note->id]]) !!}
                                                                                <a href="#!"
                                                                                    class="btn btn-sm bg-danger align-items-center show_confirm"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span>
                                                                                </a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- [ sample-page ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    @endsection
