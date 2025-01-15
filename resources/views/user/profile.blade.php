@extends('layouts.admin')
@php
$profile = asset(Storage::url('upload/profile/'));
@endphp
@section('page-title')
    {{ __('Profile') }}
@endsection
@section('title')
    <div class="page-header-title">
        <h4 class="m-b-10">{{ __('Profile') }}</h4>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Profile')}}</li>
@endsection
@section('action-btn')
@endsection
@section('content')

<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        <a href="#useradd-1" class="list-group-item list-group-item-action border-0">{{ __('Personal Info') }} <div class="float-end"></div></a>
                        <a href="#useradd-2" class="list-group-item list-group-item-action border-0">{{ __('Change Password') }} <div class="float-end"></div></a>

                    </div>
                </div>
            </div>
            <div class="col-xl-9">

                <div id="useradd-1" class="card">
                    {{Form::model($userDetail,array('route' => array('update.account'), 'method' => 'post', 'enctype' => "multipart/form-data",'class' => 'needs-validation', 'novalidate'))}}
                    <div class="card-header">
                        <h5>{{ __('Personal Info') }}</h5>
                        <small class="text-muted">{{__('Details about your personal information')}}</small>
                    </div>

                    <div class="card-body">
                        <form>
                            <div class="row">
                                @if (\Auth::user()->type == 'client')
                                @php $client=$userDetail->clientDetail; @endphp
                                <div class="col-md-4">

                                    <div class="form-group">
                                        {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
                                        {{Form::text('name',null,array('class'=>'form-control font-style','required' => 'required'))}}
                                        @error('name')
                                        <span class="invalid-name" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email'),'required' => 'required'))}}
                                    @error('email')
                                    <span class="invalid-email" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    {{Form::label('mobile',__('Mobile'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::number('mobile',$client->mobile,array('class'=>'form-control','required' => 'required'))}}
                                    @error('mobile')
                                    <span class="invalid-mobile" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('address_1',__('Address 1'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::textarea('address_1', $client->address_1, ['class'=>'form-control','rows'=>'4','required' => 'required'])}}
                                    @error('address_1')
                                    <span class="invalid-address_1" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('address_2',__('Address 2'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::textarea('address_2', $client->address_2, ['class'=>'form-control','rows'=>'4','required' => 'required'])}}
                                    @error('address_2')
                                    <span class="invalid-address_2" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    {{Form::label('city',__('City'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::text('city',$client->city,array('class'=>'form-control','required' => 'required'))}}
                                    @error('city')
                                    <span class="invalid-city" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('state',__('State'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::text('state',$client->state,array('class'=>'form-control','required' => 'required'))}}
                                    @error('state')
                                    <span class="invalid-state" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('country',__('Country'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::text('country',$client->country,array('class'=>'form-control','required' => 'required'))}}
                                    @error('country')
                                    <span class="invalid-country" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('zip_code',__('Zip Code'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::text('zip_code',$client->zip_code,array('class'=>'form-control','required' => 'required'))}}
                                    @error('zip_code')
                                    <span class="invalid-zip_code" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
                                        {{Form::text('name',null,array('class'=>'form-control font-style','required' => 'required'))}}
                                        @error('name')
                                        <span class="invalid-name" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email'),'required' => 'required'))}}
                                    @error('email')
                                    <span class="invalid-email" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>
                                <div class="choose-files mt-5">
                                    <label for="file-1">
                                        <div class=" bg-primary company_logo_update"> <i
                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                        </div>
                                        <input type="file"name="profile"id="file-1" class="form-control file mb-3" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])" data-multiple-caption="{count} files selected" multiple/>
                                        <img id="blah" width="25%"  />

                                        {{-- <input type="file" name="profile" id="file-1" class="form-control file" data-multiple-caption="{count} files selected" multiple/> --}}


                                    </label>
                                </div>
                                {{-- <div class="choose-file mt-1">
                                    <label for="logo_blue">
                                        <img alt="Image placeholder" src="{{(!empty($userDetail->avatar))? $profile.'/'.$userDetail->avatar : $profile.'/avatar.png'}}">

                                        <input type="file" name="profile" id="file-1" class="form-control mt-2">
                                    </label>
                                    <p class="edit-favicon"></p>
                                </div> --}}
                               @endif
                                <div class="text-end">
                                    {{Form::submit(__('Save Changes'),array('class'=>'btn-submit btn btn-primary'))}}
                                </div>
                            </div>
                        </form>
                    </div>
                    {{Form::close()}}
                </div>


                <div id="useradd-2" class="card">
                    {{Form::model($userDetail,array('route' => array('update.password',$userDetail->id), 'method' => 'POST','class' => 'needs-validation', 'novalidate'))}}
                    <div class="card-header">
                        <h5>{{__('Change password')}}</h5>
                        <small class="text-muted">{{__('Details about your account password change')}}</small>

                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {{Form::label('current_password',__('Current Password'),['class'=>'form-label'])}}<x-required></x-required>
                                                {{Form::password('current_password',array('class'=>'form-control','placeholder'=>__('Enter Current Password'),'required' => 'required'))}}
                                                @error('current_password')
                                                <span class="invalid-current_password" role="alert">
                                                     <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {{Form::label('new_password',__('New Password'),['class'=>'form-label'])}}<x-required></x-required>
                                                {{Form::password('new_password',array('class'=>'form-control','placeholder'=>__('Enter New Password'),'required' => 'required'))}}
                                                @error('new_password')
                                                <span class="invalid-new_password" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {{Form::label('confirm_password',__('Re-type New Password'),['class'=>'form-label'])}}<x-required></x-required>
                                                {{Form::password('confirm_password',array('class'=>'form-control','placeholder'=>__('Enter Re-type New Password'),'required' => 'required'))}}
                                                @error('confirm_password')
                                                <span class="invalid-confirm_password" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <div class="text-end">
                                            {{Form::submit(__('Update'),array('class'=>'btn-submit btn btn-primary'))}}
                                        </div>

                                    </div>


                            </div>
                        </form>
                    </div>

                    {{ Form::close() }}
                </div>

</div>


    <!-- [ Main Content ] start -->


        </div>
        <!-- [ Main Content ] end -->
    @endsection


    @push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    @endpush
