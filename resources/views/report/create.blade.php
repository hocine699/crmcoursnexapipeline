{{Form::open(array('url'=>'report','method'=>'post','enctype'=>'multipart/form-data','class' => 'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'form-label']) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('chart_type',__('Chart Type'),['class'=>'form-label']) }}
            {!!Form::select('chart_type', $chart_type,null,array('class' => 'form-control ','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('entity_type',__('Entity Type'),['class'=>'form-label']) }}
            {!!Form::select('entity_type', $entity_type,null,array('class' => 'form-control ','name'=>'entity_type','id'=>'entity_type','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('group_by',__('Group By'),['class'=>'form-label']) }}
            <select class="form-control"  name="group_by" id="group_by" required=required>

            </select>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User'),['class'=>'form-label']) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control ','required'=>'required')) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary"
        data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Save'),array('class'=>'btn  btn-primary '))}}{{Form::close()}}
</div>
</div>
{{Form::close()}}
