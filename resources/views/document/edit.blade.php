@extends('layouts.admin')
@section('page-title')
    {{ __('Document Edit') }}
@endsection
@section('title')
    {{ __('Edit Document') }} {{ '(' . $document->name . ')' }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
@section('action-btn')
    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="{{ route('document.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @else
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip"
                    title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @endif
        @if (!empty($next))
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="{{ route('document.edit', $next) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @else
            <div class="action-btn ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip"
                    title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('document.index') }}">{{ __('Document') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action border-0">{{ __('Overview') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-2"
                                class="list-group-item list-group-item-action border-0">{{ __('Account') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($document, ['route' => ['document.update', $document->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <div class="float-end">
                                    <a href="#" data-size="md" class="btn btn-sm btn-primary"
                                        data-ajax-popup-over="true" data-size="md"
                                        data-title="{{ __('Generate content with AI') }}"
                                        data-url="{{ route('generate', ['document']) }}" data-toggle="tooltip"
                                        title="{{ __('Generate') }}">
                                        <i class="fas fa-robot"></span><span
                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                    </a>
                                </div>
                            @endif
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your document information') }}</small>
                        </div>

                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
                                            @error('name')
                                                <span class="invalid-name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('folder', __('Folder'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('folder', $folders, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                            @error('folder')
                                                <span class="invalid-folder" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('type', $type, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                            @error('type')
                                                <span class="invalid-type" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                            {!! Form::select('account', $account_name, null, ['class' => 'form-control ', 'required' => 'required','placeholder'=>__('Select Account')]) !!}
                                            @error('account')
                                                <span class="invalid-account" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('opportunities', __('Opportunities'), ['class' => 'form-label']) }}
                                            {!! Form::select('opportunities', $opportunities, null, ['class' => 'form-control ', 'required' => 'required','placeholder'=>__('Select Opportunities')]) !!}
                                            @error('opportunities')
                                                <span class="invalid-opportunities" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('publish_date', __('Publish Date'), ['class' => 'form-label']) }}
                                            {!! Form::date('publish_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'required' => 'required']) !!}
                                            @error('publish_date')
                                                <span class="invalid-publish_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('expiration_date', __('Expiration Date'), ['class' => 'form-label']) }}
                                            {!! Form::date('expiration_date', date('Y-m-d'), [
                                                'class' => 'form-control datepicker',
                                                'required' => 'required',
                                            ]) !!}
                                            @error('expiration_date')
                                                <span class="invalid-expiration_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('status', $status, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                            @error('status')
                                                <span class="invalid-status" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6 mb-3 field" data-name="attachments">
                                        <div class="attachment-upload">
                                            <div class="attachment-button">
                                                <div class="pull-left">
                                                    <div class="form-group">
                                                        {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}<x-required></x-required>
                                                        {{-- {{ Form::file('attachment', ['class' => 'form-control']) }} --}}
                                                        <input type="file"name="attachment" class="form-control mb-3" 
                                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                        <img id="blah"
                                                            src="{{ !empty($document->attachment) ? \App\Models\Utility::get_file('upload/profile' . '/' . $document->attachment) : \App\Models\Utility::get_file('upload/profile/avatar.png') }}"
                                                            width="20%" height="20%" />
                                                    </div>
                                                </div>
                                            </div>
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('user', __('Assigned User'), ['class' => 'form-label']) }}
                                            {!! Form::select('user', $user, $document->user_id, ['class' => 'form-control ','placeholder'=>__('Select User')]) !!}
                                            @error('user')
                                                <span class="invalid-user" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Description'),'required'=>'required']) }}
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        {{ Form::submit(__('Update'), ['class' => 'btn-submit btn btn-primary']) }}
                                    </div>


                                </div>
                            </form>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="useradd-2" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Account') }}</h5>
                                    <small class="text-muted">{{ __('Assigned account for this document') }}</small>
                                </div>
                                <div class="col">
                                    <div class="float-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('account.create', ['document', $document->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Create') }}"
                                            data-title="{{ __('Create New Account') }}"
                                            class="btn btn-sm btn-primary btn-icon-only ">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="budget">{{ __('Website') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Type') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Country') }}</th>
                                            @if (Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('account.edit', $account->id) }}" data-size="md"
                                                        data-title="{{ __('Account Details') }}"
                                                        class="action-item text-primary">
                                                        {{ $account->name }}
                                                    </a>
                                                </td>
                                                <td class="budget">
                                                    {{ $account->website }}
                                                </td>
                                                <td>
                                                    <span class="budget">
                                                        {{ !empty($account->AccountType) ? $account->AccountType->name : '' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="budget">{{ $account->shipping_city }}</span>
                                                </td>
                                                @if (Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                                    <td class="text-end">
                                                        @can('Show Account')
                                                            <div class="action-btn me-2">
                                                                <a href="#" data-size="md"
                                                                    data-url="{{ route('account.show', $account->id) }}"
                                                                    data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('Account Details') }}"
                                                                    class="mx-3 bg-warning btn btn-sm  align-items-center text-white">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit Account')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('account.edit', $account->id) }}"
                                                                    class="mx-3 bg-info btn btn-sm  align-items-center text-white"
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Account') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Account')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id]]) !!}
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
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection
@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
