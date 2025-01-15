{{Form::open(array('url'=>'shipping_provider','method'=>'post','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Shipping Provider'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Provider Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('website',__('Website'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter website'),'required'=>'required'))}}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Save'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
    </div>
</div>
{{Form::close()}}
