{{Form::open(array('url'=>'role','method'=>'post','class' => 'needs-validation', 'novalidate'))}}

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),['class'=>'col-form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Role Name'), 'required' => 'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            @if(!empty($permissions))
                <h6>{{__('Assign Permission to Roles')}} </h6>
                <table class="table datatable" id="datatable">
                    <thead>
                    <tr>
                        <th>
                           <input type="checkbox" class="form-check-input align-middle" name="checkall"  id="checkall" >
                        </th>
                        <th>{{__('Module')}} </th>
                        <th>{{__('Permissions')}} </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $modules=['Role','User','Account','Contact','Lead','Opportunities','CommonCase','Meeting','Call','Task','Document','Campaign','Quote','SalesOrder','Invoice','Payment','Invoice Payment','Product','AccountType','AccountIndustry','LeadSource','OpportunitiesStage','CaseType','DocumentFolder','DocumentType','TargetList','CampaignType','ProductCategory','ProductBrand','ProductTax','ShippingProvider','TaskStage','Form Builder','Contract','ContractType'];
                    @endphp
                    @foreach($modules as $module)
                        <tr>
                            <td><input type="checkbox" class="form-check-input align-middle ischeck" name="checkall" data-id="{{str_replace(' ', '', $module)}}" ></td>
                            <td><label class="ischeck" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label></td>
                            <td>
                                <div class="row ">
                                    @if(in_array('Manage '.$module,(array) $permissions))
                                        @if($key = array_search('Manage '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'form-check-input custom-control-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Manage',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Create '.$module,(array) $permissions))
                                        @if($key = array_search('Create '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'form-check-input custom-control-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Create',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Edit '.$module,(array) $permissions))
                                        @if($key = array_search('Edit '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'form-check-input custom-control-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Edit',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Delete '.$module,(array) $permissions))
                                        @if($key = array_search('Delete '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'form-check-input custom-control-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Delete',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                    @if(in_array('Show '.$module,(array) $permissions))
                                        @if($key = array_search('Show '.$module,$permissions))
                                            <div class="col-md-3 custom-control custom-checkbox">
                                                {{Form::checkbox('permissions[]',$key,false, ['class'=>'form-check-input custom-control-input isscheck isscheck_'.str_replace(' ', '', $module),'id' =>'permission'.$key])}}
                                                {{Form::label('permission'.$key,'Show',['class'=>'custom-control-label'])}}<br>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
<script>
    $(document).ready(function () {
           $("#checkall").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
           $(".ischeck").click(function(){
                var ischeck = $(this).data('id');
                 $('.isscheck_'+ ischeck).prop('checked', this.checked);

            });
        });
</script>
