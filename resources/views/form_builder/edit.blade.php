
    {{ Form::model($formBuilder, array('route' => array('form_builder.update', $formBuilder->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
    <div class="row">
        <div class="col-12 form-group">
            {{ Form::label('name', __('Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, array('class' => 'form-control','required' => 'required','placeholder' => __('Enter Form Name'))) }}
        </div>
        <div class="col-12 form-group">
            {{ Form::label('active', __('Active'),['class'=>'form-label']) }}
            <div class="d-flex radio-check">
                <div class="form-check">
                    <input type="radio" id="on" value="1" name="is_active" class="form-check-input" {{($formBuilder->is_active == 1) ? 'checked' : ''}}>
                    <label class="form-check-label" for="on">{{__('On')}}</label>
                </div>
                <div class="form-check ms-3">
                    <input type="radio" id="off" value="0" name="is_active" class="form-check-input" {{($formBuilder->is_active == 0) ? 'checked' : ''}}>
                    <label class="form-check-label" for="off">{{__('Off')}}</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-secondary"
                data-bs-dismiss="modal">{{__('Cancel')}}</button>
                {{Form::submit(__('Update'),array('class'=>'btn btn-primary '))}}
        </div>
    </div>
    {{ Form::close() }}

