@extends('layouts.admin')
@php
    $dir = asset(Storage::url('uploads/plan'));
    $settings = Utility::settings();
@endphp
@push('script-page')
@endpush
@section('page-title')
    {{ __('Plan') }}
@endsection
@section('title')
    {{ __('Plan') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Plan') }}</li>
@endsection
@section('action-btn')
    @if (\Auth::user()->type == 'super admin')
        <div class="action-btn ms-2">
            <a href="#" data-url="{{ route('plan.create') }}" data-size="md" data-ajax-popup="true"
                data-bs-toggle="tooltip" data-title="{{ __('Create New Plan') }}"title="{{ __('Create') }}"
                class="btn btn-sm btn-primary btn-icon m-1">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endif
@endsection
@section('content')
    <div class="row row-gap">
        @foreach ($plans as $plan)
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6 d-flex">
                <div class="card price-card price-1 wow animate__fadeInUp mb-0" data-wow-delay="0.2s"
                    style="
               visibility: visible;
               animation-delay: 0.2s;
               animation-name: fadeInUp;
               ">
                    <div class="card-body {{ !empty(\Auth::user()->type != 'super admin') ? 'plan-box' : '' }}"
                        >

                        <span class="price-badge bg-primary">{{ $plan->name }}</span>
                        <div class="d-flex justify-content-end align-items-center mt-2">
                            <div class="col-9 text-start">
                                @if (\Auth::user()->type == 'super admin' && $plan->price > 0)
                                    <div class="form-check form-switch custom-switch-v1">
                                        <input type="checkbox" data-id="{{ $plan->id }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ __('Enable/Disable') }}"
                                            class="form-check-input input-primary is_active"
                                            {{ $plan->status == 1 ? 'checked' : '' }}>
                                    </div>
                                @endif
                            </div>

                            @if (\Auth::user()->type == 'super admin')
                                <div class="action-btn me-1">
                                    <a title="Edit Plan" data-size="md" href="#"
                                        class="btn btn-sm bg-info align-items-center text-white"
                                        data-url="{{ route('plan.edit', $plan->id) }}" data-ajax-popup="true"
                                        data-title="{{ __('Edit Plan') }}" data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ __('Edit') }}">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </div>

                                @if ($plan->id != 1)
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['plan.destroy', $plan->id],
                                        'id' => 'delete-form-' . $plan->id,
                                    ]) !!}
                                    <div class="action-btn ms-2">
                                        <a href="#!" class="bg-danger btn btn-sm align-items-center text-white show_confirm"
                                            data-bs-toggle="tooltip" title='Delete'>
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                    {!! Form::close() !!}
                                @endif
                            @endif
                            @if (\Auth::user()->type == 'owner' && \Auth::user()->plan == $plan->id)
                                <div class="ms-2 d-flex align-items-center">
                                    <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                    <span class="ms-2">{{ __('Active') }}</span>
                                </div>
                            @endif
                        </div>

                        <h1 class="mb-4 f-w-600 ">
                            {{ isset($settings['site_currency_symbol']) ? $settings['site_currency_symbol'] : '$' }}{{ $plan->price }}<small
                                class="text-sm">/{{ env('CURRENCY_SYMBOL') . __(\App\Models\Plan::$arrDuration[$plan->duration]) }}</small>

                        </h1>
                        <p class="mb-0">
                            {{ __('Free Trial Days : ') . __($plan->trial_days ? $plan->trial_days : 0) }}<br />
                        </p>
                        <p class="my-4">{{ $plan->description }}</p>

                        <ul class="list-unstyled">
                            <li> <span class="theme-avtar"><i
                                        class="text-primary ti ti-circle-plus"></i></span>{{ $plan->max_user == -1 ? __('Unlimited') : $plan->max_user }}
                                {{ __('Users') }}</li>
                            <li><span class="theme-avtar"><i
                                        class="text-primary ti ti-circle-plus"></i></span>{{ $plan->max_account == -1 ? __('Unlimited') : $plan->max_account }}
                                {{ __('Account') }}</li>
                            <li><span class="theme-avtar"><i
                                        class="text-primary ti ti-circle-plus"></i></span>{{ $plan->max_contact == -1 ? __('Unlimited') : $plan->max_contact }}
                                {{ __('Contact') }}</li>
                            <li class="white-sapce-nowrap"><span class="theme-avtar"><i
                                        class="text-primary ti ti-circle-plus"></i></span>{{ $plan->storage_limit == -1 ? __('Unlimited') : $plan->storage_limit }}
                                {{ __('MB') }} {{ __('Storage') }}</li>
                            <li class="white-sapce-nowrap"><span class="theme-avtar"><i
                                        class="text-primary ti ti-circle-plus"></i></span>{{ $plan->enable_chatgpt == 'on' ? __('Enable') : __('Disable') }}
                                {{ __('Chat GPT') }}</li>

                        </ul>
                        <br>

                        <div class="row justify-content-center align-items-center">
                            @if ($plan->id != \Auth::user()->plan && $plan->price != 0 && \Auth::user()->type != 'super admin')
                                @if ($plan->trial == 1 && empty(\Auth::user()->trial_expire_date) && \Auth::user()->type != 'super admin')
                                    <a href="{{ route('plan.trial', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                        class="btn btn-lg btn-primary btn-icon m-1 col-5">{{ __('Start Free Trial') }}</a>
                                @endif
                                <a href="{{ route('plan.payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                    class="btn btn-lg btn-primary btn-icon m-1 align-items-center col-4">{{ __('Subscribe') }}</a>
                            @elseif($plan->price <= 0)
                            @endif
                            @if (\Auth::user()->type != 'super admin' && \Auth::user()->plan != $plan->id)
                                @if ($plan->id != 1)
                                    @if (\Auth::user()->requested_plan != $plan->id)
                                        <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                            class="btn btn-lg btn-primary btn-icon m-1 col-2"
                                            data-title="{{ __('Send Request') }}" data-bs-toggle="tooltip"
                                            title="{{ __('Send Request') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-corner-up-right"></i></span>
                                        </a>
                                    @else
                                        <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                            class="btn btn-lg btn-danger btn-icon m-1 col-2"
                                            data-title="{{ __('Cancle Request') }}" data-bs-toggle="tooltip"
                                            title="{{ __('Cancle Request') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-x"></i></span>
                                        </a>
                                    @endif
                                @endif
                            @endif
                        </div>

                        @if (\Auth::user()->type == 'owner' && \Auth::user()->plan == $plan->id)
                            @if (empty(\Auth::user()->plan_expire_date) && empty(Auth::user()->trial_expire_date))
                                <p class="mb-0">{{ __('Lifetime') }}</p>
                            @elseif (\Auth::user()->plan_expire_date > \Auth::user()->trial_expire_date)
                                <p class="mb-0">
                                    {{ __('Plan Expires on ') }}
                                    {{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}
                                </p>
                            @else
                                <p class="mb-0">
                                    {{ __('Trial Expires on ') }}
                                    {{ !empty(\Auth::user()->trial_expire_date) ? date('d M Y', strtotime(\Auth::user()->trial_expire_date)) : date('Y-m-d') }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '#trial', function() {
            if ($(this).is(':checked')) {
                $('.plan_div').removeClass('d-none');
                $('#trial').attr("required", true);

            } else {
                $('.plan_div').addClass('d-none');
                $('#trial').removeAttr("required");
            }
        });

        $('.input-primary').on('change', function() {
            var planId = $(this).data('id');
            var isChecked = $(this).prop('checked');


            $.ajax({
                type: 'POST',
                url: '{{ route('update.plan.status') }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'plan_id': planId
                },
                success: function(data) {
                    if (data.success) {
                        show_toastr('Success', data.success, 'success');
                    } else {
                        show_toastr('Error', data.error, 'error');

                    }


                },
                error: function(error) {

                    if (error.status === 404) {
                        $(this).prop('checked', !isChecked);
                    }
                }
            });
        });
    </script>
@endpush
