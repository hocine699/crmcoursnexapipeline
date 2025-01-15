@php
$plansettings = App\Models\Utility::plansettings();
@endphp
{{Form::open(array('url'=>'campaign','method'=>'post','enctype'=>'multipart/form-data','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['campaign']) }}"
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
            {{Form::label('status',__('Status'),['class'=>'form-label']) }}
            {!!Form::select('status', $status, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('type',__('Type'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('type', $type, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('start_date',__('Start Date'),['class'=>'form-label']) }}
            {!!Form::date('start_date', date('Y-m-d'),array('class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('budget',__('Budget'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::number('budget',null,array('class'=>'form-control','placeholder'=>__('Enter Price'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('end_date',__('End Date'),['class'=>'form-label']) }}
            {!!Form::date('end_date', date('Y-m-d'),array('class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('target_list',__('Target Lists'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('target_list', $target_list, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('excluding_list',__('Excluding Target Lists'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('excluding_list', $target_list, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description'),['class'=>'form-label']) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description'),'required' => 'required'))}}
        </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('user', $user, null,array('class' => 'form-control','required' => 'required','placeholder' => __('Select User'))) !!}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Save'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
    </div>
</div>
</div>
{{Form::close()}}

