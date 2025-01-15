@extends('layouts.admin')

@section('page-title')
    {{ __('Contract') }}
@endsection

@section('title')
    {{ __('Contract') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Contract') }}</li>
@endsection


@section('action-btn')
    @if (\Auth::user()->type == 'owner' && \Auth::user()->type != 'Accountant' && \Auth::user()->type != 'Manager')
        @can('Create Contract')
            <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Create') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Contract') }}"
                data-url="{{ route('contract.create') }}"><i class="ti ti-plus text-white"></i></a>
        @endcan
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-6">
            <div class="card comp-card" style="min-height:110px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="">{{ __('Total Contracts') }}</h6>
                            <h3 class="text-primary">{{ $cnt_contract['total'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-success text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card" style="min-height:110px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="">{{ __('This Month Total Contracts') }}</h6>
                            <h3 class="text-info">{{ $cnt_contract['this_month'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-info text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card" style="min-height:110px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="">{{ __('This Week Total Contracts') }}</h6>
                            <h3 class="text-warning">{{ $cnt_contract['this_week'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-warning text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-6">
            <div class="card comp-card" style="min-height:110px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="">{{ __('Last 30 Days Total Contracts') }}</h6>
                            <h3 class="text-danger">{{ $cnt_contract['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-12">
            <div class="card table-card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table id="datatable" class="table datatable align-items-center">
                            <thead>
                                <tr>
                                    <th width="60px">{{ __('Contract') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <td class="Id">
                                            @can('Show Contract')
                                                <a href="{{ route('contract.show', $contract->id) }}"
                                                    class="btn btn-outline-primary">{{ Auth::user()->contractNumberFormat($contract->id) }}</a>
                                                {{-- @else --}}
                                                {{-- {{ \Auth::User()->contractNumberFormat($contract->id) }} --}}
                                            @endcan

                                        </td>
                                        <td>{{ $contract->name }}</td>
                                        <td>{{ $contract->subject }}</td>
                                        <td>{{ Auth::user()->priceFormat($contract->value) }}</td>
                                        <td>{{ $contract->contract_type->name }}</td>
                                        <td>{{ Auth::user()->dateFormat($contract->start_date) }}</td>
                                        <td>{{ Auth::user()->dateFormat($contract->end_date) }}</td>
                                        <td>
                                            @if ($contract->status == 'accept')
                                                <span
                                                    class="status_badge badge bg-primary  p-2 px-3">{{ __('Accept') }}</span>
                                            @elseif($contract->status == 'decline')
                                                <span
                                                    class="status_badge badge bg-danger p-2 px-3">{{ __('Decline') }}</span>
                                            @elseif($contract->status == 'pending')
                                                <span
                                                    class="status_badge badge bg-warning p-2 px-3">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td class="">


                                            @can('Show Contract')
                                                <div class="action-btn  me-2">
                                                    <a href="{{ route('contract.show', $contract->id) }}" data-size="md" data-bs-toggle="tooltip"
                                                        data-title="{{ __('Contract Details') }}" title="{{ __('Details') }}"
                                                        class="mx-3 btn btn-sm bg-warning align-items-center text-white ">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan

                                            @if (\Auth::user()->type == 'owner' && \Auth::user()->type != 'Accountant' && \Auth::user()->type != 'Manager')
                                                @can('Edit Contract')

                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                        data-url="{{ URL::to('contract/' . $contract->id . '/edit') }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit type') }}" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white ">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                            @endif

                                            @if (\Auth::user()->type == 'owner' && \Auth::user()->type != 'Accountant' && \Auth::user()->type != 'Manager')
                                                @can('Delete Contract')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['contract.destroy', $contract->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm bg-danger align-items-center show_confirm"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Delete') }}">
                                                            <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                        </a>
                                                            {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            @endif
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
@endsection
