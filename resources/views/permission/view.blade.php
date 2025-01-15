<div class="col-lg-12 order-lg-1">
        <div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">User Name </small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->username }}</span>
                        </div>
                        <div class="col-sm-3 text-sm-right">
                            <img src="{{(!empty($user->avatar))? asset(Storage::url("upload/profile/".$user->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" width="50px;">
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">Name </small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->name }}</span>
                        </div>

                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">Title</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->title }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">Email</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->email }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">Phone</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->phone }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">Gender</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->gender }}</span>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="h6 text-sm mb-3 mb-sm-0">Teams and Access Control</small>
                        </div>
                        <div class="col-sm-12">
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <small class="h6 text-sm mb-3 mb-sm-0">Type</small>
                                </div>
                                <div class="col-sm-5">
                                    <span class="text-sm">{{ $user->type }}</span>
                                </div>
                                <div class="col-sm-4">
                                    <small class="h6 text-sm mb-3 mb-sm-0">Is Active</small>
                                </div>
                                <div class="col-sm-5">
                                    <span class="text-sm">{{ $user->is_active }}</span>
                                </div>
                                <div class="col-sm-4">
                                    <small class="h6 text-sm mb-3 mb-sm-0">Roles</small>
                                </div>
                                <div class="col-sm-5">
                                    <span class="text-sm">{{ $user->roles }}</span>
                                </div>
                                <div class="col-sm-12 text-sm-right">
                                    <span class="text-primary">Created At : </span>
                                    <span> {{ $user->created_at }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
