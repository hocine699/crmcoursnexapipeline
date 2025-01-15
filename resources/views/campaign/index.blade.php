@extends('layouts.admin')
@section('page-title')
    {{ __('Campaign') }}
@endsection
@section('title')
    {{ __('Campaign') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Campaign') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('campaign.grid') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>

    @can('Create Campaign')
        <a href="#" data-size="lg" data-url="{{ route('campaign.create', ['campaign', 0]) }}" data-bs-toggle="tooltip"
            data-ajax-popup="true" data-title="{{ __('Create New Campaign') }}" title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="datatable" class="table datatable align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Type') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('Budget') }}
                                    </th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Assigned User') }}
                                    </th>
                                    @if (Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campaigns as $campaign)
                                    <tr>
                                        <td>
                                            <a href="{{ route('campaign.edit', $campaign->id) }}" data-size="md"
                                                data-title="{{ __('Campaign Details') }}" class="action-item text-primary">
                                                {{ ucfirst($campaign->name) }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ ucfirst(!empty($campaign->types->name) ? $campaign->types->name : '-') }}
                                        </td>
                                        <td>
                                            @if ($campaign->status == 0)
                                                <span class="badge bg-warning p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                                            @elseif($campaign->status == 1)
                                                <span class="badge bg-success p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                                            @elseif($campaign->status == 2)
                                                <span class="badge bg-danger p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                                            @elseif($campaign->status == 3)
                                                <span class="badge bg-info p-2 px-3"
                                                    style="width: 73px;">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="budget">{{ $campaign->budget }}</span>
                                        </td>
                                        <td>
                                            <span class="col-sm-12"><span
                                                    class="text-sm">{{ ucfirst(!empty($campaign->assign_user) ? $campaign->assign_user->name : '-') }}</span></span>
                                        </td>

                                        @if (Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign'))
                                            <td class="text-end">
                                                @can('Show Campaign')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('campaign.show', $campaign->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-ajax-popup="true" data-title="{{ __('Campaign Details') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Campaign')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('campaign.edit', $campaign->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                            class="mx-3 btn btn-sm bg-info align-items-center text-white"
                                                            data-title="{{ __('Edit Campaign') }}"><i
                                                                class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Campaign')
                                                    <div class="action-btn ">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['campaign.destroy', $campaign->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm  bg-danger align-items-center text-white show_confirm"
                                                            data-bs-toggle="tooltip" title='Delete'>
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
