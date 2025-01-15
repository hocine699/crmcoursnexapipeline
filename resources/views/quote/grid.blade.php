@extends('layouts.admin')
@section('page-title')
    {{__('Quote')}}
@endsection
@section('title')
    {{__('Quote')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Quote')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('quote.index') }}" class="btn  btn-icon btn-primary px-2">
        {{__('List View')}}
    </a>
    @can('Create Quote')
        <a href="#" data-size="lg" data-url="{{ route('quote.create',['quote',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Quote')}}" class="btn  btn-icon btn-primary px-2">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($quotes as $quote)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($quote->avatar)) src="{{(!empty($quote->avatar))? asset(Storage::url("upload/profile/".$quote->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$quote->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="{{ route('quote.show',$quote->id) }}" class="action-item" data-title="{{__('Quote Details')}}">
                                {{ ucfirst($quote->name)}}
                            </a>
                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($quote->status == 0)
                                    <span class="badge badge-success">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 1)
                                    <span class="badge badge-danger">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 2)
                                    <span class="badge badge-danger">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 3)
                                    <span class="badge badge-danger">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 4)
                                    <span class="badge badge-danger">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                @elseif($quote->status == 5)
                                    <span class="badge badge-danger">{{ __(\App\Models\Quote::$status[$quote->status]) }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    @if(Gate::check('Show Quote') || Gate::check('Edit Quote') || Gate::check('Delete Quote'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Quote')
                                    <a href="{{ route('quote.show',$quote->id) }}"data-size="md" class="action-item" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Quote Details')}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Quote')
                                    <a href="{{ route('quote.edit',$quote->id) }}" data-toggle="tooltip" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-original-title="{{__('   Edit')}}" class="action-item" data-title="{{__('Quote Edit ')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Quote')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$quote->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id],'id'=>'delete-form-'.$quote->id]) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

