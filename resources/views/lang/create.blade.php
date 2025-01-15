{{ Form::open(array('route' => array('store.language'))) }}
<div class="form-group">
    {{ Form::label('code', __('Language Code'),['class'=>'form-label']) }}
    {{ Form::text('code', '', array('class' => 'form-control','required'=>'required')) }}
    {{ Form::label('fullName', __('Full Name'),['class'=>'form-label']) }}
    {{ Form::text('fullName', '', array('class' => 'form-control','required'=>'required')) }}
    @error('code')
    <span class="invalid-code" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
    @enderror
    @error('fullName')
    <span class="invalid-fullName" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Create'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
</div>
{{ Form::close() }}

