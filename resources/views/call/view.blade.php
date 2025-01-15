<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $call->name }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-md-5">
                        @if($call->status == 0)
                            <span class="badge bg-success p-2 px-3">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 1)
                            <span class="badge bg-info p-2 px-3">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 2)
                            <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 3)
                            <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 4)
                            <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 5)
                            <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Call::$status[$call->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Direction')}}</span></dt>
                    <dd class="col-md-5">
                        @if($call->direction == 0)
                            {{ __(\App\Models\Call::$direction[$call->direction]) }}
                        @elseif($call->direction == 1)
                            {{ __(\App\Models\Call::$direction[$call->direction]) }}
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($call->start_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('End Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($call->end_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Parent')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $call->parent }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Parent User')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">
                        @if(!empty($call->parent_id))
                            {{ $call->getparent($call->parent,$call->parent_id)  }}
                        @endif
                    </span></dd>

                    <dt class="col-sm-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-5"><span class="text-md">{{ $call->description }}</span></dd>


                    <dt class="col-sm-5"><span class="h6 text-md mb-0">{{__('Attendees User')}}</span></dt>
                    <dd class="col-sm-5"><span class="text-md">{{ !empty($call->attendees_users->name)?$call->attendees_users->name:'-' }}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-md mb-0">{{__('Attendees Contact')}}</span></dt>
                    <dd class="col-sm-5"><span class="text-md">{{ !empty($call->attendees_contacts->name)?$call->attendees_contacts->name:'-' }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Attendees Lead')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($call->attendees_leads->name)?$call->attendees_leads->name:'-' }}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-sm-5"><span class="text-sm">{{ !empty($call->assign_user)?$call->assign_user->name:''}}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-sm-5"><span class="text-sm">{{\Auth::user()->dateFormat($call->created_at)}}</span></dd>

                </dl>
            </div>

    </div>

    <div class="w-100 text-end pr-2">
        @can('Edit Call')
        <div class="action-btn ">
            <a href="{{ route('call.edit',$call->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white"data-bs-toggle="tooltip" data-title="{{__('Edit Call')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endcan
    </div>
</div>

