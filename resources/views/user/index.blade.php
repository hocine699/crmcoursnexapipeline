@extends('layouts.admin')
@section('page-title')
    {{ __('User') }}
@endsection
@if (\Auth::user()->type == 'super admin')
    @section('title')
        {{ __('Manage Companies') }}
    @endsection
@else
    @section('title')
        {{ __('Users') }}
    @endsection
@endif

@if (\Auth::user()->type == 'super admin')
    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
    @endsection
@else
    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item">{{ __('User') }}</li>
    @endsection
@endif
@section('action-btn')
    @if (\Auth::user()->type == 'owner' || \Auth::user()->type == 'Manager' || \Auth::user()->type == 'super admin')
        @can('Manage User')
            <a href="{{ route('user.grid') }}" class="btn btn-sm btn-primary btn-icon m-1"
                data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
                <i class="ti ti-layout-grid text-white"></i>
            </a>
        @endcan
    @endif
    @can('Create User')
        <a href="#" data-url="{{ route('user.create') }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip"
            title="{{ __('Create') }}"data-title="{{ __('Create New User') }}" class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
    @if (\Auth::user()->type == 'owner')
        <a href="{{ route('userlog.index') }}" class="btn btn-sm btn-primary btn-icon m-1"
            data-bs-toggle="tooltip"title="{{ __('User Log') }}">
            <i class="ti ti-user-check"></i>
        </a>
    @endif
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="datatable" class="table align-items-center datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="username">{{ __('Avatar') }}</th>
                                    <th scope="col" class="sort" data-sort="username">{{ __('User Name') }}</th>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="email">{{ __('Email') }}</th>
                                    @if (\Auth::user()->type != 'super admin')
                                        <th scope="col" class="sort" data-sort="title">{{ __('Type') }}</th>
                                        <th scope="col" class="sort" data-sort="isactive">{{ __('Status') }}</th>
                                    @endif
                                    @if (Gate::check('Edit User') || Gate::check('Delete User'))
                                        <th scope="col" class="action-wid">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $profile = \App\Models\Utility::get_file('upload/profile/');
                                @endphp
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <span class="avatar">
                                                <a href="{{ $profile }}{{ !empty($user->avatar) ? $user->avatar : 'avatar.png' }}"
                                                    target="_blank">
                                                    <img class="rounded border-2 border border-primary" width="25%"
                                                        @if ($user->avatar) src="{{ $profile }}{{ !empty($user->avatar) ? $user->avatar : 'avatar.png' }}" @else src="{{ $profile . 'avatar.png' }}" @endif
                                                        alt="{{ $user->name }}">
                                                </a>
                                            </span>
                                        </td>
                                        <td class="budget">
                                            <a href="#" data-size="md" data-url="{{ route('user.show', $user->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('User Details') }}"
                                                class="action-item text-primary">
                                                {{ ucfirst($user->username) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="budget"> {{ ucfirst($user->name) }} </span>
                                        </td>
                                        <td>
                                            <span class="budget">{{ $user->email }}</span>
                                        </td>
                                        @if (\Auth::user()->type != 'super admin')
                                            <td>
                                                {{ ucfirst($user->type) }}
                                            </td>
                                            <td>
                                                @if ($user->is_active == 1)
                                                    <span
                                                        class="badge status_badge bg-success p-2 px-3 ">{{ __('Active') }}</span>
                                                @else
                                                    <span
                                                        class="badge status_badge bg-danger p-2 px-3 ">{{ __('In Active') }}</span>
                                                @endif
                                            </td>
                                        @endif
                                        @if (Gate::check('Edit User') || Gate::check('Delete User'))
                                            <td class="">
                                                @if (\Auth::user()->type == 'super admin')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('login.with.company', $user->id) }}"
                                                            class="mx-3 btn-primary-subtle btn btn-sm  align-items-center"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Login As Company') }}"
                                                            title="{{ __('Login As Company') }}"> <span
                                                                class="text-white"><i class="ti ti-replace"></i></a>
                                                    </div>
                                                    <div class="action-btn me-2">
                                                        <a data-url="{{ route('company.info', $user->id) }}"
                                                            class="mx-3 bg-warning-subtle btn btn-sm  align-items-center"
                                                            data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip"
                                                            data-title="{{ __('Company Info') }}"
                                                            title="{{ __('Company Info') }}"> <span class="text-white"><i
                                                                    class="ti ti-atom"></i></a>
                                                    </div>
                                                    @can('Manage Plan')
                                                        <div class="action-btn me-2">
                                                            <a href="#"
                                                                class="mx-3 bg-brown-subtitle btn btn-sm  align-items-center text-white"
                                                                data-size="md"
                                                                data-url="{{ route('plan.upgrade', $user->id) }}"
                                                                data-ajax-popup="true" data-bs-toggle="tooltip"
                                                                data-title="{{ __('Upgrade Plan') }}"
                                                                title="{{ __('Upgrade Plan') }}">
                                                                <i class="ti ti-trophy"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                @endif
                                                @if ($user->is_disable == 0)
                                                    <div class="text-center">
                                                        <i class="ti ti-lock"></i>
                                                    </div>
                                                @else
                                                    <div class="action-btn me-2">
                                                        <a href="#"
                                                            class="mx-3 bg-light-blue-subtitle btn btn-sm  align-items-center text-white"
                                                            data-size="md"
                                                            data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"
                                                            data-ajax-popup="true" title="{{ __('Reset Password') }}"
                                                            data-bs-toggle="tooltip"
                                                            data-title="{{ __('Reset Password') }}">
                                                            <i class="ti ti-key"></i>
                                                        </a>
                                                    </div>
                                                    @if ($user->is_enable_login == 1)
                                                        <div class="action-btn  me-2">
                                                            <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                                class="mx-3 bg-blue-subtitle btn btn-sm  align-items-center"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Login Disable') }}"> <span
                                                                    class="text-white"><i class="ti ti-road-sign"></i></a>
                                                        </div>
                                                    @elseif ($user->is_enable_login == 0 && $user->password == null)
                                                        <div class="action-btn me-2">
                                                            <a href="#"
                                                                data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}"
                                                                data-ajax-popup="true" data-size="md"
                                                                class="mx-3 bg-light-green-subtitle btn btn-sm  align-items-center login_enable"
                                                                data-titgle="{{ __('New Password') }}"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('New Password') }}"> <span
                                                                    class="text-white"><i class="ti ti-road-sign"></i></a>
                                                        </div>
                                                    @else
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('users.login', \Crypt::encrypt($user->id)) }}"
                                                                class="mx-3 bg-success btn btn-sm  align-items-center login_enable"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Login Enable') }}"> <span
                                                                    class="text-white"> <i class="ti ti-road-sign"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @can('Show User')
                                                        <div class="action-btn me-2">
                                                            <a href="#" data-size="md"
                                                                data-url="{{ route('user.show', $user->id) }}"
                                                                data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                                data-ajax-popup="true" data-title="{{ __('User Details') }}"
                                                                class="mx-3 bg-warning btn btn-sm  align-items-center text-white">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('Edit User')
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('user.edit', $user->id) }}"
                                                                class="mx-3 bg-info btn btn-sm  align-items-center text-white"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ __('Edit') }}"data-title="{{ __('Edit User') }}"><i
                                                                    class="ti ti-pencil"></i></a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete User')
                                                        <div class="action-btn">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id]]) !!}
                                                            <a href="#!"
                                                                class="mx-3 bg-danger btn btn-sm align-items-center text-white show_confirm"
                                                                data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    @endcan
                                                @endif
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
