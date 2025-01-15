{{Form::model($shippingProvider, array('route' => array('shipping_provider.update', $shippingProvider->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Shipping Provider'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Provider Name'),'required'=>'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('website',__('Website'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website'),'required'=>'required'))}}
            @error('website')
            <span class="invalid-website" role="alert">
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
