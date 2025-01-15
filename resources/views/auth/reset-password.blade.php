@extends('layouts.auth')
@section('page-title')
    {{ __('Reset Password') }}
@endsection
@section('content')
    <div class="card-body">
        <div class="">
            <h2 class="mb-3 f-w-600">{{ __(' Reset Password') }}
            </h2>
        </div>
        <form method="POST" action="{{ route('password.update'),'class' => 'needs-validation', 'novalidate'}}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="form-group mb-3">
                {{ Form::label('E-Mail Address', __('E-Mail Address'), ['class' => 'form-control-label']) }}
                <input id="email" type="email" class="form-control mt-2 @error('email') is-invalid @enderror"
                    name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" placeholder = "Enter E-Mail Address" autofocus required= required>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                {{ Form::label('Password', __('Password'), ['class' => 'form-control-label']) }}
                <input id="password" type="password" class="form-control mt-2 @error('password') is-invalid @enderror" placeholder = "Enter Password"
                    name="password" required autocomplete="new-password" required= required>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>


            <div class="form-group mb-3">
                {{ Form::label('password-confirm', __('Confirm Password'), ['class' => 'form-control-label']) }}
                <input id="password-confirm" type="password" class="form-control mt-2" placeholder = "Enter Confirm Password" name="password_confirmation" required
                    autocomplete="new-password" required= required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary text-white">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
@endsection
