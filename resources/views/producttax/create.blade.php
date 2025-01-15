{{Form::open(array('url'=>'product_tax','method'=>'post','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('tax_name',__('Tax Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('tax_name',null,array('class'=>'form-control','placeholder'=>__('Enter Product Brand'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('rate',__('Rate'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('rate',null,array('class'=>'form-control','placeholder'=>__('Enter Product Brand'),'required'=>'required'))}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancle') }}</button>
        {{Form::submit(__('Save'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{Form::close()}}
