{{Form::model($user,array('route' => array('user.password.update', $user->id), 'method' => 'post','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('password', __('Password'),['class'=>'form-label']) }}<x-required></x-required>
       <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{__('Enter Password')}}">
       @error('password')
       <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
           </span>
       @enderror
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('password_confirmation', __('Confirm Password'),['class'=>'form-label']) }}<x-required></x-required>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Enter Confirm Password')}}">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>
{{ Form::close() }}
