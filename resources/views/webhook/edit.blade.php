{{ Form::model($webhook, ['route' => ['webhook.update', $webhook->id], 'method' => 'PUT']) }}
<div class="form-group">
    {{ Form::label('module', __('Module'), ['class' => 'form-label']) }}
    {{ Form::select('module', $module, null, ['class' => 'form-control', 'required' => 'required']) }}
</div>
<div class="form-group">
    {{ Form::label('url', __('URL'), ['class' => 'form-label']) }}
    {{ Form::text('url', null, ['class' => 'form-control', 'placeholder' => __('Enter Url'), 'required' => 'required']) }}
</div>
<div class="form-group">
    {{ Form::label('method', __('Method'), ['class' => 'form-label']) }}
    {{ Form::select('method', $method, null, ['class' => 'form-control', 'required' => 'required']) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-primary ']) }}
</div>
</div>
{{ Form::close() }}
