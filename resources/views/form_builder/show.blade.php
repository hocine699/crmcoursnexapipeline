@extends('layouts.admin')

@section('page-title')
    {{ $formBuilder->name.__("'s Form Field") }}
@endsection
@section('title')
        {{ $formBuilder->name.__("'s Form Field") }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('form_builder.index')}}">{{__('Form Builder')}}</a></li>
    <li class="breadcrumb-item">{{__('Add Field')}}</li>
@endsection
@section('action-btn')
    @can('Create Form Field')
        <div class="action-btn ms-2">
            <a href="#" data-size='md' data-url="{{ route('form.field.create',$formBuilder->id) }}" data-size="md" data-ajax-popup="true" data-title="{{__('Create New Filed')}}" title="{{__('Create ')}}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan
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
                                <th>{{__('Name')}}</th>
                                <th>{{__('Type')}}</th>
                                @if(\Auth::user()->can('Edit Form Field') || \Auth::user()->can('Delete Form Field'))
                                    <th class="text-end" width="200px">{{__('Action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($formBuilder->form_field->count())
                                @foreach ($formBuilder->form_field as $field)
                                    <tr>
                                        <td>{{ $field->name }}</td>
                                        <td>{{ ucfirst($field->type) }}</td>
                                        @if(\Auth::user()->can('Edit Form Field') || \Auth::user()->can('Delete Form Field'))
                                            <td class="text-end">
                                                @can('Edit Form Field')
                                                    <div class="action-btn me-2">
                                                        <a href="#" class="mx-3 btn btn-sm bg-info align-items-center text-white" data-size="md"data-url="{{ route('form.field.edit',[$formBuilder->id,$field->id]) }}" data-ajax-popup="true" data-title="{{__('Edit Field')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Delete Form Field')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['form.field.destroy', [$formBuilder->id,$field->id]]]) !!}
                                                            <a href="#!" class="mx-3 bg-danger btn btn-sm   align-items-center text-white show_confirm"  data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">{{__('No data available in table')}}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

