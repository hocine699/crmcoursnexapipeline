{{ Form::open(['url' => 'payout_store', 'enctype' => 'multipart/form-data']) }}
<div class="row">
    <div class="col-12">
        <div class="form-group ">
            {{ Form::label('requested_amount', __('Request Amount'), ['class' => 'form-label']) }}
            {{ Form::number('requested_amount', null, ['class' => 'form-control', 'placeholder' => __('Enter Request Amount')]) }}
        </div>
    </div>
   
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancle') }}</button>
        {{ Form::submit(__('Send'), ['class' => 'btn btn-primary ']) }}
    </div>
</div>
{{ Form::close() }}

