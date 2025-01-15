@extends('layouts.admin')
@section('page-title')
    {{ __('User') }}
@endsection
@section('title')
    {{ __('Plan Request') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Plan Request') }}</li>
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table datatable" id="pc-dt-simple">
                                <thead>

                                </thead>
                                <tbody>

                                    @if ($plan_requests->count() > 0)
                                        <tr>
                                            <th>{{ __('User Name') }}</th>
                                            <th>{{ __('Plan Name') }}</th>
                                            <th>{{ __('Max Users') }}</th>
                                            <th>{{ __('Max Account') }}</th>
                                            <th>{{ __('Max Contact') }}</th>
                                            <th>{{ __('Duration') }}</th>
                                            <th>{{ __('expiry date') }}</th>
                                            <th width="150px">{{ __('Action') }}</th>
                                        </tr>
                                        @foreach ($plan_requests as $prequest)
                                            <tr>
                                                <td>
                                                    <div class="font-style font-weight-bold">
                                                        {{ $prequest->user->name }}</div>
                                                </td>
                                                <td>
                                                    <div class="font-style font-weight-bold">{{ $prequest->plan->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="font-weight-bold">
                                                        @if ($prequest->plan->max_user == -1)
                                                            {{ __('Unlimited Users') }}
                                                        @else
                                                            {{ $prequest->plan->max_user }} {{ __('Users') }}
                                                    </div>
                                        @endif
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">
                                                @if ($prequest->plan->max_account == -1)
                                                    {{ __('Unlimited Account') }}
                                                @else
                                                    {{ $prequest->plan->max_account }} {{ __('Account') }}
                                            </div>
                                    @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">
                                            @if ($prequest->plan->max_contact == -1)
                                                {{ __('Unlimited Contact') }}
                                            @else
                                                {{ $prequest->plan->max_contact }} {{ __('Contact') }}
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-style font-weight-bold">
                                            {{ isset($prequest->duration) ? $prequest->duration : '' }}</div>
                                    </td>
                                    <td>{{ \App\Models\Utility::getDateFormated($prequest->created_at, true) }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('response.request', [$prequest->id, 1]) }}"
                                                class="btn btn-success btn-sm me-2" data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('Accept') }}">
                                                <i class="ti ti-check"></i>
                                            </a>
                                            <a href="{{ route('response.request', [$prequest->id, 0]) }}"
                                                class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('Reject') }}">
                                                <i class="ti ti-x"></i>
                                            </a>
                                        </div>
                                    </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <th scope="col" colspan="7">
                                            <h6 class="text-center">{{ __('No Manually Plan Request Found.') }}</h6>
                                        </th>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
