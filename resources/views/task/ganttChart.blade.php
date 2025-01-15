@extends('layouts.admin')
@php
    $profile=asset(Storage::url('uploads/avatar'));
@endphp
@push('css-page')
    <link rel="stylesheet" href="{{asset('css/frappe-gantt.css')}}"/>
@endpush
@push('script-page')
    <script>
        const month_names = {
            "en": [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ],
            "en": [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ],
        };
    </script>
    <script src="{{asset('js/frappe-gantt.js')}}"></script>
    <script>

        var tasks = JSON.parse('{!! addslashes(json_encode($ganttTasks)) !!}');

        var gantt = new Gantt('#gantt', tasks, {

            custom_popup_html: function (task) {
                var status_class = 'success';
                if (task.custom_class == 'medium') {
                    status_class = 'info'
                } else if (task.custom_class == 'high') {
                    status_class = 'danger'
                }
                return `<div class="details-container">
                                <div class="title">${task.name} <span class="badge badge-${status_class} float-right">${task.extra.priority}</span></div>
                                <div class="subtitle">

                                    <b>{{ __('Stage')}} : </b> ${task.extra.stage}<br>
                                    <b>{{ __('Duration')}} : </b> ${task.extra.duration}<br>
                                    <b>{{ __('Description')}} : </b> ${task.extra.description}

                                </div>
                            </div>
                          `;
            },
            on_click: function (task) {
            },
            on_date_change: function (task, start, end) {
                task_id = task.id;
                start = moment(start);
                end = moment(end);
                $.ajax({
                    url: "{{route('task.gantt.chart.post')}}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        start: start.format('YYYY-MM-DD HH:mm:ss'),
                        end: end.format('YYYY-MM-DD HH:mm:ss'),
                        task_id: task_id,
                    },
                    type: 'POST',
                    success: function (data) {

                    },
                    error: function (data) {
                        show_toastr('Error', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                });
            },
        });
        gantt.change_view_mode('{{$duration}}');
    </script>
@endpush

@section('page-title')
    {{__('Task Gantt Chart')}}
@endsection

@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{__('Task Gantt Chart')}}</h4>
    </div>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
        <li class="breadcrumb-item "><a href="{{ route('task.index') }}">{{__('Task')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{__('Gantt Chart')}}</li>
    </ul>
@endsection

@section('action-btn')
    <a href="{{ route('task.index') }}" class="btn btn-sm btn-primary btn-icon me-1" title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>
    <a href="{{ route('task.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden ">
                <div class="card-header actions-toolbar">
                    <div class="row justify-content-between align-items-center">
                        <div class="col">
                            <h6 class="d-inline-block mb-0">{{__('Gantt Chart')}}</h6>
                        </div>
                        <div class="col-sm-6 text-end">
                            <a href="{{route('task.gantt.chart','Quarter Day')}}" class="btn btn-xs btn-primary gantt-chart-mode  @if($duration == 'Quarter Day')active @endif" data-value="Quarter Day">{{__('Quarter Day')}}</a>
                            <a href="{{route('task.gantt.chart','Half Day')}}" class="btn btn-xs btn-primary gantt-chart-mode @if($duration == 'Half Day')active @endif" data-value="Half Day">{{__('Half Day')}}</a>
                            <a href="{{route('task.gantt.chart','Day')}}" class="btn btn-xs btn-primary gantt-chart-mode @if($duration == 'Day')active @endif" data-value="Day">{{__('Day')}}</a>
                            <a href="{{route('task.gantt.chart','Week')}}" class="btn btn-xs btn-primary gantt-chart-mode @if($duration == 'Week')active @endif" data-value="Week">{{__('Week')}}</a>
                            <a href="{{route('task.gantt.chart','Month')}}" class="btn btn-xs btn-primary gantt-chart-mode @if($duration == 'Month')active @endif" data-value="Month">{{__('Month')}}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <svg id="gantt"></svg>
                </div>
            </div>
        </div>
    </div>
@endsection



