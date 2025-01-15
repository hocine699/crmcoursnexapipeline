@extends('layouts.admin')
@section('page-title')
    {{ __('Account') }}
@endsection
@section('title')
    {{ __('Account') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Account') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('account.grid') }}" class="btn btn-sm btn-primary btn-icon me-1"
        data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>

    @can('Create Account')
        <a href="#" data-url="{{ route('account.create', ['account', 0]) }}" data-size="lg" data-ajax-popup="true"
            data-bs-toggle="tooltip"data-title="{{ __('Create New Account') }}"title="{{ __('Create') }}"
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
                                    <th scope="col" class="sort" data-sort="Email">{{ __('Email') }}</th>
                                    <th scope="col" class="sort" data-sort="Phone">{{ __('Phone') }}</th>
                                    <th scope="col" class="sort" data-sort="Website">{{ __('Website') }}</th>
                                    <th scope="col" class="sort" data-sort="Assigned User">{{ __('Assigned User') }}
                                    </th>
                                    @if (Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($accounts as $account)
                                    <tr>
                                        <td>
                                            <a href="{{ route('account.edit', $account->id) }}" data-size="md"
                                                data-title="{{ __('Account Details') }}" class="action-item text-primary">
                                                {{ ucfirst($account->name) }}
                                            </a>
                                        </td>
                                        <td class="budget">
                                            {{ $account->email }}
                                        </td>
                                        <td>
                                            <span class="budget"> {{ $account->phone }} </span>
                                        </td>
                                        <td>
                                            <span class="budget">{{ $account->website }}
                                                <a href="{{ $account->website }}" target="_blank"
                                                    class="btn btn-lg btn-sm align-items-center">
                                                    <i class="ti ti-external-link text-success"
                                                        style="font-size: 1.5rem;"></i>
                                                </a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="col-sm-12"><span
                                                    class="text-sm">{{ ucfirst(!empty($account->assign_user) ? $account->assign_user->name : '-') }}</span></span>
                                        </td>

                                        @if (Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                            <td class="text-end">
                                                @can('Show Account')
                                                    <div class="action-btn me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('account.show', $account->id) }}"
                                                            data-ajax-popup="true" data-bs-toggle="tooltip"
                                                            title="{{ __('Details') }}"
                                                            data-title="{{ __('Account Details') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white ">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Account')
                                                    <div class="action-btn me-2">
                                                        <a href="{{ route('account.edit', $account->id) }}"
                                                            data-size="md"class="mx-3 btn btn-sm bg-info align-items-center text-white "
                                                            data-bs-toggle="tooltip"data-title="{{ __('Account Edit') }}"
                                                            title="{{ __('Edit') }}"><i class="ti ti-pencil"></i></a>

                                                    </div>
                                                @endcan
                                                @can('Delete Account')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id]]) !!}
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
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
@endpush
