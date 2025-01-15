@extends('layouts.admin')
@section('page-title')
    {{__('Manage Contract Type')}}
@endsection
@section('title')
    {{__('Manage Contract Type')}}
@endsection

@section('action-btn')
        @can('Create ContractType')
            <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create Contract Type')}}" data-url="{{route('contract_type.create')}}"><i class="ti ti-plus text-white"></i></a>
        @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Contract Type')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable" id="datatable">
                        <thead>
                            <tr>
                                <th>{{__('Contract Type')}}</th>
                                <th width="250px" class="text-end">{{__('Action')}}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contractTypes as $contractType)
                                <tr>
                                    <td>{{ $contractType->name }}</td>
                                    <td class="Action text-end">
                                        <span>
                                        @can('Edit ContractType')
                                        <div class="action-btn me-2">
                                            <a href="#" data-size="md"
                                            data-url="{{ URL::to('contract_type/'.$contractType->id.'/edit') }}"
                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                data-title="{{ __('Edit Contract Type') }}" title="{{ __('Edit') }}"
                                                class="mx-3 btn btn-sm bg-info align-items-center text-white ">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                        </div>
                                                {{-- <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="md" data-url="{{ URL::to('contract_type/'.$contractType->id.'/edit') }}" data-ajax-popup="true" data-title="{{__('Edit Contract Type')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div> --}}
                                            @endcan
                                            @can('Delete ContractType')
                                                <div class="action-btn">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['contract_type.destroy', $contractType->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm bg-danger align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                        <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endif
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
</div>





@endsection
