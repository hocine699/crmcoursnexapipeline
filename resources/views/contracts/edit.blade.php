@php
$plansettings = App\Models\Utility::plansettings();
@endphp
{{ Form::model($contract, array('route' => array('contract.update', $contract->id), 'method' => 'PUT')) }}

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
        <div class="col-md-6 form-group">
            {{ Form::label('name', __('Contract Name'),['class'=>'form-label']) }}
            {{ Form::text('name', null, array('class' => 'form-control','required'=>'required','placeholder'=>__('Enter Name'))) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('client_name', __('User Name'),['class'=>'form-label']) }}
            {{ Form::select('client_name', $client,null, array('class' => 'form-control select2','required'=>'required','placeholder'=>__('Select Name'))) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('subject', __('Subject'),['class'=>'form-label']) }}
            {{ Form::text('subject', null, array('class' => 'form-control','required'=>'required','placeholder'=>__('Enter Subject'))) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('value', __('Value'),['class'=>'form-label']) }}
            {{ Form::number('value', null, array('class' => 'form-control','required'=>'required','min' => '1','placeholder'=>__('Enter Value'),'placeholder'=>__('Enter Value'))) }}
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('type', __('Type'),['class'=>'form-label']) }}
            {{ Form::select('type', $contractType,null, array('class' => 'form-control select2','required'=>'required','placeholder'=>__('Select Type'))) }}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('date', __('Start Date / End Date'),['class'=>'form-label']) }}

                <div class='input-group'>
                    <input type='text' name="date" id='pc-daterangepicker-2'
                        class="form-control" placeholder="Select date range" />
                    <span class="input-group-text"><i
                            class="feather icon-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-12 form-group">
            {{ Form::label('notes', __('Description'),['class'=>'form-label']) }}
            {{ Form::textarea('notes', null, array('class' => 'form-control','placeholder'=>__('Enter Descripton'))) }}
        </div>

    </div>

<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>

</div>

    {{ Form::close() }}

    <script>
        document.querySelector("#pc-daterangepicker-2").flatpickr({
            mode: "range"
        });
    </script>
