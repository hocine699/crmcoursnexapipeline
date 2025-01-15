{{ Form::open(['route' => ['lead.import'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">

        <div class="col-md-12 mb-6">
            {{ Form::label('file', __('Download sample client CSV file'), ['class' => 'col-form-label']) }}
            <a href="{{ asset(Storage::url('uploads/sample')) . '/sample-client.xlsx' }}"
                class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-download"></i>
            </a>
        </div>
        <div class="col-md-12">

            {{ Form::label('file', __('Select CSV File'), ['class' => 'form-label']) }}
            <div class="choose-file form-group">
                <label for="file" class="form-control-label">
                    <div class='form-label'>{{ __('Choose file here') }}</div>
                    <input type="file" class="form-control" name="file" id="file" data-filename="upload_file"
                        required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancle') }}</button>
    <button type="submit" class="btn  btn-primary">{{ __('Update') }}</button>
</div>

{{ Form::close() }}
