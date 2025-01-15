@extends('layouts.admin')
@section('page-title')
    {{__('Report')}}
@endsection
@section('title')
    {{__('Quote Analytic')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{ __('Report') }}</li>
    <li class="breadcrumb-item">{{__('Quote Analytic')}}</li>
@endsection
@section('action-btn')

@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="cardcard-body">
                    <div class="collapse show float-end" id="collapseExample" style="">

                        {{ Form::open(array('route' => array('report.quoteanalytic'),'method'=>'get')) }}
                        <div class="row filter-css">
                            <div class="col-auto">
                                {{Form::month('start_month',isset($_GET['start_month'])?$_GET['start_month']:date('Y-01'),array('class'=>'form-control'))}}
                            </div>
                            <div class="col-auto">
                                {{Form::month('end_month',isset($_GET['end_month'])?$_GET['end_month']:date('Y-12'),array('class'=>'form-control'))}}
                            </div>
                            <div class="col-auto" style="margin-left: -29px;">
                                {{ Form::select('status', [''=>'Select Status']+$status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control ','style'=>'margin-left: 29px;')) }}
                            </div>
                            <div class="action-btn  ms-5" style="margin-top :7px; margin-right :4px;">
                            <div class="col-auto">
                                <button type="submit" class="mx-3 bg-primary btn btn-sm align-items-center text-white" data-bs-toggle="tooltip" data-title="{{__('Apply')}}" title="{{__('Apply')}}"><i class="ti ti-search"></i></button>
                            </div>
                            </div>
                            <div class="action-btn  ms-2" style="margin-top :7px; margin-right :4px;">
                            <div class="col-auto">
                                <a href="{{route('report.quoteanalytic')}}" data-bs-toggle="tooltip" title="{{__('Reset')}}" data-title="{{__('Reset')}}" class="mx-3 bg-danger btn btn-sm align-items-center text-white"><i class="ti ti-refresh"></i></a>
                            </div>
                            </div>
                            <div class="action-btn ms-2" style="margin-top :7px; margin-right :4px;">
                            <div class="col-auto">
                                <a href="#" onclick="saveAsPDF();" class="mx-3 bg-primary btn btn-sm align-items-center text-white" data-bs-toggle="tooltip" data-title="{{__('Download')}}" title="{{__('Download')}}" id="download-buttons">
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
                            @if(isset($report['startDateRange']) || isset($report['endDateRange']))
                                <input type="hidden" value="{{__('Quote Report of').' '.$report['startDateRange'].' to '.$report['endDateRange']}}" id="filesname">
                            @else
                                <input type="hidden" value="{{__('Quote Report')}}" id="filesname">
                            @endif
                            <div class="col">
                                {{__('Report')}} : <h6>{{__('Quote Summary')}}</h6>
                            </div>
                            <div class="col">
                                {{__('Duration')}} : <h6>{{$report['startDateRange'].' to '.$report['endDateRange']}}</h6>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div id="report-chart"></div>
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
                        {{-- <button class="btn btn-light-primary btn-sm json">Export JSON</button>
                        <button class="btn btn-light-primary btn-sm excel">Export Excel</button>
                        <button class="btn btn-light-primary btn-sm pdf">Export pdf</button> --}}
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table" id="pc-dt-export">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{__('Account Name')}}</th>
                                    <th scope="col" class="sort" data-sort="name">{{__('Assign User')}}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{__('Status')}}</th>
                                    <th scope="col" class="sort" data-sort="name">{{__('Date Quote')}}</th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quote as $result)
                        <tr>
                            <td>
                                {{$result['name']}}
                            </td>
                            <td>
                                {{!empty($result['accounts']['name'])?$result['accounts']['name']:'-'}}
                            </td>
                            <td class="">
                                {{!empty($result['assign_user']['name'])?$result['assign_user']['name']:'-'}}
                            </td>
                            <td>
                                @if ($result->status == 0)
                                    <span class="badge status_badge bg-secondary p-2 px-3">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @elseif($result->status == 1)
                                    <span class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @endif
                                {{-- @if($result->status == 0)
                                    <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @elseif($result->status == 1)
                                    <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @elseif($result->status == 2)
                                    <span class="badge bg-info p-2 px-3 rounded">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @elseif($result->status == 3)
                                    <span class="badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @elseif($result->status == 4)
                                    <span class="badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @elseif($result->status == 5)
                                    <span class="badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Quote::$status[$result->status]) }}</span>
                                @endif --}}
                            </td>
                            <td class="">
                                {{\Auth::user()->dateFormat($result['date_quoted'])}}
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

@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dataTables.buttons.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/buttons.html5.min.js')}}"></script>

    <script src="../assets/js/plugins/simple-datatables.js"></script>
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
        var WorkedHoursChart = (function () {
            var $chart = $('#report-chart');

            (function () {
        var options = {
            chart: {
                width: '100%',
                height: 180,
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
                name: 'Quote',
                data: {!! json_encode($quoteTotal) !!},
            }],
            xaxis: {
                categories: {!! json_encode($monthList) !!},
                title: {
                            text: '{{__('Quote')}}'
                        },
            },
            colors: ['#453b85', '#FF3A6E'],

            grid: {
                strokeDashArray: 4,
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
            },

        };
        var chart = new ApexCharts(document.querySelector("#report-chart"), options);
        chart.render();
    })();


            // Events
            if ($chart.length) {
                $chart.each(function () {
                    init($(this));
                });
            }
        })();
    </script>


<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

@endpush
