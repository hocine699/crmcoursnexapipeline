@php
    $settings = App\Models\Utility::settings();
    $plansettings = App\Models\Utility::plansettings();

@endphp
{{ Form::open(['route' => ['invoice.storeitem', $invoice->id],'class' => 'needs-validation', 'novalidate']) }}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn btn-sm btn-primary " data-ajax-popup-over="true" data-size="md"
                data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['invoice item']) }}"
                data-toggle="tooltip" title="{{ __('Generate') }}">
                <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
            </a>
        </div>
    @endif
    <div class="form-group col-md-6">
        {{ Form::label('item', __('Item'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::select('item', $items, null, ['class' => 'form-control', 'required' => 'required']) }}
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
        {{ Form::label('tax', __('Tax'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::hidden('tax', null, ['class' => 'form-control taxId']) }}
        <div class="row">
            <div class="col-md-12">
                <div class="tax"></div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12">
        {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '2', 'required' => 'required','placeholder'=>__('Enter Description')]) !!}
    </div>
    <div class="col-md-12">
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancle') }}</button>
            {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
        </div>
    </div>
</div>
{{ Form::close() }}
