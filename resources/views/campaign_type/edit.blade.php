{{Form::model($campaignType, array('route' => array('campaign_type.update', $campaignType->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Campaign Type'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Campaign Type'),'required'=>'required'))}}
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
