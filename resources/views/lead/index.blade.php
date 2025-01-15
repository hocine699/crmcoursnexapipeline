@extends('layouts.admin')
@section('page-title')
    {{__('Lead')}}
@endsection
@section('title')
        <div class="page-header-title">
            {{__('Lead')}}
        </div>

@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
@endsection
@section('action-btn')
<a href="{{ route('lead.grid') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" title="{{ __('Kanban View') }}">
    <i class="ti ti-layout-kanban"></i>
</a>

    @can('Create Lead')
        <a href="#" data-url="{{ route('lead.create',['lead',0]) }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{__('Create New Lead')}}"title="{{__('Create')}}" class="btn btn-sm btn-primary btn-icon m-1">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
    <a href="{{route('lead.export')}}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
        <i class="ti ti-file-export text-white"></i>
    </a>

    <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Import')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Import client CSV file')}}" data-url="{{route('lead.file.import')}}">
        <i class="ti ti-file-import text-white"></i>
    </a>
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
                                <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                <th scope="col" class="sort" data-sort="completion">{{__('Account')}}</th>
                                <th scope="col" class="sort" data-sort="budget">{{__('Email')}}</th>
                                <th scope="col" class="sort" data-sort="status">{{__('Phone')}}</th>
                                <th scope="col" class="sort" data-sort="status">{{__('Assign user')}}</th>
                                @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                    <th scope="col" class="text-end">{{__('Action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                            <tr>
                                <td>
                                    <a href="{{ route('lead.edit',$lead->id) }}" data-size="md" data-title="{{__('Lead Details')}}" class="action-item text-primary">
                                        {{ ucfirst($lead->name) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="budget">{{ ucfirst(!empty($lead->accounts)?$lead->accounts->name:'--')}}</span>
                                </td>
                                <td>
                                    <span class="budget">{{ $lead->email }}</a></span>
                                </td>
                                <td>
                                    <span class="budget">
                                        {{ $lead->phone }}
                                    </span>
                                </td>
                                <td>
                                    <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($lead->assign_user)?$lead->assign_user->name:'')}}</span></span>
                                </td>
                                @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                    <td class="text-end">
                                        @can('Show Lead')
                                        <div class="action-btn  me-2">
                                            <a href="#" data-size="md" data-url="{{ route('lead.show',$lead->id) }}" data-bs-toggle="tooltip" title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Lead Details')}}" class="mx-3 bg-warning btn btn-sm align-items-center text-white ">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('Edit Lead')
                                        <div class="action-btn  me-2">
                                            <a href="{{ route('lead.edit',$lead->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white " data-bs-toggle="tooltip"title="{{__('Edit')}}" data-title="{{__('Edit Lead')}}"><i class="ti ti-pencil"></i></a>
                                        </div>
                                        @endcan
                                        @can('Delete Lead')
                                        <div class="action-btn">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id]]) !!}
                                        <a href="#!" class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm" data-bs-toggle="tooltip" title='Delete'>
                                            <i class="ti ti-trash"></i>
                                        </a>
                                        {!! Form::close() !!}
                                    </div>

                                            {{-- <a href="#" class="btn  btn-icon btn-danger px-1  " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$lead->id}}').submit();">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id],'id'=>'delete-form-'.$lead ->id]) !!}
                                        {!! Form::close() !!} --}}
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
