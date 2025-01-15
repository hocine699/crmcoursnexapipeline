@extends('layouts.admin')
@section('page-title')
    {{__('Manage Payment Method')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Manage Payment Method')}}</h5>
    </div>
@endsection
@section('breadcrumb')
     <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Manage Payment Method')}}</li>
@endsection
@section('action-btn')
    @can('Create Payment')
    <div class="action-btn bg-warning ms-2">
        <a href="#" data-url="{{ route('payments.create') }}" data-size="md" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{__('Create Payment Method')}}"title="{{__('Create')}}" class="btn btn-sm btn-primary btn-icon m-1">
            <i class="ti ti-plus"></i>
        </a>
    </div>

    @endcan
@endsection
@section('content')
<div class="row">
    <div class="card">
        <div class="card-body table-border-style">
            <div class="table-responsive">
                <table class="table datatable" id="datatable">
                    <thead>
                        <tr>
                            <th>{{__('Payment Method')}}</th>
                            <th width="250px">{{__('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr data-id="{{$payment->id}}">
                                <td>
                                    <a>{{$payment->name}}</a>
                                </td>
                                <td class="Action">
                                    <span>
                                        @can('Edit Payment')
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('payments.edit',$payment->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center text-white" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Payment Method')}}"><i class="ti ti-pencil"></i></a>
                                        </div>
                                        @endcan
                                        @can('Delete Payment')
                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['payments.destroy', $payment->id]]) !!}
                                           <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm" data-bs-toggle="tooltip" title='Delete'>
                                               <i class="ti ti-trash"></i>
                                           </a>
                                           {!! Form::close() !!}
                                       </div>

                                        @endcan
                                    </span>
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
