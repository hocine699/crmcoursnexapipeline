@extends('layouts.admin')
@section('page-title')
    {{ __('Cases') }}
@endsection
@section('title')
    {{ __('Cases') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Cases') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('commoncases.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>
    @can('Create CommonCase')
        <a href="#" data-size="lg" data-url="{{ route('commoncases.create', ['commoncases', 0]) }}" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-title="{{ __('Create New Case') }}"title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
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
                        <table class="table datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Number') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Account') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Priority') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Assigned User') }}</th>
                                    @if (Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cases as $case)
                                    <tr>
                                        <td>
                                            <a href="{{ route('commoncases.edit', $case->id) }}"     data-size="md"
                                                data-title="{{ __('Cases Details') }}" class="text-primary">
                                                {{ $case->name }}
                                            </a>
                                        </td>
                                        <td>{{ $case->number }}</td>
                                        <td>
                                            {{ !empty($case->accounts->name) ? $case->accounts->name : '--' }}
                                        </td>
                                        <td>
                                            @if ($case->status == 0)
                                                <span class="badge bg-success p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$status[$case->status]) }}</span>
                                            @elseif($case->status == 1)
                                                <span class="badge bg-info p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$status[$case->status]) }}</span>
                                            @elseif($case->status == 2)
                                                <span class="badge bg-warning p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$status[$case->status]) }}</span>
                                            @elseif($case->status == 3)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$status[$case->status]) }}</span>
                                            @elseif($case->status == 4)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$status[$case->status]) }}</span>
                                            @elseif($case->status == 5)
                                                <span class="badge bg-warning p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$status[$case->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($case->priority == 0)
                                                <span class="badge bg-primary p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$priority[$case->priority]) }}</span>
                                            @elseif($case->priority == 1)
                                                <span class="badge bg-info p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$priority[$case->priority]) }}</span>
                                            @elseif($case->priority == 2)
                                                <span class="badge bg-warning p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$priority[$case->priority]) }}</span>
                                            @elseif($case->priority == 3)
                                                <span class="badge bg-danger  p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\CommonCase::$priority[$case->priority]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ !empty($case->assign_user) ? $case->assign_user->name : '' }}
                                        </td>
                                        @if (Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                                            <td class="text-end">

                                                @can('Show CommonCase')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('commoncases.show', $case->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Cases Details') }}"title="{{ __('Details') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white ">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit CommonCase')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('commoncases.edit', $case->id) }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white "
                                                            data-bs-toggle="tooltip"
                                                            data-title="{{ __('Edit Cases') }}"title="{{ __('Edit') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete CommonCase')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $case->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm bg-danger  align-items-center text-white show_confirm"
                                                            data-bs-toggle="tooltip" title='Delete'>
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}


                                                        {{-- <a href="#" class="btn  btn-icon btn-danger px-1 " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$case->id}}').submit();">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $case->id],'id'=>'delete-form-'.$case ->id]) !!}
                                            {!! Form::close() !!} --}}
                                                    @endcan
                                                </div>
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
