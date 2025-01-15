@extends('layouts.admin')
@push('script-page')
    <script>
        EngagementChart = function() {
            var e = $("#plan_order");
            e.length && e.each(function() {

                (function() {
                    var options = {
                        chart: {
                            height: 300,
                            type: 'area',
                            toolbar: {
                                show: false,
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: 2,
                            curve: 'smooth'
                        },
                        series: [{
                            name: "{{ __('Order') }}",
                            data: {!! json_encode($chartData['data']) !!}
                        }],
                        xaxis: {
                            categories: {!! json_encode($chartData['label']) !!},
                            title: {
                                text: '{{ __('Days') }}'
                            }
                        },
                        colors: ['#453b85'],

                        grid: {
                            strokeDashArray: 4,
                        },
                        legend: {
                            show: false,
                        },

                        yaxis: {
                            tickAmount: 3,
                            min: 10,
                            max: 70,
                        }
                    };
                    var chart = new ApexCharts(document.querySelector("#plan_order"), options);
                    chart.render();
                })();

            })
        }()
    </script>
@endpush
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('title')
    {{ __('Dashboard') }}
@endsection
@section('breadcrumb')
@endsection

@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mt-3">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-user"></i>
                            </div>
                            <div class="ms-3 mb-3 mt-3">
                                <a href="{{ route('user.index') }}"> <h6 class="ml-4 w-60-px">{{ __('Total Users') }}</h6></a>
                            </div>
                        </div>
                        <div class="number-icon ms-3 mb-3 mt-3">
                            <h3>{{ $user->total_user }}</h3>
                        </div>
                        <div class="ms-3 mb-3 mt-3">
                            <h6>{{ __('Paid Users') }} : <span
                                    class="text-dark w-100 d-inline-block">{{ $user['total_paid_user'] }}</span>
                            </h6>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mt-3">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                            <div class="ms-3 mb-3 mt-3">
                                <a href="{{ route('order.index') }}"><h6 class="ml-4">{{ __('Total Orders') }}</h6></a>
                            </div>
                        </div>

                        <div class="number-icon ms-3 mb-3 mt-3">
                            <h3>{{ $user->total_orders }}</h3>
                        </div>
                        <div class="ms-3 mb-3 mt-3">
                            <h6>{{ __('Total Order Amount') }} : <span
                                    class="text-dark">{{ env('CURRENCY_SYMBOL') }}{{ $user['total_orders_price'] }}</span>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-3 mt-3">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-award"></i>
                            </div>
                            <div class="ms-3 mb-3 mt-3">
                                <a href="{{ route('plan.index') }}"> <h6 class="ml-4">{{ __('Total Plans') }}</h6></a>
                            </div>
                        </div>

                        <div class="number-icon ms-3 mb-3 mt-3">
                            <h3>{{ $user->total_plan }}</h3>
                        </div>
                        <div class="ms-3 mb-3 mt-3">
                            <h6>{{ __('Most Purchase Plan') }} : <span
                                    class="text-dark w-100 d-inline-block">{{ $user['most_purchese_plan'] }}</span></h6>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Order') }}</h5>
                </div>
                <div class="card-body">
                    <div id="plan_order" data-color="primary" data-height="230"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
