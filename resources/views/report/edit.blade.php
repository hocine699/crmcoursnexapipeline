@extends('layouts.admin')
@section('page-title')
    {{__('Report')}}
@endsection
@section('title')

    {{__('Edit Report')}}  {{ '('. $report->name .')' }}
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
        <div class="action-btn  ms-2" style="margin-right :4px;">
            <a href="{{ route('report.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" title="{{__('Previous')}}">
                <i class="ti ti-chevron-left"></i>
            </a>
        </div>
        @else
        <div class="action-btn  ms-2" style="margin-right :4px;">
            <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip" title="{{__('Previous')}}">
                <i class="ti ti-chevron-left"></i>
            </a>
        </div>
        @endif
        @if(!empty($next))
        <div class="action-btn  ms-2" style="margin-right :4px;">
            <a href="{{ route('report.edit',$next) }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" title="{{__('Next')}}">
                <i class="ti ti-chevron-right"></i>
            </a>
        </div>
        @else
        <div class="action-btn  ms-2" style="margin-right :4px;">
            <a href="#" class="btn btn-sm btn-primary btn-icon m-1 disabled" data-bs-toggle="tooltip" title="{{__('Next')}}">
                <i class="ti ti-chevron-right"></i>
            </a>
        </div>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('report.index')}}">{{__('Report')}}</a></li>
    <li class="breadcrumb-item">{{__('Edit')}}</li>
@endsection
@section('content')
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        <a href="#useradd-1" class="list-group-item list-group-item-action">{{ __('Overview') }} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div id="useradd-1" class="card">
                    {{Form::model($report,array('route' => array('report.update', $report->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
                    <div class="card-header">
                        <h5>{{ __('Overview') }}</h5>
                        <small class="text-muted">{{__('Edit about your Report information')}}</small>
                    </div>

                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {{Form::label('name',__('Name'),['class'=>'form-label']) }}
                                        {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                        @error('name')
                                        <span class="invalid-name" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        {{Form::label('chart_type',__('Chart Type'),['class'=>'form-label']) }}
                                        {!!Form::select('chart_type', $chart_type,null,array('class' => 'form-control ','required'=>'required')) !!}
                                        @error('amount')
                                        <span class="invalid-amount" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        {{Form::label('Assign User',__('Assign User'),['class'=>'form-label']) }}
                                        {!! Form::select('user', $user, null,array('class' => 'form-control ','required'=>'required')) !!}
                                        @error('user')
                                        <span class="invalid-user" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end">
                                    {{Form::submit(__('Update'),array('class'=>'btn-submit btn btn-primary'))}}
                                </div>


                            </div>
                        </form>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->
</div>




    {{-- <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#report_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Overview')}}</a>
                                <p class="mb-0 text-sm">{{__('Edit about your Report information')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--Report edit -->
            <div id="report_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Overview')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::model($report,array('route' => array('report.update', $report->id), 'method' => 'PUT')) }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('name',__('Name')) }}
                                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                    @error('name')
                                    <span class="invalid-name" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('chart_type',__('Chart Type')) }}
                                    {!!Form::select('chart_type', $chart_type,null,array('class' => 'form-control ','required'=>'required')) !!}
                                    @error('amount')
                                    <span class="invalid-amount" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                {{Form::label('Assign User',__('Assign User')) }}
                                {!! Form::select('user', $user, null,array('class' => 'form-control ','required'=>'required')) !!}
                                @error('user')
                                <span class="invalid-user" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--Opportunities edit end-->
        </div>
    </div> --}}
@endsection
@push('script-page')

<script>
    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#useradd-sidenav',
        offset: 300
    })
</script>

    <script>

        $(document).on('change', 'select[name=parent]', function () {
            console.log('h');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
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
