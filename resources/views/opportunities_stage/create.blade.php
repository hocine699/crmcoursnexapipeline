{{Form::open(array('url'=>'opportunities_stage','method'=>'post','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Opportunities Stage'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Opportunities Stage'),'required'=>'required'))}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Save'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
    </div>
</div>
{{Form::close()}}
