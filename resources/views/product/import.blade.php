
    {{ Form::open(array('route' => array('product.import'),'method'=>'post', 'enctype' => "multipart/form-data")) }}
    <div class="row">
        <div class="col-md-12 mb-6">
            {{Form::label('file',__('Download sample product CSV file'),['class'=>'form-label'])}}
            <a href="{{asset(Storage::url('uploads/sample')).'/sample-product.xlsx'}}" class="btn btn-sm btn-primary btn-icon-only" data-toggle="tooltip" data-original-title="{{__('Download')}}">
                <i class="ti ti-download"></i> 
            </a>
        </div>
        <div class="col-md-12">
            {{Form::label('file',__('Select CSV File'),['class'=>'form-label'])}}
            <div class="choose-file form-group">
                <label for="file" class="form-control-label">
                    <div class='form-label'>{{__('Choose file here')}}</div>
                    <input type="file" class="form-control" name="file" id="file" data-filename="upload_file" required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-secondary"
                data-bs-dismiss="modal">{{__('Cancel')}}</button>
                {{Form::submit(__('Upload'),array('class'=>'btn btn-primary '))}}
        </div>
       
    </div>
    {{ Form::close() }}
