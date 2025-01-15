{{ Form::model($form_field, array('route' => array('form.field.update', $form->id, $form_field->id), 'method' => 'post','class' => 'needs-validation', 'novalidate')) }}
<div class="row" id="frm_field_data">
    <div class="col-12 form-group">
        {{ Form::label('name', __('Question Name'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::text('name', null, array('class' => 'form-control','required'=>'required','placeholder'=>__('Question Name'))) }}
    </div>
    <div class="col-12 form-group">
        {{ Form::label('type', __('Type'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::select('type', $types,null, array('class' => 'form-control','required'=>'required')) }}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Update'),array('class'=>'btn btn-primary '))}}
    </div>
</div>
{{ Form::close() }}
