@extends('layouts.admin')
@section('page-title')
    {{ __('Sales Order') }}
@endsection
@section('title')
    {{ __('Sales Order') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Sales Order') }}</li>
@endsection
@section('action-btn')
    <div class="action-btn me-2">
        <a href="{{ route('salesorder.export') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
            title=" {{ __('Export') }}">
            <i class="ti ti-file-export"></i>
        </a>
    </div>

    @can('Create SalesOrder')
        <div class="action-btn bg-warning me-2">
            <a href="#" data-size="lg" data-url="{{ route('salesorder.create', ['salesorder', 0]) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Sales Order') }}" title="{{ __('Create') }}"
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('ID') }}</th>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Account') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Created At') }} </th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Amount') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Assigned User') }}</th>
                                    @if (Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesorders as $salesorder)
                                    <tr>
                                        <td>
                                            <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                class="btn btn-outline-primary" data-title="{{ __('Quote Details') }}">
                                                {{ \Auth::user()->salesorderNumberFormat($salesorder->salesorder_id) }}
                                            </a>
                                        </td>
                                        <td> {{ ucfirst($salesorder->name) }} </td>
                                        <td>
                                            <span class="budget">{{ ucfirst(!empty($salesorder->accounts) ? $salesorder->accounts->name : '--') }}</span>
                                        </td>
                                        <td>
                                            @if ($salesorder->status == 0)
                                                <span class="badge bg-secondary p-2 px-3" style="width: 79px;">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                            {{-- @elseif($salesorder->status == 1)
                                                <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                            @elseif($salesorder->status == 2)
                                                <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                            @elseif($salesorder->status == 3)
                                                <span class="badge bg-success p-2 px-3">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span> --}}
                                            @elseif($salesorder->status == 1)
                                                <span class="badge bg-info p-2 px-3" style="width: 79px;">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="budget">{{ \Auth::user()->dateFormat($salesorder->created_at) }}</span>
                                        </td>
                                        <td>
                                            <span class="budget">{{ \Auth::user()->priceFormat($salesorder->getTotal()) }}</span>
                                        </td>
                                        <td>
                                            <span class="budget">{{ ucfirst(!empty($salesorder->assign_user) ? $salesorder->assign_user->name : '-') }}</span>
                                        </td>
                                        @if (Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                                            <td class="text-end">
                                                @can('Create SalesOrder')
                                                    <div class="action-btn  me-2">
                                                        {!! Form::open(['method' => 'get', 'route' => ['salesorder.duplicate', $salesorder->id], 'id' => 'duplicate-form-' . $salesorder->id]) !!}
                                                        <a href="#" class="mx-3 bg-success btn btn-sm align-items-center text-white duplicate_confirm"
                                                            data-bs-toggle="tooltip" title="{{ __('Duplicate') }}"
                                                            data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                            data-confirm-yes="document.getElementById('duplicate-form-{{ $salesorder->id }}').submit();">
                                                            <i class="ti ti-copy"></i>
                                                            {!! Form::close() !!}
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Show SalesOrder')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('salesorder.show', $salesorder->id) }}"
                                                            data-size="md" class="mx-3 btn btn-sm bg-warning align-items-center text-white"
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-title="{{ __('SalesOrders Details') }}">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit SalesOrder')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('salesorder.edit', $salesorder->id) }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-title="{{ __('Edit SalesOrders') }}"><i class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete SalesOrder')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id]]) !!}
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

            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {

            $.ajax({
                url: '{{ route('salesorder.getaccount') }}',
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
