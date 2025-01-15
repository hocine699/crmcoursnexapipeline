@extends('layouts.admin')
@section('page-title')
    {{__('Invoice')}}
@endsection
@section('title')
    {{__('Invoice')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Invoice')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Invoice')
        <a href="#" data-size="lg" data-url="{{ route('invoice.create',['invoice',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Invoice')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($invoices as $invoice)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($invoice->avatar)) src="{{(!empty($invoice->avatar))? asset(Storage::url("upload/profile/".$invoice->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$invoice->name}}" @endif>
                        </div>

                        <h5 class="h6 mt-4 mb-1">
                            <a href="{{ route('invoice.show',$invoice->id) }}" class="action-item" data-title="{{__('Invoice Details')}}">
                                {{ $invoice->name}}
                            </a>
                        </h5>

                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($invoice->status == 0)
                                    <span class="badge badge-success">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                @elseif($invoice->status == 1)
                                    <span class="badge badge-danger">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                @elseif($invoice->status == 2)
                                    <span class="badge badge-danger">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                @elseif($invoice->status == 3)
                                    <span class="badge badge-danger">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                @elseif($invoice->status == 4)
                                    <span class="badge badge-danger">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                @elseif($invoice->status == 5)
                                    <span class="badge badge-danger">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>

                                @endif
                            </a>
                        </div>
                    </div>
                    @if(Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Invoice')
                                    <a href="{{ route('invoice.show',$invoice->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" class="action-item" data-title="{{__('Invoice Details')}}">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Invoice')
                                <a href="{{ route('invoice.edit',$invoice->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Invoice Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Invoice')
                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$invoice->id}}').submit();">
                                    <i class="fas fa-trash"></i>
                                </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]) !!}
                                {!! Form::close() !!}
                                @endcan
                            </div>
                        </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

