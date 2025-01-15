{{ Form::open(array('url' => 'contract_type','class' => 'needs-validation', 'novalidate')) }}

    <div class="row">
        <div class="form-group col-12">
            {{ Form::label('name', __('Contract Type Name'),['class'=>'col-form-label']) }}<x-required></x-required>
            {{ Form::text('name', '', array('class' => 'form-control','required'=>'required','placeholder'=>'Enter Contract Type Name')) }}
        </div>
    </div>

<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>

    {{ Form::close() }}

