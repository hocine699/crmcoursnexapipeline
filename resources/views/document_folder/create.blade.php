{{Form::open(array('url'=>'document_folder','method'=>'post','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'form-label']) }}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('parent',__('Parent'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('parent', $parent, null,array('class' => 'form-control','required'=>'required')) !!}
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
{{Form::close()}}
