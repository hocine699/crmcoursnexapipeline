@extends('layouts.admin')
@section('page-title')
    {{__('Role')}}
@endsection
@section('title')
        {{ __('Role')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Role')}}</li>
@endsection
@section('action-btn')
    @can('Create Role')
        <div class="action-btn bg-warning ms-2">
            <a href="#" data-url="{{ route('role.create') }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip"title="{{__(' Create')}}"data-title="{{__('Create New Role')}}" class="btn btn-sm btn-primary btn-icon m-1">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable" id="datatable1">
                        <thead>
                            <tr>
                                <th width="150">{{__('Role')}} </th>
                                <th>{{__('Permissions')}} </th>
                                @if(Gate::check('Edit Role') || Gate::check('Delete Role'))
                                    <th width="150" class="text-end">{{__('Action')}} </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($roles as $role)

                                <tr>
                                    <td width="150">{{ $role->name }}</td>
                                    <td class="Permission mt-10">
                                        <div class="badges">
                                            {{-- @for($j=0;$j<count($role->permissions()->pluck('name'));$j++)
                                                <span class="badge bg-primary p-1 px-2 ">{{$role->permissions()->pluck('name')[$j]}}</span>
                                            @endfor --}}
                                            @foreach ($role->permissions as $permission)
                                            <span class="badge p-2 m-1 px-3 bg-primary">
                                                <a href="#" class="text-white">{{ $permission->name }}</a>
                                            </span>
                                        @endforeach
                                        </div>
                                    </td>
                                    @if(Gate::check('Edit Role') || Gate::check('Delete Role'))
                                        <td class="text-end">
                                            @can('Edit Role')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 bg-info btn btn-sm  align-items-center text-white" data-url="{{ route('role.edit',$role->id) }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Role')}}">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                </div>
                                            @endcan

                                            @can('Delete Role')
                                                <div class="action-btn ">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['role.destroy', $role->id]]) !!}
                                                        <a href="#!" class="mx-3 bg-danger  btn btn-sm  align-items-center text-white show_confirm" data-bs-toggle="tooltip" title='Delete'>
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </td>
                                    @endif
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
