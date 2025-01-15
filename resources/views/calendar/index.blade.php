@extends('layouts.admin')
@section('page-title')
    {{__('Calendar')}}
@endsection
@section('title')
    {{__('Calendar')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Calendar')}}</li>
@endsection
@section('action-btn')
    @can('Create Call')

            <a href="#" data-size="lg" data-url="{{ route('call.create',['call',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Call')}}" class="btn btn-sm btn-primary me-1 mt-1">
                {{__('Add Call')}}
            </a>
    @endcan
    @can('Create Meeting')

            <a href="#" data-size="lg" data-url="{{ route('meeting.create',['meeting',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Meeting')}}" class="btn btn-sm btn-info me-1 mt-1">
                {{__('Add Meeting')}}
            </a>
    @endcan
    @can('Create Task')

            <a href="#" data-size="lg" data-url="{{ route('task.create',['task',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Task')}}" class="btn btn-sm btn-success me-1 mt-1 ">
                {{__('Add Task')}}
            </a>
    @endcan
    <div class="float-end px-1">
        <select name="calenderdata" data-toggle='select' class="form-select px-2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" style="margin-right: 29px;">
            <option value="{{ route('calendar.index','all') }}" {{ ((Request::segment(2) == 'all' || empty(Request::segment(2))) ? 'selected' : '') }}>{{ __('Show All') }}</option>
            <option value="{{ route('calendar.index','meeting') }}"{{ ((Request::segment(2) == 'meeting') ? 'selected' : '') }}>{{ __('Show Meeting') }}</option>

            <option value="{{ route('calendar.index','call') }}" {{ ((Request::segment(2) == 'call') ? 'selected' : '') }}>{{ __('Show Call') }}</option>
            <option value="{{ route('calendar.index','task') }}" {{ ((Request::segment(2) == 'task') ? 'selected' : '') }}>{{ __('Show Task') }}</option>
        </select>
    </div>

@endsection
@section('filter')

@endsection


@push('script-page')
<script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
<script type="text/javascript">
    @php
    $segment=Request::segment(2);
    @endphp
    $(document).ready(function()
    {
        get_data();
    });

    function get_data()
    {
        var segment="{{$segment}}";
        if(segment=='call'){
            var urls=$("#path_admin").val()+"/call/get_call_data";
        }
        else if(segment=='meeting'){
            var urls=$("#path_admin").val()+"/meeting/get_meeting_data";
        }
        else if(segment=='task'){
            var urls=$("#path_admin").val()+"/task/get_task_data";
        }
        else{
            var urls=$("#path_admin").val()+"/all-data";
        }

        var calender_type=$('#calender_type :selected').val();
        $('#calendar').removeClass('local_calender');
        $('#calendar').removeClass('goggle_calender');

        if(calender_type == undefined){
            calender_type = 'local_calender';
        }

        $('#calendar').addClass(calender_type);
        $.ajax({
            url: urls ,
            method:"POST",
            data: {"_token": "{{ csrf_token() }}",'calender_type':calender_type},
            success: function(data) {
                (function() {
                    var etitle;
                    var etype;
                    var etypeclass;
                    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        buttonText: {
                            timeGridDay: "{{ __('Day') }}",
                            timeGridWeek: "{{ __('Week') }}",
                            dayGridMonth: "{{ __('Month') }}"
                        },
                        slotLabelFormat: {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false,
                                },
                        themeSystem: 'bootstrap',
                        // slotDuration: '00:10:00',
                        navLinks: true,
                        droppable: true,
                        selectable: true,
                        selectMirror: true,
                        editable: true,
                        dayMaxEvents: true,
                        handleWindowResize: true,
                        height: 'auto',
                        timeFormat: 'H(:mm)',
                        events: data,
                        // eventContent: function(event, element, view) {
                        //             var customHtml = event.event._def.extendedProps.html;
                        //             return {
                        //                 html: customHtml
                        //             }
                        //     }

                    });

                    calendar.render();
                })();
            }
        });
    }
</script>
@endpush
@php

$setting = App\Models\Utility::settings();

@endphp


