{{Form::open(array('url'=>'target_list','method'=>'post','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Target List'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Target List'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            {{Form::label('description',__('Description'),['class'=>'form-label']) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>'3','placeholder'=>__('Enter Description')))}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Save'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
    </div>
</div>
{{Form::close()}}
