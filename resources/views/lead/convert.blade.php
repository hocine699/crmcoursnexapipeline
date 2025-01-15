{{ Form::model($lead, array('route' => array('lead.convert.to.account', $lead->id), 'method' => 'POST')) }}
<div class="row">
    <div class="col-xs-12">
        <!--account edit -->
        <div id="account_edit" class="tabs-card">
                    {{ Form::model($lead, array('route' => array('lead.convert.to.account', $lead->id), 'method' => 'POST')) }}
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                {{Form::label('name',__('Name'),['class'=>'form-label']) }}
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
                                {{Form::label('email',__('Email'),['class'=>'form-label']) }}
                                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter email'),'required'=>'required'))}}
                                @error('email')
                                <span class="invalid-email" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{Form::label('phone',__('Phone'),['class'=>'form-label']) }}
                                {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter phone'),'required'=>'required'))}}
                                @error('phone')
                                <span class="invalid-phone" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{Form::label('website',__('Website'),['class'=>'form-label']) }}
                                {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website'),'required'=>'required'))}}
                                @error('website')
                                <span class="invalid-website" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{Form::label('billing_address',__('Billing Address'),['class'=>'form-label']) }}
                                <div class="action-btn bg-primary ms-2 float-end">
                                <a class="mx-3 btn btn-sm d-inline-flex align-items-center text-white" id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address"><i class="ti ti-copy"></i></a>
                                <span class="clearfix"></span>
                                </div>
                                {{Form::text('billing_address',null,array('class'=>'form-control','placeholder'=>__('Enter Billing Address')))}}
                                @error('billing_address')
                                <span class="invalid-billing_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{Form::label('shipping_address',__('Shipping Address'),['class'=>'form-label']) }}
                                {{Form::text('shipping_address',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Address')))}}
                                @error('shipping_address')
                                <span class="invalid-shipping_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('city',__('City'),['class'=>'form-label']) }}
                                {{Form::text('billing_city',null,array('class'=>'form-control','placeholder'=>__('Enter Billing City')))}}
                                @error('billing_city')
                                <span class="invalid-billing_city" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('state',__('State'),['class'=>'form-label']) }}
                                {{Form::text('billing_state',null,array('class'=>'form-control','placeholder'=>__('Enter Billing State')))}}
                                @error('billing_state')
                                <span class="invalid-billing_state" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('shipping_city',__('City'),['class'=>'form-label']) }}
                                {{Form::text('shipping_city',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping City')))}}
                                @error('shipping_city')
                                <span class="invalid-shipping_city" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('shipping_state',__('State'),['class'=>'form-label']) }}
                                {{Form::text('shipping_state',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping State')))}}
                                @error('shipping_state')
                                <span class="invalid-shipping_state" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('billing_country',__('Country'),['class'=>'form-label']) }}
                                {{Form::text('billing_country',null,array('class'=>'form-control','placeholder'=>__('Enter Billing country')))}}
                                @error('billing_country')
                                <span class="invalid-billing_country" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('billing_postalcode',__('Postal Code'),['class'=>'form-label']) }}
                                {{Form::number('billing_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Billing Postal Code')))}}
                                @error('billing_postalcode')
                                <span class="invalid-billing_postalcode" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('shipping_country',__('Country'),['class'=>'form-label']) }}
                                {{Form::text('shipping_country',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Country')))}}
                                @error('shipping_country')
                                <span class="invalid-shipping_country" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                {{Form::label('shipping_postalcode',__('Postal Code'),['class'=>'form-label']) }}
                                {{Form::number('shipping_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Postal Code')))}}
                                @error('shipping_postalcode')
                                <span class="invalid-shipping_postalcode" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="mt-1 mb-2">
                            <h5>{{__('Detail')}}</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                {{Form::label('type',__('Type'),['class'=>'form-label']) }}
                                {!! Form::select('type', $accountype, null,array('class' => 'form-control ','required'=>'required')) !!}
                                @error('type')
                                <span class="invalid-name" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                {{Form::label('industry',__('Industry'),['class'=>'form-label']) }}
                                {!! Form::select('industry', $industry, null,array('class' => 'form-control ','required'=>'required')) !!}
                                @error('industry')
                                <span class="invalid-industry" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                {{Form::label('document_id',__('Document'),['class'=>'form-label']) }}
                                {!! Form::select('document_id', $document_id, null,array('class' => 'form-control','required'=>'required')) !!}
                                @error('industry')
                                <span class="invalid-industry" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                {{Form::label('description',__('Description'),['class'=>'form-label']) }}
                                {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Name'),'required'=>'required'))}}
                            </div>
                        </div>


                        <div class="col-6">
                            <div class="form-group">
                            {{Form::label('user',__('Assigned User'),['class'=>'form-label']) }}
                            {!! Form::select('user', $user, null,array('class' => 'form-control ','required'=>'required')) !!}
                            @error('user')
                            <span class="invalid-user" role="alert">
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
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var is_client = $("input[name='client_check']:checked").val();


        $("input[name='client_check']").click(function () {
            is_client = $(this).val();
            console.log(is_client);
            if (is_client == "exist") {
                $('.exist_client').removeClass('d-none');
                $('#client_name').removeAttr('required');
                $('#client_email').removeAttr('required');
                $('#client_password').removeAttr('required');
                $('.new_client').addClass('d-none');
            } else {
                $('.new_client').removeClass('d-none');
                $('#client_name').attr('required', 'required');
                $('#client_email').attr('required', 'required');
                $('#client_password').attr('required', 'required');
                $('.exist_client').addClass('d-none');
            }
        });
        if (is_client == "exist") {
            $('.exist_client').removeClass('d-none');
            $('#client_name').removeAttr('required');
            $('#client_email').removeAttr('required');
            $('#client_password').removeAttr('required');
            $('.new_client').addClass('d-none');
        } else {
            $('.new_client').removeClass('d-none');
            $('#client_name').attr('required', 'required');
            $('#client_email').attr('required', 'required');
            $('#client_password').attr('required', 'required');
            $('.exist_client').addClass('d-none');
        }
    })
</script>
