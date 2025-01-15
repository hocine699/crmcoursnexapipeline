@if (\Auth::user()->type == 'super admin')
    {{ Form::open(['url' => 'user', 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
    <div class="form-group">
        {{ Form::label('name', __('User Name'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
    </div>
    <div class="form-group">
        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
    </div>
    <div class="form-group">
        {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter User Email'), 'required' => 'required']) }}
    </div>
    <div class="col-md-4 mb-3 form-group mt-4">
        <label for="password_switch">{{ __('Login is enable') }}</label>
        <div class="form-check form-switch custom-switch-v1 float-end">
            <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer"
                value="on" id="password_switch">
            <label class="form-check-label" for="password_switch"></label>
        </div>
    </div>

    <div class="form-group ps_div d-none">
        {!! Form::label('password', __('Password'), ['class' => 'form-label']) !!}
        {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter User Password'),'minlength'=>"8"))}}
    </div>

    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{ Form::submit(__('Create'), ['class' => 'btn btn-primary ']) }}
    </div>

    {{ Form::close() }}
@else
    {{ Form::open(['url' => 'user', 'method' => 'post', 'enctype' => 'multipart/form-data','class' => 'needs-validation', 'novalidate']) }}
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('User Name'), ['class' => 'form-label']) }}
                {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Title'), 'required' => 'required']) }}
            </div>
        </div>

        {{-- <div class="col-6">
            <div class="form-group">
                {{ Form::label('name', __('Phone'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone'), 'required' => 'required']) }}
            </div>
        </div> --}}
        <x-mobile divClass="col-6" name="phone" label="{{ __('Phone')}}" placeholder="{{__('Enter Phone')}}" required="required"></x-mobile>

        <div class="col-6">
            <div class="form-group ">
                {{ Form::label('name', __('Gender'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('gender', $gender, null, ['class' => 'form-control', 'required' => 'required']) !!}
            </div>
        </div>

        <div class="col-12 p-0">
            <hr class="m-0 mb-3">
            <h6>{{ __('Login Details') }}</h6>
        </div>
        <div class="col-6">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Password'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Password'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                {{ Form::label('user_roles', __('Roles'), ['class' => 'form-label']) }}<x-required></x-required>
                {!! Form::select('user_roles', $roles, null, ['class' => 'form-control ', 'required' => 'required']) !!}
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                {{ Form::label('name', __('Is Active'), ['class' => 'form-label']) }}
                <div>
                    <input type="checkbox" class="form-check-input" name="is_active" checked>
                </div>
            </div>
        </div>
        <div class="col-12 p-0">
            <hr class="m-0 mb-3">
            <h6>{{ __('Avatar') }}</h6>
        </div>
        <div class="col-12 mb-3 field" data-name="avatar">
            <div class="attachment-upload">
                <div class="attachment-button">
                    <div class="pull-left">
                        {{-- {{Form::file('avatar',array('class'=>'form-control'))}} --}}
                        <input type="file"name="avatar" class="form-control mb-3"
                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                        <img id="blah" width="25%" />
                    </div>
                </div>
                <div class="attachment"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
            {{ Form::submit(__('Save'), ['class' => 'btn  btn-primary  ']) }}{{ Form::close() }}
        </div>
    </div>
    {{ Form::close() }}
@endif

    <script>
        $(document).on('change', '#password_switch', function() {
            console.log('sd');
            if ($(this).is(':checked')) {
                $('.ps_div').removeClass('d-none');
                $('#password').attr("required", true);

            } else {
                $('.ps_div').addClass('d-none');
                $('#password').val(null);
                $('#password').removeAttr("required");
            }
        });
        $(document).on('click', '.login_enable', function() {
            setTimeout(function() {
                $('.modal-body').append($('<input>', {
                    type: 'hidden',
                    val: 'true',
                    name: 'login_enable'
                }));
            }, 2000);
        });
    </script>

