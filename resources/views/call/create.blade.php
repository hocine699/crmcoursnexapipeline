
@php
    $setting = App\Models\Utility::settings();
    $plansettings = App\Models\Utility::plansettings();
@endphp

{{ Form::open(['url' => 'call', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
                data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['call']) }}"
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
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('status', $status, null, ['class' => 'form-control ', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::date('start_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::date('end_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('direction', __('Direction'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('direction', $direction, null, ['class' => 'form-control ', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            {{ Form::label('parent', __('Parent'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('parent', ['' => __('Select Parent')] + $parent, null, [
                'class' => 'form-control',
                'name' => 'parent',
                'id' => 'parent',
                'required' => 'required',
            ]) !!}
        </div>
    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            {{ Form::label('parent_id', __('Parent User'), ['class' => 'form-label']) }}
            <select class="form-control" name="parent_id" id="parent_id" required=required>

            </select>
        </div>
    </div>
    @if ($type == 'account')
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('account', __('Account Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('account', $account_name, $id, [
                    'class' => 'form-control',
                    'required' => 'required',
                    'placeholder' => 'Select Account',
                ]) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('account', $account_name, null, [
                    'class' => 'form-control',
                    'required' => 'required',
                    'placeholder' => 'Select Account',
                ]) !!}
            </div>
        </div>
    @endif
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('Assign User', __('Assign User'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('user', $user, null, ['class' => 'form-control ', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Description'), 'required' => 'required']) }}
        </div>
    </div>

    <div class="col-12">
        <hr class="mt-2 mb-2">
        <h6>{{ __('Attendees') }}</h6>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{ Form::label('attendees_user', __('Attendees User'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('attendees_user', $user, null, ['class' => 'form-control ', 'required' => 'required','placeholder' => __('Select User')]) !!}

        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('attendees_contact', __('Attendees Contact'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('attendees_contact', $attendees_contact, null, [
                'class' => 'form-control ',
                'required' => 'required', 
                'placeholder' => __('Select Contact')
            ]) !!}

        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('attendees_lead', __('Attendees Lead'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('attendees_lead', $attendees_lead, null, [
                'class' => 'form-control ',
                'required' => 'required', 'placeholder' => __('Select Lead')
            ]) !!}

        </div>
    </div>
    @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
        <div class="form-group col-md-6">
            <label>{{ __('Synchronize in Google Calendar') }}</label>
            <div class="form-check form-switch pt-2">
                <input id="switch-shadow" class="form-check-input" value="1" name="is_check" type="checkbox">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
    @endif
    {{-- <div class="form-group">
            <label>Synchroniz in Google Calendar ?</label>
            <div class="switch__container">
                <input id="switch-shadow" class="switch switch--shadow" value="1" name="is_check"
                    type="checkbox">
                <label for="switch-shadow"></label>
            </div>
        </div> --}}
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{ Form::submit(__('Save'), ['class' => 'btn btn-primary ']) }}
    </div>
</div>
</div>
{{ Form::close() }}
