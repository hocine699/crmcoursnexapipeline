@php
    $plansettings = App\Models\Utility::plansettings();
@endphp
{{Form::open(array('url'=>'document','method'=>'post','enctype'=>'multipart/form-data','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    @if (isset($plansettings['enable_chatgpt']) && $plansettings['enable_chatgpt'] == 'on')
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['document']) }}"
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
                {{Form::label('account',__('Account'),['class'=>'form-label']) }}
                {!!Form::select('account', $account, $id,array('class' => 'form-control ','required'=>'required','placeholder'=>__('Select Account'))) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('account',__('Account'),['class'=>'form-label']) }}
                {!! Form::select('account', $account, null,array('class' => 'form-control ','required'=>'required','placeholder'=>__('Select Account'))) !!}
            </div>
        </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('folder',__('Folder'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('folder', $folder, null,array('class' => 'form-control ','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('type',__('Type'),['class'=>'form-label']) }}<x-required></x-required>
            {!!Form::select('type', $types, null,array('class' => 'form-control ','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">    
            {{Form::label('opportunities',__('Opportunities'),['class'=>'form-label']) }}
            {!!Form::select('opportunities', $opportunities, null,array('class' => 'form-control ','required'=>'required','placeholder'=>__('Select Opportunities'))) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('status',__('Status'),['class'=>'form-label']) }}
            {!!Form::select('status', $status, null,array('class' => 'form-control ','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('publish_date',__('Publish Date'),['class'=>'form-label']) }}
            {!!Form::date('publish_date', date('Y-m-d'),array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('expiration_date',__('Expiration Date'),['class'=>'form-label']) }}
            {!!Form::date('expiration_date', date('Y-m-d'),array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>

    <div class="col-6 field" data-name="attachments">
        <div class="attachment-upload">
            <div class="attachment-button">
                <div class="pull-left">
                    <div class="form-group">
                    {{Form::label('attachment',__('Attachment'),['class'=>'form-label']) }}<x-required></x-required>
                    {{-- {{Form::file('attachment',array('class'=>'form-control'))}} --}}
                    <input type="file"name="attachment" class="form-control mb-3"  required=required onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                    <img id="blah" width="20%" height="20%"/>
                    </div>
                </div>
            </div>
            <div class="attachments"></div>
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
            {{Form::label('description',__('Description'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description'),'required'=>'required'))}}
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

