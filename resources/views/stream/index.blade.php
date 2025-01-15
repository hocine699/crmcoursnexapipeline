@extends('layouts.admin')
@section('page-title')
    {{__('Stream')}}
@endsection
@section('title')
        {{__('Stream')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Stream')}}</li>
@endsection
@section('action-btn')
@endsection
@section('filter')
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>{{__('Latest comments')}}</h5>
            </div>
            @foreach($streams as $stream)
                @php
                    $remark = json_decode($stream->remark);
                @endphp
            <div class="card-body">
                <div class="row">

                    <div class="col-xl-12">
                        <ul class="list-group team-msg">
                            <li class="list-group-item border-0 d-flex align-items-start mb-2">
                                <div class="avatar me-3">
                                    @php
                                        $profile=\App\Models\Utility::get_file('upload/profile/');
                                    @endphp
                                    <a href="{{(!empty($stream->file_upload))?($profile.$stream->file_upload): $profile .'avatar.png'}}" target="_blank">
                                        <img alt="" class="rounded border-2 border border-primary" src="{{(!empty($stream->file_upload))?($profile.$stream->file_upload):$profile.'avatar.png'}}" >
                                    </a>
                                </div>
                                <div class="d-block d-sm-flex align-items-center right-side">
                                    <div class="d-flex align-items-start flex-column justify-content-center mb-3 mb-sm-0">
                                        <div class="h6 mb-1">{{$remark->user_name}}
                                        </div>
                                        <span class="text-sm lh-140 mb-0">
                                            posted to <a href="#">{{$remark->title}}</a> , {{$stream->log_type}}  <a href="#">{{$remark->stream_comment}}</a>
                                        </span>
                                    </div>
                                    <div class=" ms-2  d-flex align-items-center ">
                                        <small class="float-end ">{{$stream->created_at}}</small>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['stream.destroy', $stream->id]]) !!}
                                    <a href="#!" class="bg-danger mx-3 btn btn-sm  align-items-center text-white show_confirm" data-bs-toggle="tooltip" title='Delete'>
                                        <i class="ti ti-trash"></i>
                                    </a>
                                    {!! Form::close() !!}
                                    </div>
                                </div>

                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