@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 style="width: 150px;">{{ __('Calendar') }}</h5>
                    @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                        <select class="form-control" name="calender_type" id="calender_type" style="float: right;width: 150px;" onchange="get_data()">
                            <option value="goggle_calender">{{__('Google Calender')}}</option>
                            <option value="local_calender" selected="true">{{__('Local Calender')}}</option>
                        </select>
                        @endif
                        <input type="hidden" id="path_admin" value="{{url('/')}}">
                </div>

                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Next events</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @php
                            $date = Carbon\Carbon::now()->format('m');
                            $this_month_Call = App\Models\Call::get();
                            if(\Auth::user()->type == 'owner'){
                                $this_month_Call = App\Models\Call::where('created_by', \Auth::user()->creatorId())->get();

                            }
                            else
                            {
                                $this_month_Call = App\Models\Call::where('user_id', \Auth::user()->id)->get();

                            }
                        @endphp
                        @foreach($this_month_Call as $Call)
                            @php
                                $month =date('m', strtotime($Call->start_date));
                            @endphp
                            @if($date == $month)
                                <li class="list-group-item card mb-3">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="ti ti-phone-call"></i>
                                                </div>
                                                <div class="ms-3" style="color: #3ec9d6">
                                                <h6 class="m-0">{{$Call->name}}</h6>
                                                <small class="text-muted">{{$Call->start_date}} to {{$Call->end_date}}</small>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </li>
                            @endif
                        @endforeach
                        @php
                            $date = Carbon\Carbon::now()->format('m');
                            $this_month_meeting = App\Models\meeting::get();

                            if(\Auth::user()->type == 'owner'){
                                $this_month_meeting = App\Models\meeting::where('created_by', \Auth::user()->creatorId())->get();
                            }
                            else
                            {
                                $this_month_meeting = App\Models\meeting::where('user_id', \Auth::user()->id)->get();
                            }
                        @endphp
                        @foreach($this_month_meeting as $meeting)
                            @php  $month =date('m', strtotime($meeting->start_date));@endphp
                            @if($date == $month)
                                <li class="list-group-item card mb-3">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-info">
                                                    <i class="ti ti-calendar-event"></i>
                                                </div>
                                                <div class="ms-3">
                                                <h6 class="m-0">{{$meeting->name}}</h6>
                                                <small class="text-muted">{{$meeting ->start_date}} to {{$meeting->end_date}}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                        @php
                            $date = Carbon\Carbon::now()->format('m');
                            $this_month_task = App\Models\task::get();
                            if(\Auth::user()->type == 'owner'){
                                $this_month_task = App\Models\task::where('created_by', \Auth::user()->creatorId())->get();
                            }
                            else
                            {
                                $this_month_task = App\Models\task::where('user_id', \Auth::user()->id)->get();
                            }
                        @endphp
                        @foreach($this_month_task as $task)
                            @php
                                $month =date('m', strtotime($task->start_date));
                            @endphp
                            @if($date == $month)
                                <li class="list-group-item card mb-3">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="fa fa-tasks"></i>
                                                </div>
                                                <div class="ms-3">
                                                <h6 class="m-0">{{$task->name}}</h6>
                                                <small class="text-muted">{{$task->start_date}} to {{$task->end_date}}</small>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
@endsection


    @push('script-page')

    <script type="text/javascript">
        $(document).on('change', 'select[name=parent]', function () {

            var parent = $(this).val();

            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '{{route('call.getparent')}}',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log(data);
                    $('#parent_id').empty();
                    {{--$('#parent_id').append('<option value="">{{__('Select Parent')}}</option>');--}}

                    $.each(data, function (key, value) {
                        $('#parent_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#parent_id').empty();
                    }
                }
            });
        }

        $(document).on('change', '#parents', function () {
            console.log('h');
            var parent = $(this).val();
            getparents(parent);
        });

        function getparents(bid) {
            console.log(bid);
            $.ajax({
                url: '{{route('task.getparent')}}',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log(data);
                    $('#parent_id').empty();
                    {{--$('#parent_id').append('<option value="">{{__('Select Parent')}}</option>');--}}

                    $.each(data, function (key, value) {
                        $('#parent_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#parent_id').empty();
                    }
                }
            });
        }


    </script>
@endpush
