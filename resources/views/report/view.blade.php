@extends('layouts.admin')
@section('page-title')
    {{__('Report')}}
@endsection
@section('title')
        {{__('Report')}} {{ '('. $report->name .')' }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('report.index')}}">{{__('Report')}}</a></li>
    <li class="breadcrumb-item">{{__('Show')}}</li>
@endsection
@section('action-btn')
    @can('Edit Report')
    <div class="action-btn ms-2">
        <a href="{{ route('report.edit',$report->id) }}" class="btn btn-sm btn-info btn-icon m-1" data-title="{{__('Report Edit')}}"data-bs-toggle="tooltip" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
        </a>
    </div>
    @endcan
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="cardcard-body">
                    <div class="collapse show float-end" id="collapseExample" style="">
                        {{ Form::open(array('route' => array('report.show',$report->id),'method'=>'get')) }}
                        <div class="row filter-css">
                            <div class="col-auto">
                                {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'form-control datepicker']) }}
                            </div>
                            <div class="col-auto">
                                {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'form-control datepicker']) }}
                            </div>
                            <div class="action-btn ms-2 mt-2" style="margin-right :4px;">
                                <div class="col-auto">
                                    <button type="submit" class="mx-3 bg-primary btn btn-sm align-items-center text-white"
                                        data-bs-toggle="tooltip" data-title="{{ __('Apply') }}"
                                        title="{{ __('Apply') }}"><i class="ti ti-search"></i></button>
                                </div>
                            </div>
                            <div class="action-btn ms-2 mt-2" style="margin-right :4px;">
                                <div class="col-auto">
                                    <a href="{{ route('report.show', $report->id) }}" data-bs-toggle="tooltip"
                                        data-title="{{ __('Reset') }}" title="{{ __('Reset') }}"
                                        class="mx-3 bg-danger btn btn-sm align-items-center text-white"><i
                                            class="ti ti-refresh"></i></a>
                                </div>
                            </div>
                            <div class="action-btn ms-2 mt-2" style="margin-right :4px;">
                                <div class="col-auto ">
                                    <a href="#" onclick="saveAsPDF();"
                                        class="mx-3 bg-primary btn btn-sm align-items-center text-white"
                                        data-bs-toggle="tooltip"
                                        data-title="{{ __('Download') }}"title="{{ __('Download') }}"
                                        id="download-buttons">
                                        <i class="ti ti-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="printableArea">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <dl class="row">

                            @if(!empty($report['startDateRange']) || $report['endDateRange'])
                                <input type="hidden" value="{{$report['name'].' '.__('Report of').' '.$report['startDateRange'].' to '.$report['endDateRange']}}" id="filesname">
                            @else
                                <input type="hidden" value="{{$report['name'].' '.__('Report')}}" id="filesname">
                            @endif
                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ $report->name }}</span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Entity Type')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ucfirst($report->entity_type)}}</span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ !empty($report->assign_user)?$report->assign_user->name:''}}</span></dd>


                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Group By')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">

                        <td>
                             <span class="badge bg-success p-2 px-3">
                            @if($report->entity_type == 'users')
                                     {{__(\App\Models\Report::$users[$report->group_by])}}
                                 @elseif($report->entity_type == 'quotes')
                                     {{__(\App\Models\Report::$quotes[$report->group_by])}}
                                 @elseif($report->entity_type == 'accounts')
                                     {{__(\App\Models\Report::$accounts[$report->group_by])}}
                                 @elseif($report->entity_type == 'contacts')
                                     {{__(\App\Models\Report::$contacts[$report->group_by])}}
                                 @elseif($report->entity_type == 'leads')
                                     {{__(\App\Models\Report::$leads[$report->group_by])}}
                                 @elseif($report->entity_type == 'opportunities')
                                     {{__(\App\Models\Report::$opportunities[$report->group_by])}}
                                 @elseif($report->entity_type == 'invoices')
                                     {{__(\App\Models\Report::$invoices[$report->group_by])}}
                                 @elseif($report->entity_type == 'cases')
                                     {{__(\App\Models\Report::$cases[$report->group_by])}}
                                 @elseif($report->entity_type == 'products')
                                     {{__(\App\Models\Report::$products[$report->group_by])}}
                                 @elseif($report->entity_type == 'tasks')
                                     {{__(\App\Models\Report::$tasks[$report->group_by])}}
                                 @elseif($report->entity_type == 'calls')
                                     {{__(\App\Models\Report::$calls[$report->group_by])}}
                                 @elseif($report->entity_type == 'campaigns')
                                     {{__(\App\Models\Report::$campaigns[$report->group_by])}}
                                 @elseif($report->entity_type == 'sales_orders')
                                     {{__(\App\Models\Report::$sales_orders[$report->group_by])}}
                                 @else
                                     {{__(\App\Models\Report::$users[$report->group_by])}}
                                 @endif
                            </span>
                        </td></span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Chart Type')}}</span></dt>
                            <dd class="col-sm-3">
                            <span class="text-sm">
                            @if($report->chart_type == 0)
                                    <span>{{ __(\App\Models\Report::$chart_type[$report->chart_type]) }}</span>
                                @endif
                            </span>
                            </dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{\Auth::user()->dateFormat($report->created_at)}}</span></dd>

                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Report')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ucfirst($entity_type).' '.__('Summary')}}</span></dd>
                            @if(!empty($report['startDateRange'] || $report['endDateRange'] ))
                            <dt class="col-sm-1"><span class="h6 text-sm mb-0">{{__('Duration')}}</span></dt>
                            <dd class="col-sm-3"><span class="text-sm">{{ $report['startDateRange'] .' '. 'to' . ' '.  $report['endDateRange']}}</span></dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-12 ">
            <div class="card">
                <div class="card-header">
                    <div class="float-end">
                        <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Refferals"><i
                                class="ti ti-info-circle"></i></a>
                    </div>
                    <h5>{{ __('Chart') }}</h5>
                </div>
                <div class="card-body" style="@if($report->chart_type == 'pie') align-self: center;@endif">

                    <div id="report-chart" data-color="primary" ></div>
                    @if($report->chart_type == 'pie')
                    <div class="col-6 ">
                        <div class="row mt-3">
                                {{-- @foreach($label as $lab)
                                <div class="col-6">
                                    <span class="d-flex align-items-center mb-2">
                                        <i class="f-10 lh-1 fas fa-circle text-primary"></i>
                                        <span class="ms-2 text-sm">{{json_encode($lab)}}</span>
                                    </span>
                                </div>
                        @endforeach --}}
                        </div>

                    @endif
                </div>
                </div>
            </div>
        </div>



    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div>
                        <button class="btn btn-light-primary btn-sm csv me-1">Export CSV</button>
                        {{-- <button class="btn btn-light-primary btn-sm sql">Export SQL</button> --}}
                        <button class="btn btn-light-primary btn-sm txt">Export TXT</button>

                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table" id="pc-dt-export">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">#</th>
                                    <th scope="col" class="sort" data-sort="budget">{{__('Total')}}</th>
                                </tr>
                            </thead>
                            <tbody>


                               @foreach($data as $result)
                                    <tr>
                                        @if($entity_type == 'users')
                                        @php( $groupBy = $group_by . '_name')
                                        <td>
                                            {{-- {{ $user->name }} --}}
                                            {{ $result[$groupBy] }}
                                        </td>
                                        @else
                                        @php( $user = \App\Models\User::getIdByUser($result['user_id']))
                                        <td>

                                            {{ $user->name }}
                                            {{-- {{ $result[$groupBy] }} --}}
                                        </td>
                                        @endif
                                        <td class="">
                                            {{ $result['count'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


@endsection
@push('script-page')





<script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>
<script>
    const table = new simpleDatatables.DataTable("#pc-dt-export");
    document.querySelector("button.csv").addEventListener("click", () => {
        table.export({
            type: "csv",
            download: true,
            lineDelimiter: "\n\n",
            columnDelimiter: ";"
        })
    })
    document.querySelector("button.txt").addEventListener("click", () => {
        table.export({
            type: "txt",
            download: true,
        })
    })
    document.querySelector("button.sql").addEventListener("click", () => {
        table.export({
            type: "sql",
            download: true,
            tableName: "export_table"
        })
    })

    document.querySelector("button.json").addEventListener("click", () => {
        table.export({
            type: "json",
            download: true,
            escapeHTML: true,
            space: 3
        })
    })
    document.querySelector("button.excel").addEventListener("click", () => {
        table.export({
            type: "excel",
            download: true,

        })
    })
        document.querySelector("button.pdf").addEventListener("click", () => {
        table.export({
            type: "pdf",
            download: true,


        })
    })
</script>

    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dataTables.buttons.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var filename = $('#filename').val();
            setTimeout(function () {
                $('#reportTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            title: filename
                        }, {
                            extend: 'csvHtml5',
                            title: filename
                        }, {
                            extend: 'pdfHtml5',
                            title: filename
                        },
                    ],

                });
            }, 500);

        });
    </script>



    <script>
        var filename = $('#filesname').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A2'}
            };
            html2pdf().set(opt).from(element).save();

        }
    </script>

    <script>
        var chart_type = '{{$report->chart_type}}';
        if (chart_type == 'bar_vertical' || chart_type == 'bar_horizontal') {
            if (chart_type == 'bar_vertical') {
                chart_type = 'bar';
                var types = false;
                    (function () {
                    var options = {
                    chart: {
                        height: 150,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '30%',
                            borderRadius: 10,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    colors: ["#51459d"],
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 1,
                        colors: ['#fff']
                    },
                    grid: {
                        strokeDashArray: 4,
                    },
                    series: [{
                            name: '{!! $entity_type !!}',
                            data: {!! json_encode($record) !!},
                        }],
                    xaxis: {
                        categories:  {!! json_encode($label) !!},
                    },
                    title: {
                                text: '{!! ucfirst(str_replace('_', ' ', $group_by)) !!}'
                            },
                    };
                    var chart = new ApexCharts(document.querySelector("#report-chart"), options);
                    chart.render();
                    })();
            } else {
                chart_type = 'bar';
                var types = true;

                (function () {
        var options = {
            chart: {
                height: 300,
                type: 'bar',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 10,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            colors: ["#3ec9d6"],
            dataLabels: {
                enabled: true,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            grid: {
                strokeDashArray: 4,
            },
            series: [{
                            name: '{!! $entity_type !!}',
                            data: {!! json_encode($record) !!},
                        }],
            xaxis: {
                categories: {!! json_encode($label) !!},
            },
            title: {
                                text: '{!! ucfirst(str_replace('_', ' ', $group_by)) !!}'
                            },
        };
        var chart = new ApexCharts(document.querySelector("#report-chart"), options);
        chart.render();
    })();

  }

            var WorkedHoursChart = (function () {
                var $chart = $('#report-chart');

                function init($this) {
                    var options = {
                        chart: {
                            width: '100%',
                            type: chart_type,
                            zoom: {
                                enabled: false
                            },
                            toolbar: {
                                show: false
                            },
                            shadow: {
                                enabled: false,
                            },
                        },
                        plotOptions: {
                            bar: {
                                horizontal: types,
                                columnWidth: '30%',
                                endingShape: 'rounded'
                            },
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        series: [{
                            name: '{!! $entity_type !!}',
                            data: {!! json_encode($record) !!},
                        }],
                        xaxis: {
                            labels: {

                                style: {
                                    colors: PurposeStyle.colors.gray[600],
                                    fontSize: '14px',
                                    fontFamily: PurposeStyle.fonts.base,
                                    cssClass: 'apexcharts-xaxis-label',
                                },
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: true,
                                borderType: 'solid',
                                color: PurposeStyle.colors.gray[300],
                                height: 6,
                                offsetX: 0,
                                offsetY: 0
                            },
                            title: {
                                text: '{!! ucfirst(str_replace('_', ' ', $group_by)) !!}'
                            },
                            categories: {!! json_encode($label) !!},
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    color: PurposeStyle.colors.gray[600],
                                    fontSize: '12px',
                                    fontFamily: PurposeStyle.fonts.base,
                                },
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: true,
                                borderType: 'solid',
                                color: PurposeStyle.colors.gray[300],
                                height: 6,
                                offsetX: 0,
                                offsetY: 0
                            }
                        },
                        fill: {
                            type: 'solid'
                        },
                        markers: {
                            size: 4,
                            opacity: 0.7,
                            strokeColor: "#fff",
                            strokeWidth: 3,
                            hover: {
                                size: 7,
                            }
                        },
                        grid: {
                            borderColor: PurposeStyle.colors.gray[300],
                            strokeDashArray: 5,
                        },
                        dataLabels: {
                            enabled: false
                        }
                    }
                    // Get data from data attributes
                    var dataset = $this.data().dataset,
                        labels = $this.data().labels,
                        color = $this.data().color,
                        height = $this.data().height,
                        type = $this.data().type;
                    // Inject synamic properties
                    options.colors = [
                        PurposeStyle.colors.theme[color]
                    ];
                    options.markers.colors = [
                        PurposeStyle.colors.theme[color]
                    ];
                    options.chart.height = height ? height : 350;
                    // Init chart
                    var chart = new ApexCharts($this[0], options);
                    // Draw chart
                    setTimeout(function () {
                        chart.render();
                    }, 300);
                }

                // Events
                if ($chart.length) {
                    $chart.each(function () {
                        init($(this));
                    });
                }
            })();
        }
        else if (chart_type == 'line') {
             var e = $("#report-chart");
            // !function (e) {
            //     var t = {
            //         chart: {width: "100%", zoom: {enabled: !1}, toolbar: {show: !1}, shadow: {enabled: !1}},
            //         stroke: {width: 6, curve: "smooth"},
            //         series: [{
            //             name: "{{__('Order')}}",
            //             data: {!! json_encode($record) !!}}],
            //         xaxis: {labels: {format: "MMM", style: {colors: PurposeStyle.colors.gray[600], fontSize: "14px", fontFamily: PurposeStyle.fonts.base, cssClass: "apexcharts-xaxis-label"}}, axisBorder: {show: !1}, axisTicks: {show: !0, borderType: "solid", color: PurposeStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0}, type: "text", categories:{!! json_encode($label) !!}},
            //         yaxis: {labels: {style: {color: PurposeStyle.colors.gray[600], fontSize: "12px", fontFamily: PurposeStyle.fonts.base}}, axisBorder: {show: !1}, axisTicks: {show: !0, borderType: "solid", color: PurposeStyle.colors.gray[300], height: 6, offsetX: 0, offsetY: 0}},
            //         fill: {type: "solid"},
            //         markers: {size: 4, opacity: .7, strokeColor: "#fff", strokeWidth: 3, hover: {size: 7}},
            //         grid: {borderColor: PurposeStyle.colors.gray[300], strokeDashArray: 5},
            //         dataLabels: {enabled: !1}
            //     }, a = (e.data().dataset, e.data().labels, e.data().color), n = e.data().height, o = e.data().type;
            //     t.colors = [PurposeStyle.colors.theme[a]], t.markers.colors = [PurposeStyle.colors.theme[a]], t.chart.height = n || 350, t.chart.type = o || "line";
            //     var i = new ApexCharts(e[0], t);
            //     setTimeout(function () {
            //         i.render()
            //     }, 300)
            // }($("#report-chart"));

            (function () {
        var options = {
            chart: {
                height: 350,
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
                name:"{{__('Order')}}",
                data: {!! json_encode($record) !!}
            }],
            xaxis: {
                categories: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            colors: ['#453b85'],

            grid: {
                strokeDashArray: 4,
            },
            legend: {
                show: false,
            },
            markers: {
                size: 4,
                colors: ['#453b85'],
                opacity: 0.9,
                strokeWidth: 2,
                hover: {
                    size: 7,
                }
            },
            yaxis: {
                tickAmount: 3,
                min: 10,
                max: 70,
            }
        };
        var chart = new ApexCharts(document.querySelector("#report-chart"), options);
        chart.render();
    })();





        }
        else {
            // var options = {
            //     series: {!! json_encode($record) !!},
            //     chart: {
            //         width: 600,
            //         type: 'pie',
            //     },
            //     labels:{!! json_encode($label) !!},
            //     responsive: [{
            //         breakpoint: 480,
            //         options: {
            //             chart: {
            //                 width: 200
            //             },
            //             legend: {
            //                 position: 'bottom',
            //             }
            //         }
            //     }]
            // };

            // var chart = new ApexCharts(document.querySelector("#report-chart"), options);
            // chart.render();


            (function () {
                var options = {
                    chart: {
                        height: 340,
                        width: 600,
                        type: 'donut',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                            }
                        }
                    },
                    series:{!! json_encode($record) !!},
                    colors: ["#CECECE", '#ffa21d', '#FF3A6E', '#3ec9d6', '#FF3A6E'],
                    labels:{!! json_encode($label) !!},
                    legend: {
                        show: true
                    }
                };
                var chart = new ApexCharts(document.querySelector("#report-chart"), options);
                chart.render();
            })();
        }
    </script>
@endpush
