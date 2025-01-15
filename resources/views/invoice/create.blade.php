@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
{{Form::open(array('url'=>'invoice','method'=>'post','enctype'=>'multipart/form-data','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['invoice']) }}"
            data-toggle="tooltip" title="{{ __('Generate') }}">
            <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
        </a>
    </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('name',null,array('id'=>'name','class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    @if($type == 'salesorder')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('salesorder',__('Sales Orders'),['class'=>'form-label']) }}
                {!!Form::select('salesorder', $salesorder,$id,array('id'=>'salesorder','class' => 'form-control','placeholder'=>__('Select Sales Orders'))) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('salesorder',__('Sales Orders'),['class'=>'form-label']) }}
                {!!Form::select('salesorder', $salesorder, null,array('id'=>'salesorder','class' => 'form-control','placeholder'=>__('Select Sales Orders'))) !!}
            </div>
        </div>
    @endif
    @if($type == 'quote')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('quote',__('Quote'),['class'=>'form-label']) }}
                {!!Form::select('quote', $quote, $id,array('class' => 'form-control','placeholder'=>__('Select Quote'))) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('quote',__('Quote'),['class'=>'form-label']) }}
                {!!Form::select('quote', $quote, null,array('class' => 'form-control','placeholder'=>__('Select Quote'))) !!}
            </div>
        </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('opportunity',__('opportunity'),['class'=>'form-label']) }}
            {!!Form::select('opportunity', $opportunities, null,array('id'=>'opportunity','class' => 'form-control','required'=>'required','placeholder'=>__('Select Opportunity'))) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('account',__('Account'),['class'=>'form-label']) }}
            {{Form::text('account',null,array('id'=>'account_name','class'=>'form-control','placeholder'=>__('Enter account'),'required'=>'required'))}}
            <input type="hidden" name="account" id="account_id">
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('date_invoice',__('Date Invoice'),['class'=>'form-label']) }}
            {{Form::date('date_quoted',date('Y-m-d'),array('class'=>'form-control datepicker','required'=>'required'))}}

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('quote_number',__('Quote Number'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('quote_number',null,array('class'=>'form-control','placeholder'=>__('Enter Quote Number'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('billing_contact',__('Billing Contact'),['class'=>'form-label']) }}
            {!!Form::select('billing_contact', $contact, null,array('class' => 'form-control','placeholder'=>__('Select Billing Contact'))) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('billing_address',__('Billing Address'),['class'=>'form-label']) }}<x-required></x-required>
            <div class="action-btn bg-primary float-end">
            <a class="mx-3 btn btn-sm d-inline-flex align-items-center text-white " id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address"><i class="fas fa-copy"></i></a>
            <span class="clearfix"></span>
            </div>
            {{Form::text('billing_address',null,array('id'=>'billing_address','class'=>'form-control','placeholder'=>__('Billing Address'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('shipping_address',__('Shipping Address'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('shipping_address',null,array('id'=>'shipping_address','class'=>'form-control','placeholder'=>__('Shipping Address'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('billing_city',null,array('id'=>'billing_city','class'=>'form-control','placeholder'=>__('Billing city'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('billing_state',null,array('id'=>'billing_state','class'=>'form-control','placeholder'=>__('Billing State'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('shipping_city',null,array('id'=>'shipping_city','class'=>'form-control','placeholder'=>__('Shipping City'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('shipping_state',null,array('id'=>'shipping_state','class'=>'form-control','placeholder'=>__('Shipping State'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('billing_country',null,array('id'=>'billing_country','class'=>'form-control','placeholder'=>__('Billing Country'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('billing_postalcode',null,array('id'=>'billing_postalcode','class'=>'form-control','placeholder'=>__('Billing Postal Code'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('shipping_country',null,array('id'=>'shipping_country','class'=>'form-control','placeholder'=>__('Shipping Country'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::text('shipping_postalcode',null,array('id'=>'shipping_postalcode','class'=>'form-control','placeholder'=>__('Shipping Postal Code'),'required'=>'required'))}}
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('shipping_contact',__('Shipping Contact'),['class'=>'form-label']) }}
            {!!Form::select('shipping_contact', $contact, null,array('class' => 'form-control','placeholder'=>__('Select Shipping Contact'))) !!}
        </div>
    </div>
    {{-- <div class="col-6">
        <div class="form-group">
            {{Form::label('tax',__('Tax'),['class'=>'form-label']) }}
            {{ Form::select('tax[]', $tax,null, array('class' => 'form-control select2','id'=>'choices-multiple2','multiple'=>'')) }}
        </div>
    </div> --}}
    <div class="col-6">
        <div class="form-group">
            {{Form::label('shipping_provider',__('Shipping Provider'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('shipping_provider', $shipping_provider, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('user', $user, null,array('class' => 'form-control','required' => 'required','placeholder'=>__('Select User'))) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description'),'required' => 'required'))}}
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Save'),array('class'=>'btn btn-primary'))}}{{Form::close()}}
</div>

</div>
</div>
{{Form::close()}}


