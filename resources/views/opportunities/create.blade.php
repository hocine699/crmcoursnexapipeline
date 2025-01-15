@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
{{Form::open(array('url'=>'opportunities','method'=>'post','enctype'=>'multipart/form-data','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['opportunities']) }}"
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
    @if($type == 'account')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('account',__('Account Name'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::select('account', $account_name, $id,array('class' => 'form-control ','required'=>'required')) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('account',__('Account'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::select('account', $account_name, null,array('class' => 'form-control ','required'=>'required','placeholder'=>__('Select Account'))) !!}
            </div>
        </div>
    @endif
    @if($type == 'contact')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('contact',__('Contacts'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::select('contact', $contact, $id,array('class' => 'form-control ','required'=>'required','placeholder'=>__('Select Contacts'))) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('contact',__('Contacts'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::select('contact', $contact, null,array('class' => 'form-control ','required'=>'required')) !!}
            </div>
        </div>
    @endif
    @if($type == 'campaign')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('campaign_id',__('Campaign'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::select('campaign_id', $campaign_id,$id,array('class' => 'form-control ','required'=>'required')) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('campaign',__('Campaign'),['class'=>'form-label']) }}<x-required></x-required>
                {!!Form::select('campaign', $campaign_id, null,array('class' => 'form-control ','required'=>'required','placeholder'=>__('Select Campaign'))) !!}
            </div>
        </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('stage',__('Opportunities Stage'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('stage', $opportunities_stage, null,array('class' => 'form-control ','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('amount',__('Amount'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::number('amount', null,array('class' => 'form-control ','placeholder'=>__('Enter Amount'),'required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('probability',__('Probability'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::number('probability',null,array('class'=>'form-control','placeholder'=>__('Enter Probability'),'required'=>'required'))}}
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('close_date',__('Close Date'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::date('close_date',date('Y-m-d'),array('class'=>'form-control ','placeholder'=>__('Enter Title'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('lead_source',__('Lead Source'),['class'=>'form-label']) }}
            {!! Form::select('lead_source', $leadsource, null,array('class' => 'form-control ')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User'),['class'=>'form-label']) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control ','placeholder'=>__('Select User'))) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Description',__('Description'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description'),'required'=>'required'))}}
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
