@extends('layouts.admin')
@section('page-title')
    {{ __('Quote Edit') }}
@endsection
@section('title')
    {{ __('Edit Quote') }} {{ '(' . $quote->name . ')' }}
@endsection
@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
@section('action-btn')
    <div class="btn-group" role="group">
        @if (!empty($previous))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('quote.edit', $previous) }}" class="btn btn-sm btn-primary btn-icon m-1 "
                    data-bs-toggle="tooltip" title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @else
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon disabled m-1" data-bs-toggle="tooltip"
                    title="{{ __('Previous') }}">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </div>
        @endif
        @if (!empty($next))
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="{{ route('quote.edit', $next) }}" class="btn btn-sm btn-primary btn-icon m-1"
                    data-bs-toggle="tooltip" title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @else
            <div class="action-btn  ms-2" style="margin-right :4px;">
                <a href="#" class="btn btn-sm btn-primary btn-icon disabled m-1" data-bs-toggle="tooltip"
                    title="{{ __('Next') }}">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('quote.index') }}">{{ __('Quote') }}</a></li>
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
                                class="list-group-item list-group-item-action border-0">{{ __('Sales Orders') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-3"
                                class="list-group-item list-group-item-action border-0">{{ __('Invoice') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1" class="card">
                        {{ Form::model($quote, ['route' => ['quote.update', $quote->id], 'method' => 'PUT','class' => 'needs-validation', 'novalidate']) }}
                        <div class="card-header">
                            @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
                                <div class="float-end">
                                    <a href="#" data-size="md" class="btn btn-sm btn-primary "
                                        data-ajax-popup-over="true" data-size="md"
                                        data-title="{{ __('Generate content with AI') }}"
                                        data-url="{{ route('generate', ['quote']) }}" data-toggle="tooltip"
                                        title="{{ __('Generate') }}">
                                        <i class="fas fa-robot"></span><span
                                                class="robot">{{ __('Generate With AI') }}</span></i>
                                    </a>
                                </div>
                            @endif
                            <h5>{{ __('Overview') }}</h5>
                            <small class="text-muted">{{ __('Edit about your quote information') }}</small>
                        </div>

                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
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
                                            {{ Form::label('opportunity', __('Opportunity'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('opportunity', $opportunity, null, ['class' => 'form-control', 'required' => 'required','placeholder'=>__('Select Opportunity')]) !!}
                                            @error('opportunity')
                                                <span class="invalid-opportunity" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                            <select name="status" id="status" class="form-control" required>
                                                @foreach ($status as $k => $v)
                                                    <option value="{{ $k }}"
                                                        {{ $quote->status == $k ? 'selected' : '' }}>{{ __($v) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <span class="invalid-status" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}
                                            {!! Form::select('account', $account, null, ['class' => 'form-control','placeholder'=>__('Select Account')]) !!}
                                            @error('account')
                                                <span class="invalid-account" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('date_quoted', __('Date Quoted'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::date('date_quoted', date('Y-m-d'), ['class' => 'form-control datepicker', 'placeholder' => __('Enter Date Quoted'), 'required' => 'required']) }}
                                            @error('date_quoted')
                                                <span class="invalid-date_quoted" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('quote_number', __('Quote Number'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::number('quote_number', null, ['class' => 'form-control', 'placeholder' => __('Enter Quote Number'), 'required' => 'required']) }}
                                            @error('quote_number')
                                                <span class="invalid-quote_number" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('billing_address', __('Billing Address'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('billing_address', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing Address'), 'required' => 'required']) }}
                                            @error('billing_address')
                                                <span class="invalid-billing_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('shipping_address', __('Shipping Address'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::text('shipping_address', null, ['class' => 'form-control', 'placeholder' => __('Enter Shipping Address'), 'required' => 'required']) }}
                                            @error('shipping_address')
                                                <span class="invalid-shipping_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('billing_city', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing City'), 'required' => 'required']) }}
                                            @error('billing_city')
                                                <span class="invalid-billing_city" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('billing_state', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing State'), 'required' => 'required']) }}
                                            @error('billing_state')
                                                <span class="invalid-billing_state" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('shipping_city', null, ['class' => 'form-control', 'placeholder' => __('Enter Shipping City'), 'required' => 'required']) }}
                                            @error('shipping_city')
                                                <span class="invalid-shipping_city" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('shipping_state', null, ['class' => 'form-control', 'placeholder' => __('Enter Shipping State'), 'required' => 'required']) }}
                                            @error('shipping_state')
                                                <span class="invalid-shipping_state" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('billing_country', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing country'), 'required' => 'required']) }}
                                            @error('billing_country')
                                                <span class="invalid-billing_country" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('billing_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Enter Billing Postal Code'), 'required' => 'required']) }}
                                            @error('billing_postalcode')
                                                <span class="invalid-billing_postalcode" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('shipping_country', null, ['class' => 'form-control', 'placeholder' => __('Enter Shipping Country'), 'required' => 'required']) }}
                                            @error('shipping_country')
                                                <span class="invalid-shipping_country" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            {{ Form::text('shipping_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Enter Shipping Postal Code'), 'required' => 'required']) }}
                                            @error('shipping_postalcode')
                                                <span class="invalid-shipping_postalcode" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('billing_contact', __('Billing Contact'), ['class' => 'form-label']) }}
                                            {!! Form::select('billing_contact', $billing_contact, null, ['class' => 'form-control','placeholder'=>__('Select Billing Contact')]) !!}
                                            @error('billing_contact')
                                                <span class="invalid-billing_contact" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('shipping_contact', __('Shipping Contact'), ['class' => 'form-label']) }}
                                            {!! Form::select('shipping_contact', $billing_contact, null, ['class' => 'form-control','placeholder'=>__('Select Shipping Contact')]) !!}
                                            @error('shipping_contact')
                                                <span class="invalid-shipping_contact" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                      <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('user', __('Assigned User'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {!! Form::select('user', $user, $quote->user_id, ['class' => 'form-control', 'required' => 'required','placeholder'=>__('Select User')]) !!}
                                        </div>
                                        @error('user')
                                            <span class="invalid-user" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            {{ Form::label('shipping_provider', __('Shipping Provider'), ['class' => 'form-label']) }}
                                            {!! Form::select('shipping_provider', $shipping_provider, null, [
                                                'class' => 'form-control',
                                                'required' => 'required',
                                            ]) !!}
                                            @error('shipping_provider')
                                                <span class="invalid-shipping_provider" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
                                            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
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
                                    <h5>{{ __('Sales Orders') }}</h5>
                                    <small class="text-muted">{{ __('Assign sales orders for this quote') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('salesorder.create', ['quote', $quote->id]) }}"
                                            data-bs-toggle="tooltip"data-ajax-popup="true"
                                            title="{{ __('Create') }}"data-title="{{ __('Create New Sales Orders') }}"
                                            class="btn btn-sm btn-primary btn-icon-only">
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
                                            <th scope="col" class="sort" data-sort="budget">{{ __('Account') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">{{ __('Amount') }}
                                            </th>
                                            <th scope="col" class="text-right">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesorders as $salesorder)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                        class="action-item text-primary"
                                                        data-title="{{ __('SalesOrders Details') }}">
                                                        {{ $salesorder->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ !empty($salesorder->accounts->name) ? $salesorder->accounts->name : '-' }}
                                                </td>
                                                <td>
                                                    @if ($salesorder->status == 0)
                                                        <span
                                                            class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 1)
                                                        <span
                                                            class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 2)
                                                        <span
                                                            class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 3)
                                                        <span
                                                            class="badge status_badge bg-success p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 4)
                                                        <span
                                                            class="badge status_badge bg-warning p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @elseif($salesorder->status == 5)
                                                        <span
                                                            class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($salesorder->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->priceFormat($salesorder->amount) }}</span>
                                                </td>
                                                <td class="text-right">
                                                    @can('Show SalesOrder')
                                                        <div class="action-btn  me-2">
                                                            <a href="{{ route('salesorder.show', $salesorder->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                class="mx-3 bg-warning btn btn-sm align-items-center text-white"
                                                                data-title="{{ __('SalesOrders Details') }}">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('Edit SalesOrder')
                                                        <div class="action-btn  me-2">
                                                            <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                class="mx-3 bg-info btn btn-sm align-items-center text-white"
                                                                data-title="{{ __('Edit SalesOrders') }}"><i
                                                                    class="ti ti-pencil"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete SalesOrder')
                                                        <div class="action-btn ">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id]]) !!}
                                                            <a href="#!"
                                                                class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
                                                                data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </td>
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
                                    <h5>{{ __('Invoice') }}</h5>
                                    <small class="text-muted">{{ __('Assign invoice for this quote') }}</small>
                                </div>
                                <div class="col">
                                    <div class="text-end">
                                        <a href="#" data-size="lg"
                                            data-url="{{ route('invoice.create', ['quote', $quote->id]) }}"
                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                            data-title="{{ __('Create New Invoice') }}" title="{{ __('Create') }}"
                                            class="btn btn-sm btn-primary btn-icon-only ">
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
                                            <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                            <th scope="col" class="sort" data-sort="budget">{{ __('Account') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="status">{{ __('Status') }}
                                            </th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Created At') }}</th>
                                            <th scope="col" class="sort" data-sort="completion">
                                                {{ __('Amount') }}</th>
                                            <th scope="col">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoices as $invoice)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('invoice.edit', $invoice->id) }}"
                                                        class="action-item text-primary"
                                                        data-title="{{ __('Invoice Details') }}">
                                                        {{ $invoice->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ !empty($invoice->accounts->name) ? $invoice->accounts->name : '-' }}
                                                </td>
                                                <td>
                                                    @if ($invoice->status == 0)
                                                        <span
                                                            class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 1)
                                                        <span
                                                            class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 2)
                                                        <span
                                                            class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 3)
                                                        <span
                                                            class="badge status_badge bg-success p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 4)
                                                        <span
                                                            class="badge status_badge bg-warning p-2 px-3">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                                    @elseif($invoice->status == 5)
                                                        <span
                                                            class="badge status_badge bg-danger p-2 px-3r">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->dateFormat($invoice->created_at) }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="budget">{{ \Auth::user()->priceFormat($invoice->amount) }}</span>
                                                </td>
                                                <td class="text-right">
                                                    @can('Show Invoice')
                                                        <div class="action-btn  me-2">
                                                            <a href="{{ route('invoice.show', $invoice->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                class="mx-3 btn btn-sm bg-warning align-items-center text-white"
                                                                data-title="{{ __('Invoice Details') }}">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('Edit Invoice')
                                                        <div class="action-btn  me-2">
                                                            <a href="{{ route('invoice.edit', $invoice->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                class="mx-3 btn btn-sm align-items-center text-white bg-info"
                                                                data-title="{{ __('Edit Invoice') }}"><i
                                                                    class="ti ti-pencil"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Invoice')
                                                        <div class="action-btn  ">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id]]) !!}
                                                            <a href="#!"
                                                                class="mx-3 btn btn-sm  align-items-center text-white show_confirm bg-danger"
                                                                data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </td>
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
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })

        $(document).on('change', 'select[name=opportunity]', function() {

            var opportunities = $(this).val();
            console.log(opportunities);
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '{{ route('quote.getaccount') }}',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data);
                    $('#amount').val(data.opportunitie.amount);
                    $('#name').val(data.opportunitie.name);
                    $('#account_name').val(data.account.name);
                    $('#account_id').val(data.account.id);
                    $('#billing_address').val(data.account.billing_address);
                    $('#shipping_address').val(data.account.shipping_address);
                    $('#billing_city').val(data.account.billing_city);
                    $('#billing_state').val(data.account.billing_state);
                    $('#shipping_city').val(data.account.shipping_city);
                    $('#shipping_state').val(data.account.shipping_state);
                    $('#billing_country').val(data.account.billing_country);
                    $('#billing_postalcode').val(data.account.billing_postalcode);
                    $('#shipping_country').val(data.account.shipping_country);
                    $('#shipping_postalcode').val(data.account.shipping_postalcode);

                }
            });
        }
    </script>
@endpush
