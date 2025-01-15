{{Form::open(array('url'=>'permission','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'form-label']) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
        </div>
        <div class="form-group">
            @if(!$roles->isEmpty())
                <h6>{{__('Assign Permission to Roles')}}</h6>
                
                @foreach ($roles as $role)
                    <div class="form-check">
                        {{Form::checkbox('roles[]',$role->id,false, ['class'=>'form-check-input','id' =>'role'.$role->id])}}
                        {{Form::label('role'.$role->id, __(ucfirst($role->name)),['class'=>'form-check-label '])}}
                    </div>
                @endforeach
            @endif
            @error('roles')
            <span class="invalid-roles" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary"
            data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{Form::submit(__('Save'),array('class'=>'btn btn-primary '))}}
    </div>
</div>
{{Form::close()}}
