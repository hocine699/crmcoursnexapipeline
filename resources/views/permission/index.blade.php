@extends('layouts.admin')
@section('page-title')
    {{__('Permission')}}
@endsection
@section('title')
    {{__('Permission')}}
        
 
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Permission')}}</li>
@endsection
@section('action-btn')
<div class="action-btn bg-warning ms-2">
    <a href="#" data-size="md" data-url="{{ route('permission.create') }}" data-ajax-popup="true"data-bs-toggle="tooltip" data-title="{{__('Create New Permission')}}" title="{{__('Create')}}"class="btn btn-sm btn-primary btn-icon m-1">
        <i class="ti ti-plus"></i>
    </a>
</div>
    
@endsection
@section('filter')
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
                                <th scope="col" class="sort" data-sort="name">{{__('Permission')}}</th>
                                <th class="text-end">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr>
                                <td class="sorting_1">{{$permission->name}}</td>
                                <td class="action text-end">
                                    <div class="action-btn bg-info ms-2">
                                        <a href="#" data-size="md" data-url="{{ route('permission.edit',$permission->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}"data-ajax-popup="true" data-title="{{__('Edit Permission')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center text-white">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    </div>
                                    <div class="action-btn bg-danger ms-2">
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['permission.destroy', $permission->id]]) !!}
                                    <a href="#!" class="mx-3 btn btn-sm   align-items-center text-white show_confirm" data-bs-toggle="tooltip" title='Delete'>
                                        <i class="ti ti-trash"></i>
                                    </a>
                                    {!! Form::close() !!}
                                </div>
                                
                                    
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
