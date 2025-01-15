@php
    $setting = App\Models\Utility::settings();
    $plansettings = App\Models\Utility::plansettings();
@endphp


{{ Form::open(['url' => 'task', 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
        <div class="text-end">
            <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
                data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['task']) }}"
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
            {{ Form::label('stage', __('Task Stage'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('stage', $stage, null, ['class' => 'form-control ', 'required' => 'required']) !!}
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

    <div class="col-6" data-name="parent">
        <div class="form-group">
            {{ Form::label('parent', __('Assign'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('parent', ['' => __('Select Parent')] + $parent, null, [
                'class' => 'form-control',
                'id' => 'parents',
                'required' => 'required'
            ]) !!}
        </div>
    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            {{ Form::label('parent_id', __('Assign Name'), ['class' => 'form-label']) }}
            <select class="form-control" name="parent_id" id="parent_id" required=required>

            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('priority', $priority, null, ['class' => 'form-control ', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6 mb-3 field" data-name="attachments">
        <div class="attachment-upload">
            <div class="attachment-button">
                <div class="pull-left">
                    <div class="form-group">
                        {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{-- {{Form::file('attachment',array('class'=>'form-control'))}} --}}
                        <input type="file"name="attachment" class="form-control mb-3"
                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                        <img id="blah" width="20%" height="20%" />
                    </div>
                </div>
            </div>
            <div class="attachments"></div>
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
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('Assign User', __('Assign User'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('user', $user, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select User')]) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Description'), 'required' => 'required']) }}
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
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}
        </button>
        {{ Form::submit(__('Save'), ['class' => 'btn  btn-primary ']) }}{{ Form::close() }}
    </div>

</div>
</div>
{{ Form::close() }}
