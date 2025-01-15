@php
$plansettings = App\Models\Utility::plansettings();
@endphp
{{ Form::open(['url' => 'account', 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['account']) }}"
            data-toggle="tooltip" title="{{ __('Generate') }}">
            <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
        </a>
    </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
        </div>
    </div>
    {{-- <div class="col-6">
        <div class="form-group">
            {{ Form::label('phone', __('Phone'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone'), 'required' => 'required']) }}
        </div>
    </div> --}}
    <x-mobile divClass="col-6" name="phone" label="{{ __('Phone')}}" placeholder="{{__('Phone')}}" required="required"></x-mobile>

    <div class="col-6">
        <div class="form-group">
            {{ Form::label('website', __('Website'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('website', null, ['class' => 'form-control', 'placeholder' => __('Website'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('billingaddress', __('Billing Address'), ['class' => 'form-label']) }}
            <x-required></x-required>
            <div class="action-btn bg-primary ms-2 float-end">
                <a class="mx-3 btn btn-sm d-inline-flex align-items-center text-white " id="billing_data"
                    data-bs-toggle="tooltip" data-placement="top" title="{{ 'Same As Billing Address' }}"><i
                        class="fas fa-copy"></i></a>
                <span class="clearfix"></span>
            </div>
            {{ Form::text('billing_address', null, ['class' => 'form-control', 'placeholder' => __('Billing Address'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('shippingaddress', __('Shipping Address'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('shipping_address', null, ['class' => 'form-control', 'placeholder' => __('Shipping Address'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('billing_city', null, ['class' => 'form-control', 'placeholder' => __('Billing City'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('billing_state', null, ['class' => 'form-control', 'placeholder' => __('Billing State'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('shipping_city', null, ['class' => 'form-control', 'placeholder' => __('Shipping City'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('shipping_state', null, ['class' => 'form-control', 'placeholder' => __('Shipping State'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('billing_country', null, ['class' => 'form-control', 'placeholder' => __('Billing Country'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('billing_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Billing Postal Code'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('shipping_country', null, ['class' => 'form-control', 'placeholder' => __('Shipping Country'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{ Form::text('shipping_postalcode', null, ['class' => 'form-control', 'placeholder' => __('Shipping Postal Code'), 'required' => 'required']) }}
        </div>
    </div>
    <div class="col-12">
        <hr class="mt-2 mb-2">
        <h6>{{ __('Detail') }}</h6>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }} <x-required></x-required>
            {!! Form::select('type', $accountype, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('Industry', __('Industry'), ['class' => 'form-label']) }} <x-required></x-required>
            {!! Form::select('industry', $industry, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('document_id', __('Document'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('document_id', $document_id, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Document') ]) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('Assign User', __('Assign User'), ['class' => 'form-label']) }} <x-required></x-required>
            {!! Form::select('user', $user, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select User')]) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('Description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control','rows' => 2, 'placeholder' => __('Enter Description')]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary ']) }}
</div>
</div>
{{ Form::close() }}
