
@php
$plansettings = App\Models\Utility::plansettings();
@endphp
{{ Form::open(['url' => 'commoncases', 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['case']) }}"
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

    @if ($type == 'account')
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('account', $account, $id, ['class' => 'form-control ', 'required' => 'required','placeholder' => __('Select Account')]) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('account', __('Account'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('account', $account, null, ['class' => 'form-control ', 'required' => 'required' ,'placeholder' => __('Select Account')]) !!}
            </div>
        </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('priority', $priority, null, ['class' => 'form-control ', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('contacts', __('Contact'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('contacts', $contact_name, null, ['class' => 'form-control ', 'required' => 'required' ,'placeholder' => __('Select Contact')]) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('type', $case_type, null, ['class' => 'form-control ', 'required' => 'required']) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('User', __('Assigned User'), ['class' => 'form-label']) }}<x-required></x-required>
            {!! Form::select('user', $user, null, ['class' => 'form-control ', 'required' => 'required' ,'placeholder' => __('Select User')]) !!}
        </div>
    </div>

<div class="col-12">
    <div class="form-group">
        {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter Description'), 'required' => 'required']) }}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    {{ Form::submit(__('Save'), ['class' => 'btn  btn-primary ']) }}{{ Form::close() }}
</div>
</div>
{{ Form::close() }}
