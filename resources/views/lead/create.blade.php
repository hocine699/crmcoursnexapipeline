@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
{{Form::open(array('url'=>'lead','method'=>'post','enctype'=>'multipart/form-data','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['lead']) }}"
            data-toggle="tooltip" title="{{ __('Generate') }}">
            <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
        </a>
    </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('account',__('Account'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('account', $account, null,array('class' => 'form-control','required'=>'required','placeholder'=>__('Select Account'))) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">    
            {{Form::label('email',__('Email'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
        </div>
    </div>
    {{-- <div class="col-6">
        <div class="form-group">
            {{Form::label('phone',__('Phone'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
        </div>
    </div> --}}
    <x-mobile divClass="col-6" name="phone" label="{{ __('Phone')}}" placeholder="{{__('Enter Phone')}}" required="required"></x-mobile>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('title',__('Title'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('website',__('Website'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('lead_address',__('Address'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('lead_address',null,array('class'=>'form-control','placeholder'=>__('Address'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::label('lead_city',__('City'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('lead_city',null,array('class'=>'form-control','placeholder'=>__('City'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::label('lead_state',__('State'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('lead_state',null,array('class'=>'form-control','placeholder'=>__('State'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('lead_postalcode',__('Postal Code'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('lead_postalcode',null,array('class'=>'form-control','placeholder'=>__('Postal Code'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('lead_country',__('Country'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('lead_country',null,array('class'=>'form-control','placeholder'=>__('Country'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <hr class="mt-2 mb-2">
        <h6>{{__('Details')}}</h6>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('status',__('Status'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('status',$status, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('source',__('Source'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('source', $leadsource, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('opportunity_amount',__('Opportunity Amount'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::number('opportunity_amount', null,array('class' => 'form-control','required'=>'required','placeholder'=>__('Enter Amount'))) !!}
        </div>
    </div>
    @if($type == 'campaign')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('campaign',__('Campaign'),['class'=>'form-label']) }}
                {!! Form::select('campaign', $campaign, $id,array('class' => 'form-control')) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('campaign',__('Campaign'),['class'=>'form-label']) }}
                {!! Form::select('campaign', $campaign, null,array('class' => 'form-control','placeholder'=>__('Select Campaign'))) !!}
            </div>
        </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('industry',__('Industry'),['class'=>'form-label']) }}
            {!! Form::select('industry', $industry, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User'),['class'=>'form-label']) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control','required' => 'required','placeholder'=>__('Select User'))) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Description',__('Description'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description'),'required' => 'required'))}}
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Save'),array('class'=>'btn btn-primary '))}}
</div>
</div>
{{Form::close()}}
