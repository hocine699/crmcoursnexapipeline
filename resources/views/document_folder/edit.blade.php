{{Form::model($documentFolder, array('route' => array('document_folder.update', $documentFolder->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
        {{Form::label('parent',__('parent'),['class'=>'form-label']) }}<x-required></x-required>
        {!! Form::select('parent', $parent, null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
        @error('parent')
        <span class="invalid-parent" role="alert">
        <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::textarea('description',null,array('class' =>'form-control ','rows'=>3,'required'=>'required','placeholder'=>__('Enter Description'))) !!}
            @error('description')
            <span class="invalid-description" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
</div>
{{Form::close()}}
