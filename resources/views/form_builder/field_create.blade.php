{{ Form::open(array('route' => ['form.field.store',$formbuilder->id],'class' => 'needs-validation', 'novalidate')) }}
<div class="row" id="frm_field_data">
    <div class="col-12 form-group">
        {{ Form::label('name', __('Question Name'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::text('name[]', '', array('class' => 'form-control','required'=>'required','placeholder'=>__('Question Name'))) }}
    </div>
    <div class="col-13 form-group">
        {{ Form::label('type', __('Type'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::select('type[]', $types,null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Create'),array('class'=>'btn btn-primary '))}}
    </div>
</div>
{{ Form::close() }}
