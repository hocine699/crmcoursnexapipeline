@extends('layouts.admin')
@section('page-title')
    {{ __('Quote') }}
@endsection
@section('title')
    {{ __('Quote') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Quote') }}</li>
@endsection
@section('action-btn')
    <div class="action-btn me-2">
        <a href="{{ route('quote.export') }}" class="btn btn-sm btn-primary btn-icon m-1"data-toggle="tooltip"
            data-bs-toggle="tooltip" title=" {{ __('Export') }}">
            <i class="ti ti-file-export"></i>
        </a>
    </div>

    @can('Create Quote')
        <div class="action-btn me-2">
            <a href="#" data-url="{{ route('quote.create', ['quote', 0]) }}" data-size="lg" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Quote Item') }}"
                title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon m-1">
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
                                    <th scope="col" class="sort" data-sort="name">{{ __('ID') }}</th>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Account') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Created At') }}
                                    </th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Amount') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Assign User') }}
                                    </th>
                                    @if (Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotes as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('quote.edit', $quote->id) }}" class="btn btn-outline-primary"
                                                data-title="{{ __('Quote Details') }}">
                                                {{ \Auth::user()->quoteNumberFormat($quote->quote_id) }}
                                            </a>
                                        </td>
                                        <td> {{ ucfirst($quote->name) }}</td>
                                        <td>
                                            {{ ucfirst(!empty($quote->accounts) ? $quote->accounts->name : '--') }}
                                        </td>
                                        <td>
                                            @if ($quote->status == 0)
                                                <span class="badge bg-secondary p-2 px-3"
                                                    style="width: 79px;">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                                {{-- @elseif($quote->status == 1)
                                            <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                        @elseif($quote->status == 2)
                                            <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                        @elseif($quote->status == 3)
                                            <span class="badge bg-success p-2 px-3">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span> --}}
                                            @elseif($quote->status == 1)
                                                <span class="badge bg-info p-2 px-3"
                                                    style="width: 79px;">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="budget">{{ \Auth::user()->dateFormat($quote->created_at) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="budget">{{ \Auth::user()->priceFormat($quote->getTotal()) }}</span>
                                        </td>
                                        <td>
                                            <span class="col-sm-12"><span
                                                    class="text-sm">{{ ucfirst(!empty($quote->assign_user) ? $quote->assign_user->name : '-') }}</span></span>
                                        </td>

                                        @if (Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                                            <td class="text-end">
                                                @can('Create Quote')
                                                    <div class="action-btn  me-2">
                                                        {!! Form::open([
                                                            'method' => 'get',
                                                            'route' => ['quote.duplicate', $quote->id],
                                                            'id' => 'duplicate-form-' . $quote->id,
                                                        ]) !!}

                                                        <a href="#"
                                                            class="mx-3 btn bg-secondary btn-sm align-items-center text-white duplicate_confirm"
                                                            data-bs-toggle="tooltip" data-title="{{ __('Duplicate') }}"
                                                            title="{{ __('Duplicate') }}"
                                                            data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                            data-confirm-yes="document.getElementById('duplicate-form-{{ $quote->id }}').submit();">
                                                            <i class="ti ti-copy"></i>
                                                            {!! Form::close() !!}
                                                        </a>
                                                    </div>
                                                @endcan

                                                @if ($quote->converted_salesorder_id == 0)
                                                    <div class="action-btn  me-2">
                                                        {!! Form::open([
                                                            'method' => 'get',
                                                            'route' => ['quote.convert', $quote->id],
                                                            'id' => 'quotes-form-' . $quote->id,
                                                        ]) !!}

                                                        <a href="#"
                                                            class="mx-3 btn btn-sm bg-success align-items-center text-white convert_confirm"
                                                            data-bs-toggle="tooltip"
                                                            data-title="{{ __('Convert to Sales Order') }}"
                                                            title="{{ __('Conver to Sale Order') }}"
                                                            data-confirm="{{ __('You want to confirm convert to sales order. Press Yes to continue or Cancel to go back') }}"
                                                            data-confirm-yes="document.getElementById('quotes-form-{{ $quote->id }}').submit();">
                                                            <i class="ti ti-exchange"></i>
                                                            {!! Form::close() !!}
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('salesorder.show', $quote->converted_salesorder_id) }}"
                                                            class="mx-3 btn bg-success btn-sm align-items-center text-white"
                                                            data-bs-toggle="tooltip"
                                                            data-original-title="{{ __('Sales Order Details') }}"
                                                            title="{{ __('SalesOrders Details') }}">
                                                            <i class="fab fa-stack-exchange"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                                @can('Show Quote')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('quote.show', $quote->id) }}"
                                                            data-size="md"class="mx-3 bg-warning btn btn-sm align-items-center text-white "
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-title="{{ __('Quote Details') }}">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Quote')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('quote.edit', $quote->id) }}"
                                                            class="mx-3 btn btn-sm align-items-center text-white bg-info"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            data-title="{{ __('Edit Quote') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Quote')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id]]) !!}
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
@push('script-page')
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
