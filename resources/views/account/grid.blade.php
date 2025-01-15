@extends('layouts.admin')
@section('page-title')
    {{ __('Account') }}
@endsection
@section('title')
    {{ __('Account') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Account')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('account.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip"
        title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>
    @can('Create Account')
       
            <a href="#" data-url="{{ route('account.create', ['account', 0]) }}" data-size="lg" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Account') }}" title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
       
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
<div class="row">
    @foreach ($accounts as $account)
        <div class="col-md-3">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex align-items-center">
                        {{-- <span class="badge bg-primary rounded">
                            @if (!empty($user->getRoleNames()))
                                @foreach ($user->getRoleNames() as $v)
                                    <label class="badge bg-primary p-1 px-2 rounded">{{ $v }}</label>
                                @endforeach
                            @endif
                        </span> --}}
                    </div>
                    <div class="card-header-right">
                        <div class="btn-group card-option">
                            <button type="button" class="btn dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if (Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                @can('Edit Account')
                                <a href="{{ route('account.edit', $account->id) }}" data-size="md" class="dropdown-item"
                                    data-bs-whatever="{{ __('Edit Account') }}" data-bs-toggle="tooltip"
                                    data-title="{{ __('Edit Account') }}"><i class="ti ti-pencil"></i>
                                    {{ __('Edit') }}</a>
                                @endcan
                                @can('Show Account')
                                    <a href="#" data-url="{{ route('account.show', $account->id) }}"
                                        data-ajax-popup="true" data-size="md" class="dropdown-item"
                                        data-bs-whatever="{{ __('Account Details') }}"
                                        data-bs-toggle="tooltip"
                                        data-title="{{ __('Account Details') }}"><i class="ti ti-eye"></i>
                                        {{ __('Details') }}</a>
                                @endcan

                                @can('Delete Account')
                                {!! Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id]]) !!}
                                <a href="#!" class="dropdown-item  show_confirm" data-bs-toggle="tooltip" >
                                    <i class="ti ti-trash"></i>{{ __('Delete') }}
                                </a>
                                {!! Form::close() !!}
                                    {{-- <form method="POST"
                                        action="{{ route('account.destroy', $account->id) }}">
                                        @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button type="submit"
                                            class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm"
                                            data-bs-toggle="tooltip" title='Delete'>
                                            {{ __('Delete') }}
                                        </button>
                                    </form> --}}
                                @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">                                
                    <div class="row g-2 justify-content-between">
                        <div class="col-12">
                            <div class="text-center client-box">
                                <div class="avatar-parent-child">
                                    {{-- <img @if (!empty($employee->avatar)) src="{{ $profile . '/' . $employee->avatar }}" @else
                                        avatar="{{ $employee->name }}" @endif
                                    class="avatar rounded-circle avatar-lg"> --}}

                                    <img alt="user-image" class="img-fluid rounded border-2 border border-primary" @if (!empty($account->avatar)) src="{{ !empty($account->avatar) ? asset(Storage::url('upload/profile/' . $account->avatar)) : asset(url('assets/images/user/avatar-2.png')) }}" @else  avatar="{{ $account->name }}" @endif>

                                </div>
                                <h5 class="h6 mt-2 mb-1 text-primary">{{ ucfirst($account->name) }}</h5>
                                {{-- <h5 class="h6 mt-4 mb-1">
                                    <a href="#" data-size="md" data-url="{{ route('account.show', $account->id) }}" data-ajax-popup="true"
                                        data-title="{{ __('Account Details') }}" class="action-item text-primary">
                                        {{ ucfirst($account->name) }}
                                    </a>
                                </h5> --}}
                                <div class="mb-1"><a href="#" class="text-sm small text-muted">{{ $account->email }}</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @endforeach
    <div class="col-md-3">
                    
        <a href="#" class="btn-addnew-project border border-primary"  data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Account') }}" data-url="{{ route('account.create', ['account', 0]) }}">
             <div class="badge bg-primary proj-add-icon">
                <i class="ti ti-plus"></i>
            </div>
            <h6 class="mt-4 mb-2">{{ __('New Account') }}</h6>
            <p class="text-muted text-center">{{ __('Click here to add New Account') }}</p>
        </a>
     </div>
</div>




   



@endsection
