@extends('layouts.admin')

@section('page-title')
        {{__('Notification Template')}}
@endsection

@section('title')
    {{__('Notification Template')}}
@endsection

@section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{__('Notification Template')}}</li>
@endsection

@section('action-btn')

@endsection
@section('content')

    <div class="col-xl-12 mt-4">
        <div class="card">
            <div class="card-header card-body table-border-style">

                <div class="table-responsive">
                    <table id="datatable" class="table datatable align-items-center">
                        <thead>
                            <tr>
                                <th scope="col" class="sort" data-sort="name"> {{__('Name')}}</th>
                                @if (\Auth::user()->type == 'owner')
                                <th class="text-end">{{ __('Action') }}</th>
                            @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notification_templates as $notification_template)
                                <tr>
                                    <td>{{ $notification_template->name }}</td>
                                    <td>
                                        @if (\Auth::user()->type == 'owner')
                                            <div class="text-end">
                                                    <span>
                                                        <div class="action-btn">
                                                            <a href="{{ route('manage.notification.language', [$notification_template->id, \Auth::user()->lang]) }}"
                                                                class="mx-3 btn btn-sm  align-items-center bg-warning"
                                                                data-bs-toggle="tooltip" data-bs-original-title="{{__('View')}}" title="">
                                                                <span class="text-white"><i class="ti ti-eye"></i></span>
                                                            </a>
                                                        </div>
                                                    </span>
                                            </div>
                                        @endif
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

