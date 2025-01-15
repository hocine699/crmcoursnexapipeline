@extends('layouts.auth')


@php
    //  $logo=asset(Storage::url('uploads/logo/'));
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $company_logo = Utility::getValByName('company_logo');
    $lang = \App::getLocale('lang');

@endphp

@php
    $languages = App\Models\Utility::languages();
@endphp
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$setting['SITE_RTL'] == 'on'?'rtl':''}}">    --}}

@section('title')
    {{ __('Reset Password') }}
@endsection


@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ $languages[$lang] }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach ($languages as $code => $language)
                    <a href="{{ route('verification.notice', $code) }}" tabindex="0" class="dropdown-item ">
                        <span>{{ ucfirst($language) }}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection


@section('content')
    <div class="col-12">
        <div class="card-body">
            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-success">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @elseif(session('status') == 'verification-link-not-sent')
                <div class="mb-4 font-medium text-sm text-danger">
                    {{ __("Oops! We encountered an issue while attempting to send the email. It seems there's a problem with the mail server's SMTP (Simple Mail Transfer Protocol). Please review the SMTP settings and configuration to resolve the problem.") }}
                </div>
            @endif

            <div class="mb-4 text-sm text-gray-600">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="row">
                    <div class="col-auto">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>
                    </div>

                    <div class="col-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm"> {{ __('Logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
@endsection
