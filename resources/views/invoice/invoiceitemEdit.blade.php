{{Form::model($invoiceItem,array('route' => array('invoice.item.update', $invoiceItem->id), 'method' => 'POST','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('item', __('Item'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::select('item', $items,null, array('class' => 'form-control items','required'=>'required')) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::number('quantity', null, ['class' => 'form-control quantity', 'required' => 'required','placeholder'=>__('Enter Quantity')]) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('price', __('Price'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::number('price', null, ['class' => 'form-control price', 'required' => 'required', 'stage' => '0.01','placeholder'=>__('Enter Price')]) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('discount', __('Discount'), ['class' => 'form-label']) }}
        {{ Form::number('discount', null, ['class' => 'form-control discount','placeholder'=>__('Enter Discount')]) }}
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('tax', __('Tax'),['class'=>'form-label']) }}<x-required></x-required>
        {{ Form::hidden('tax',null, array('class' => 'form-control taxId')) }}
        <div class="row">
            <div class="col-md-12">
                <div class="tax"></div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description'),['class'=>'form-label']) }}<x-required></x-required>
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2','required'=>'required','placeholder'=>__('Enter Description')]) !!}
    </div>
    <div class="col-md-12">
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancle') }}</button>
            {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
        </div>
    </div>
</div>
{{ Form::close() }}
<script>
    $('.items').val({{$invoiceItem->item}}).trigger("change")
</script>
