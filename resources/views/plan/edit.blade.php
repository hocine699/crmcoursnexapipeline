@php
$settings = App\Models\Utility::settings();
@endphp
{{Form::model($plan, array('route' => array('plan.update', $plan->id), 'method' => 'PUT', 'enctype' => "multipart/form-data",'class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    @if (!empty($settings['chatgpt_key']))
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['plan']) }}"
            data-toggle="tooltip" title="{{ __('Generate') }}">
            <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
        </a>
    </div>
    @endif
    <div class="col-6">
    <div class="form-group">
        {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
        {{Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter Plan Name'),'required'=>'required'))}}
    </div>
    </div>
    @if($plan->price>0)
    <div class="col-6">
        <div class="form-group">
            {{Form::label('price',__('Price'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('price',null,array('class'=>'form-control','placeholder'=>__('Enter Plan Price'),'step'=>'0.01'))}}
        </div>
    </div>
    @endif
    <div class="col-6">
    <div class="form-group">
        {{Form::label('max_user',__('Maximum User'),['class'=>'form-label'])}}<x-required></x-required>
        {{Form::number('max_user',null,array('class'=>'form-control','required'=>'required', 'placeholder' => __('Enter Maximum User')))}}
        <span class="small">{{__('Note: "-1" for lifetime')}}</span>
    </div>
    </div>
    <div class="col-6">
    <div class="form-group">
        {{Form::label('max_account',__('Maximum Account'),['class'=>'form-label'])}}<x-required></x-required>
        {{Form::number('max_account',null,array('class'=>'form-control','required'=>'required', 'placeholder' => __('Enter Maximum Account')))}}
        <span class="small">{{__('Note: "-1" for lifetime')}}</span>
    </div>
    </div>
    <div class="col-6">
    <div class="form-group">
        {{Form::label('max_contact',__('Maximum Contact'),['class'=>'form-label'])}}<x-required></x-required>
        {{Form::number('max_contact',null,array('class'=>'form-control','required'=>'required', 'placeholder' => __('Enter Maximum Contact')))}}
        <span class="small">{{__('Note: "-1" for lifetime')}}</span>
    </div>
    </div>
    <div class="col-6">
    <div class="form-group">
        {{ Form::label('duration', __('Duration'),['class'=>'form-label']) }}<x-required></x-required>
        {!! Form::select('duration', $arrDuration, null,array('class' => 'form-control','required'=>'required')) !!}
    </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('storage_limit', __('Storage Limit'), ['class' => 'form-label']) }}
            <div class="input-group">
                <input for="storage_limit" name="storage_limit" type="text" class="form-control" value={{$plan->storage_limit}} placeholder="Enter Storage Limit"z
                    required>
                <div class="input-group-append">
                    <span class="input-group-text">
                        MB
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-3 plan_price_div">
        <label class="form-check-label" for="trial"></label>
        <div class="form-group">
            <label for="trial" class="form-label">{{ __('Trial is enable(on/off)') }}</label>
            <div class="form-check form-switch custom-switch-v1 float-end">
                <input type="checkbox" name="trial" class="form-check-input input-primary pointer" value="1" id="trial" {{ $plan->trial == 1 ?' checked ':'' }}>
                <label class="form-check-label" for="trial"></label>
            </div>
        </div>
    </div>
    <div class="col-md-6  {{ $plan->trial == 1 ?'  ':'d-none' }} plan_div plan_price_div">
        <div class="form-group">
            {{ Form::label('trial_days', __('Trial Days'), ['class' => 'form-label']) }}
            {{ Form::number('trial_days',null, ['class' => 'form-control','placeholder' => __('Enter Trial days'),'step' => '1','min'=>'1']) }}
        </div>
    </div>
    <div class="col-12">
    <div class="form-group">
        {{ Form::label('description', __('Description'),['class'=>'form-label']) }}<x-required></x-required>
        {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2','required'=>'required','placeholder' => __('Enter Description')]) !!}
    </div>
    </div>
    <div class="col-6">
        <div class="custom-control form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="enable_chatgpt" id="enable_chatgpt"
            {{ $plan->enable_chatgpt == 'on' ? 'checked="checked"' : '' }}>
        <label class="custom-control-label form-check-label"
            for="enable_chatgpt">{{ __('Enable Chatgpt') }}</label>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            {{Form::submit(__('Update'),array('class'=>'btn btn-primary '))}}
    </div>
</div>
{{ Form::close() }}


