@extends('layouts.admin')
@section('page-title')
    {{ __('Report') }}
@endsection
@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Report') }}</h4>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Report') }}</li>
    <li class="breadcrumb-item">{{ __('Custom Report') }}</li>
@endsection
@section('action-btn')
    @can('Create Report')
        <div class="action-btn me-4">
            <a href="#" data-size="md" data-url="{{ route('report.create', ['report', 0]) }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Report') }}" title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon me-2">
                <i class="ti ti-plus"></i>
            </a>
            <a href="{{ route('report.export') }}" data-bs-toggle="tooltip" title="{{ __('Export') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-file-export"></i>
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
                        <div class="card body table-border-style"></div>
                        <table id="datatable" class="table datatable align-items-center" id="pc-dt-export">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Entity Type') }}
                                    </th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Group By') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Chart Type') }}
                                    </th>
                                    @if (Gate::check('Show Report') || Gate::check('Edit Report') || Gate::check('Delete Report'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>
                                            <a href="{{ route('report.show', $report->id) }}"
                                                class="action-item text-primary">
                                                {{ $report->name }}
                                            </a>
                                        </td>
                                        <td class="budget">
                                            <span>{{ __(\App\Models\Report::$entity_type[$report->entity_type]) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary p-2 px-3" style="width: 85px;">
                                                @if ($report->entity_type == 'users')
                                                    {{ __(\App\Models\Report::$users[$report->group_by]) }}
                                                @elseif($report->entity_type == 'quotes')
                                                    {{ __(\App\Models\Report::$quotes[$report->group_by]) }}
                                                @elseif($report->entity_type == 'accounts')
                                                    {{ __(\App\Models\Report::$accounts[$report->group_by]) }}
                                                @elseif($report->entity_type == 'contacts')
                                                    {{ __(\App\Models\Report::$contacts[$report->group_by]) }}
                                                @elseif($report->entity_type == 'leads')
                                                    {{ __(\App\Models\Report::$leads[$report->group_by]) }}
                                                @elseif($report->entity_type == 'opportunities')
                                                    {{ __(\App\Models\Report::$opportunities[$report->group_by]) }}
                                                @elseif($report->entity_type == 'invoices')
                                                    {{ __(\App\Models\Report::$invoices[$report->group_by]) }}
                                                @elseif($report->entity_type == 'cases')
                                                    {{ __(\App\Models\Report::$cases[$report->group_by]) }}
                                                @elseif($report->entity_type == 'products')
                                                    {{ __(\App\Models\Report::$products[$report->group_by]) }}
                                                @elseif($report->entity_type == 'tasks')
                                                    {{ __(\App\Models\Report::$tasks[$report->group_by]) }}
                                                @elseif($report->entity_type == 'calls')
                                                    {{ __(\App\Models\Report::$calls[$report->group_by]) }}
                                                @elseif($report->entity_type == 'campaigns')
                                                    {{ __(\App\Models\Report::$campaigns[$report->group_by]) }}
                                                @elseif($report->entity_type == 'sales_orders')
                                                    {{ __(\App\Models\Report::$sales_orders[$report->group_by]) }}
                                                @else
                                                    {{ __(\App\Models\Report::$users[$report->group_by]) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="budget">
                                            {{ __(\App\Models\Report::$chart_type[$report->chart_type]) }}
                                        </td>
                                        @if (Gate::check('Show Report') || Gate::check('Edit Report') || Gate::check('Delete Report'))
                                            <td>
                                                <div class=" float-end">
                                                    @can('Show Report')
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('report.show', $report->id) }}"
                                                                class="mx-3 btn btn-sm bg-warning align-items-center text-white"
                                                                data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                data-title="{{ __('Report Details') }}">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('Edit Report')
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('report.edit', $report->id) }}"
                                                                class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                                data-title="{{ __('Report Edit') }}"><i
                                                                    class="ti ti-pencil"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Report')
                                                        <div class="action-btn">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['report.destroy', $report->id]]) !!}
                                                            <a href="#!"
                                                                class="mx-3 bg-danger btn btn-sm align-items-center text-white show_confirm"
                                                                data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                </div>
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
        $(document).on('change', 'select[name=entity_type]', function() {
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '{{ route('report.getparent') }}',
                type: 'POST',
                data: {
                    "parent": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data);
                    $('#group_by').empty();
                    {{-- $('#group_by').append('<option value="">{{__('Select Parent')}}</option>'); --}}

                    $.each(data, function(key, value) {
                        $('#group_by').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#group_by').empty();
                    }
                }
            });
        }
    </script>
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jszip.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/buttons.html5.min.js') }}"></script>

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
    </script>
    <script>
        $(document).ready(function() {
            var filename = $('#filename').val();
            setTimeout(function() {
                $('#reportTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excelHtml5',
                        title: filename
                    }, {
                        extend: 'csvHtml5',
                        title: filename
                    }, {
                        extend: 'pdfHtml5',
                        title: filename
                    }, ],

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
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush
