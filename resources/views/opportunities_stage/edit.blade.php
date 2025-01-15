{{Form::model($opportunitiesStage, array('route' => array('opportunities_stage.update', $opportunitiesStage->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Opportunities Stage'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Opportunities Stage'),'required'=>'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
</div>
{{Form::close()}}
