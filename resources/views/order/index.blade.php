@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{ __('Order') }}
@endsection
@section('title')
    {{ __('Order') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Order') }}</li>
@endsection
@section('action-btn')
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
                                    <th scope="col" class="sort" data-sort="name"> {{ __('Order Id') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Date') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Plan Name') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Price') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Payment Type') }}
                                    </th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion"> {{ __('Coupon') }}</th>
                                    <th scope="col" class="sort text-center" data-sort="completion">
                                        {{ __('Invoice') }}</th>
                                    @if (\Auth::user()->type == 'super admin')
                                        <th scope="col" class="sort" data-sort="completion"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>
                                            {{ $order->order_id }}
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>{{ $order->user_name }}</td>
                                        <td>{{ $order->plan_name }}</td>
                                        <td>{{ env('CURRENCY_SYMBOL') . $order->price }}</td>
                                        <td>{{ $order->payment_type }}</td>

                                        <td>
                                            @if ($order->payment_status == 'succeeded')
                                                {{-- <span class="d-flex align-items-center">
                                                    <i class="f-20 lh-1 ti ti-circle-check text-success"></i>
                                                    <span class="ms-1">{{ ucfirst($order->payment_status) }}</span>
                                                </span> --}}
                                                <span class="status_badge badge bg-primary p-2 px-3">{{ucfirst($order->payment_status)}}</span>

                                            @elseif($order->payment_status == 'Approved')
                                                {{-- <span class="d-flex align-items-center">
                                                    <i class="f-20 lh-1 ti ti-circle-check text-success"></i>
                                                    <span class="ms-1">{{ ucfirst($order->payment_status) }}</span>
                                                </span> --}}
                                                <span class="status_badge badge bg-primary p-2 px-3">{{ucfirst($order->payment_status)}}</span>

                                            @elseif($order->payment_status == 'Pending')
                                                {{-- <span class="d-flex align-items-center">
                                                    <i class="f-20 lh-1 ti ti-circle-check text-warning"></i>
                                                    <span class="ms-1">{{ ucfirst($order->payment_status) }}</span>
                                                </span> --}}
                                                <span class="status_badge badge bg-primary p-2 px-3">{{ucfirst($order->payment_status)}}</span>

                                            @else
                                                {{-- <span class="d-flex align-items-center">
                                                    <i class="f-20 lh-1 ti ti-circle-x text-danger"></i>
                                                    <span class="ms-1">{{ ucfirst($order->payment_status) }}</span>
                                                </span> --}}
                                                <span class="status_badge badge bg-secondary p-2 px-3">{{ucfirst($order->payment_status)}}</span>

                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ !empty($order->total_coupon_used) ? (!empty($order->total_coupon_used->coupon_detail) ? $order->total_coupon_used->coupon_detail->code : '-') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($order->receipt != 'free coupon' && $order->payment_type == 'STRIPE')
                                                <a href="{{ $order->receipt }}" class="btn  btn-outline-primary"
                                                    target="_blank"><i class="fas fa-file-invoice"></i>
                                                    {{ __('Invoice') }}</a>
                                            @elseif ($order->payment_type == 'Bank Transfer')
                                                <a href="{{ \App\Models\Utility::get_file($order->receipt) }}"
                                                    class="btn  btn-outline-primary" target="_blank"><i
                                                        class="fas fa-file-invoice"></i>
                                                    {{ __('Receipt') }}</a>
                                            @elseif($order->receipt == 'free coupon')
                                                <p>{{ __('Used 100 % discount coupon code.') }}</p>
                                            @elseif($order->payment_type == 'Manually')
                                                <p>{{ __('Manually plan upgraded by super admin') }}</p>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @php
                                            $user = App\Models\User::find($order->user_id);
                                        @endphp
                                        @if (\Auth::user()->type == 'super admin')
                                            <td>
                                                @if ($order->payment_status == 'Pending' && $order->payment_type == 'Bank Transfer')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="lg"
                                                            data-url="{{ route('order.show', $order->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-ajax-popup="true" data-title="{{ __('Payment Status') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                            <i class="ti ti-caret-right text-white"></i>
                                                        </a>
                                                    </div>
                                                @endif

                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['bank_transfer.destroy', $order->id]]) !!}
                                                    <a href="#!"
                                                        class="mx-3 btn btn-sm bg-danger align-items-center show_confirm ">
                                                        <i class="ti ti-trash text-white" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Delete') }}"></i>
                                                    </a>
                                                    {!! Form::close() !!}
                                                </div>
                                                @foreach ($userOrders as $userOrder)
                                                    @if ($user->plan == $order->plan_id && $order->order_id == $userOrder->order_id && $order->is_refund == 0)
                                                    <div class="badge bg-warning p-2 px-3 ms-2">
                                                            <a href="{{ route('order.refund', [$order->id, $order->user_id]) }}"
                                                                class="align-items-center" data-bs-toggle="tooltip"
                                                                title="{{ __('Refund') }}"
                                                                data-original-title="{{ __('Refund') }}">
                                                                <span class ="text-white">{{ __('Refund') }}</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
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
