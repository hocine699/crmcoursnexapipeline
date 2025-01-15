{{Form::model($productTax, array('route' => array('product_tax.update', $productTax->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('tax_name',__('Tax Name'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('tax_name',null,array('class'=>'form-control','placeholder'=>__('Enter Tax Name'),'required'=>'required'))}}
            @error('tax_name')
            <span class="invalid-tax_name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('rate',__('Rate'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('rate',null,array('class'=>'form-control','placeholder'=>__('Enter Tax Rate'),'required'=>'required'))}}
            @error('rate')
            <span class="invalid-rate" role="alert">
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
