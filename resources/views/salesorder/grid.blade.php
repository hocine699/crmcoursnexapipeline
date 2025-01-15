@extends('layouts.admin')
@section('page-title')
    {{__('Sales Order')}}
@endsection
@section('title')
    {{__('Sales Order')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Sales Order')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('salesorder.index') }}" class="btn  btn-icon btn-primary px-2">
        {{__('List View')}}
    </a>
    @can('Create SalesOrder')
        <a href="#" data-size="lg" data-url="{{ route('salesorder.create',['salesorder',0]) }}" data-ajax-popup="true" data-title="{{__('Create New SalesOrder')}}" class="btn  btn-icon btn-primary px-2">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($salesorders as $salesorder)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($salesorder->avatar)) src="{{(!empty($salesorder->avatar))? asset(Storage::url("upload/profile/".$salesorder->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$salesorder->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="{{ route('salesorder.show',$salesorder->id) }}" class="action-item" data-title="{{__('SalesOrders Details')}}">
                                {{ ucfirst($salesorder->name)}}
                            </a>
                        </h5>

                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($salesorder->status == 0)
                                    <span class="badge badge-success">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                @elseif($salesorder->status == 1)
                                    <span class="badge badge-danger">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                @elseif($salesorder->status == 2)
                                    <span class="badge badge-danger">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                @elseif($salesorder->status == 3)
                                    <span class="badge badge-danger">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                @elseif($salesorder->status == 4)
                                    <span class="badge badge-danger">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                @elseif($salesorder->status == 5)
                                    <span class="badge badge-danger">{{ __(\App\Models\SalesOrder::$status[$salesorder->status]) }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    @if(Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show SalesOrder')
                                    <a href="{{ route('salesorder.show',$salesorder->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('SalesOrders Details')}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit SalesOrder')
                                    <a href="{{ route('salesorder.edit',$salesorder->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('SalesOrder Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete SalesOrder')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$salesorder->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id],'id'=>'delete-form-'.$salesorder->id]) !!}
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

