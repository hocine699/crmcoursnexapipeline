@extends('layouts.admin')
@section('page-title')
    {{ __('Campaign Edit') }}
@endsection
@section('title')
    {{ __('Edit Campaign') }} {{ '(' . $campaign->name . ')' }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
@section('action-btn')
    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('campaign.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @else
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip"
                    title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @endif
        @if (!empty($next))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('campaign.edit', $next) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @else
            <div class="action-btn  ms-2" style="margin-right :4px;">
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
    <li class="breadcrumb-item"><a href="{{ route('campaign.index') }}">{{ __('Campaign') }}</a></li>
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
                                class="list-group-item list-group-item-action border-0">{{ __('Opportunities') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-3"
                                class="list-group-item list-group-item-action border-0">{{ __('Lead') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($campaign, ['route' => ['campaign.update', $campaign->id], 'method' => 'PUT','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <div class="float-end">
                                    <a href="#" data-size="md" class="btn btn-sm btn-primary"
                                        data-ajax-popup-over="true" data-size="md"
                                        data-title="{{ __('Generate content with AI') }}"
                                        data-url="{{ route('generate', ['campaign']) }}" data-toggle="tooltip"
                                        title="{{ __('Generate') }}">
                                        <i class="fas fa-robot"></span><span
                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                    </a>
                                </div>
                            @endif
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your campaign information') }}</small>
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
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                            {!! Form::select('status', $status, null, ['class' => 'form-control ', 'required' => 'required']) !!}
                                        </div>
                                        @error('status')
                                            <span class="invalid-status" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                                            {!! Form::select('type', $type, null, ['class' => 'form-control ']) !!}
                                        </div>
                                        @error('type')
                                            <span class="invalid-type" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}
                                            {!! Form::date('start_date', date('Y-m-d'), ['class' => 'form-control datepicker']) !!}
                                        </div>
                                        @error('start_date')
                                            <span class="invalid-start_date" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('budget', __('Budget'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::number('budget', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
                                        </div>
                                        @error('budget')
                                            <span class="invalid-budget" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}
                                            {!! Form::date('end_date', date('Y-m-d'), ['class' => 'form-control datepicker']) !!}
                                            @error('end_date')
                                                <span class="invalid-end_date" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('target_list', __('Target Lists'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('target_list', $target_list, null, ['class' => 'form-control ' ,'required' => 'required']) !!}
                                        </div>
                                        @error('target_list')
                                            <span class="invalid-target_list" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('excludingtarget_list', __('Excluding Target Lists'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('excludingtarget_list', $target_list, null, [
                                                'class' => 'form-control ',
                                                'required' => 'required',
                                            ]) !!}
                                        </div>
                                        @error('excludingtarget_list')
                                            <span class="invalid-excludingtarget_list" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Description'), 'required' => 'required']) }}
                                            @error('description')
                                                <span class="invalid-description" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('user', __('Assigned User'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('user', $user, $campaign->user_id, ['class' => 'form-control ', 'required' => 'required','placeholder' => __('Select User')]) !!}
                                            @error('user')
                                                <span class="invalid-user" role="alert">
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
                                    <h5>{{ __('Opportunities') }}</h5>
                                    <small class="text-muted">{{ __('Assign opportunities for this campaign') }}</small>
                                </div>
                                <div class="col">
                                    <div class="float-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('opportunities.create', ['campaign', $campaign->id]) }}"
                                            data-bs-toggle="tooltip" data-ajax-popup="true"
                                            data-title="{{ __('Create New Opportunities') }}"
                                            title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon-only">
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
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Stage') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">{{ __('Amount') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Assigned User') }}</th>
                                            @if (Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($opportunitiess as $opportunities)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opportunities.edit', $opportunities->id) }}"
                                                        data-size="md" data-title="{{ __('Opportunities Details') }}"
                                                        class="action-item text-primary">
                                                        {{ ucfirst($opportunities->name) }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="budget">
                                                        {{ ucfirst(!empty($opportunities->stages) ? $opportunities->stages->name : '-') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->priceFormat($opportunities->amount) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ ucfirst(!empty($opportunities->assign_user) ? $opportunities->assign_user->name : '-') }}</span>
                                                </td>
                                                @if (Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                                    <td class="text-end">
                                                        @can('Show Opportunities')
                                                            <div class="action-btn me-2">
                                                                <a href="#" data-size="md"
                                                                    data-url="{{ route('opportunities.show', $opportunities->id) }}"
                                                                    data-bs-toggle="tooltip"title="{{ __('Details') }}"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('opportunities Details') }}"
                                                                    class="mx-3 btn btn-sm bg-warning align-items-center text-white ">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit Opportunities')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('opportunities.edit', $opportunities->id) }}"
                                                                    class="mx-3 bg-info btn btn-sm  align-items-center text-white "
                                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Opportunities Edit') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Opportunities')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id]]) !!}
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

                    <div id="useradd-3" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5>{{ __('Leads') }}</h5>
                                    <small class="text-muted">{{ __('Assign lead for this campaign') }}</small>
                                </div>
                                <div class="col">
                                    <div class="float-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('lead.create', ['campaign', $campaign->id]) }}"
                                            data-ajax-popup="true" data-title="{{ __('Create New Lead') }}"
                                            data-bs-toggle="tooltip" title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only">
                                            <i class="ti ti-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datatable" id="datatable1">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="budget">{{ __('Email') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Phone') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('City') }}</th>
                                            @if (Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leads as $lead)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('lead.edit', $lead->id) }}" data-size="md"
                                                        data-title="{{ __('Lead Details') }}"
                                                        class="action-item text-primary">
                                                        {{ $lead->name }}
                                                    </a>
                                                </td>
                                                <td class="budget">
                                                    {{ $lead->email }}
                                                </td>
                                                <td>
                                                    <span class="budget">
                                                        {{ $lead->phone }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="budget">{{ $lead->lead_city }}</span>
                                                </td>
                                                @if (Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                                    <td class="text-end">

                                                        @can('Show Lead')
                                                            <div class="action-btn me-2">
                                                                <a href="#" data-size="md"
                                                                    data-url="{{ route('lead.show', $lead->id) }}"
                                                                    data-bs-toggle="tooltip" data-ajax-popup="true"
                                                                    data-title="{{ __('Lead Details') }}"
                                                                    title="{{ __('Details') }}"
                                                                    class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Edit Lead')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('lead.edit', $lead->id) }}"
                                                                    class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                                    data-bs-toggle="tooltip"
                                                                    data-title="{{ __('Edit Lead') }}"
                                                                    title="{{ __('Edit') }}"><i
                                                                        class="ti ti-pencil"></i></a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete Lead')
                                                            <div class="action-btn">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm bg-danger  align-items-center text-white show_confirm"
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
