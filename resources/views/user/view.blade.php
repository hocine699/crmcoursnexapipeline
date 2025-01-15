<div class="col-lg-12 order-lg-1">

            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('User Name')}} </small>
                        </div>
                        <div class="col-md-5">
                            <span class="text-md">{{ $user->username }}</span>
                        </div>
                        <div class="col-md-3 text-md-end">
                            @php
                                $profile=\App\Models\Utility::get_file('upload/profile/');
                            @endphp
                        <img src="{{(!empty($user->avatar))? asset($profile.$user->avatar): ($profile.'avatar.png')}}" class="rounded border-2 border border-primary" width="50px;">

                        </div>
                        <div class="col-md-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Name')}} </small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-md">{{ $user->name }}</span>
                        </div>

                        <div class="col-sm-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Title')}}</small>
                        </div>
                        <div class="col-md-5">
                            <span class="text-md">{{ $user->title }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Email')}}</small>
                        </div>
                        <div class="col-md-5">
                            <span class="text-md">{{ $user->email }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Phone')}}</small>
                        </div>
                        <div class="col-md-5">
                            <span class="text-md">{{ $user->phone }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Gender')}}</small>
                        </div>
                        <div class="col-md-5">
                            <span class="text-md">{{ $user->gender }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Created At :')}} </small>
                        </div>
                        <div class="col-md-5">
                            <span class="text-md">{{\Auth::user()->dateFormat($user->created_at )}}</span>
                        </div>

                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-12">
                            <small class="h6 text-md mb-3 mb-md-0">{{__('Teams and Access Control')}}</small>
                        </div>
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <small class="h6 text-md mb-3 mb-md-0">{{__('Type')}}</small>
                                </div>
                                <div class="col-md-5">
                                    <span class="text-md">{{ $user->type }}</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="h6 text-md mb-3 mb-md-0">{{__('Is Active')}}</small>
                                </div>
                                <div class="col-md-5">
                                    <input type="checkbox" class="form-check-input" disabled name="is_active" {{($user->is_active == 1)? 'checked': ''}}>
                                </div>
                                <div class="col-md-4">
                                    <small class="h6 text-md mb-3 mb-md-0">{{__('Roles')}}</small>
                                </div>
                                <div class="col-md-5">
                                        <span class="text-md">{{!empty($roles[0]->name)?$roles[0]->name:'-' }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </li>
            </ul>

    <div class=" text-end ">
        @can('Edit User')
        <div class="action-btn">
            <a href="{{ route('user.edit',$user->id) }}" data-bs-toggle="tooltip" title="{{__('Edit')}}" class="mx-3 bg-info btn btn-sm align-items-center text-white"><i class="ti ti-pencil"></i>
            </a>
        </div>
        @endcan
    </div>
</div>
