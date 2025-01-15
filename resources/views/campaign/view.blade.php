<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{ $campaign->name }}</span></dd>


                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-md-7">
                        @if($campaign->status == 0)
                            <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                        @elseif($campaign->status == 1)
                            <span class="badge bg-success p-2 px-3">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                        @elseif($campaign->status == 2)
                            <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                        @elseif($campaign->status == 3)
                            <span class="badge bg-info p-2 px-3">{{ __(\App\Models\Campaign::$status[$campaign->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Type')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{ $campaign->types->name}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Budget')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{\Auth::user()->priceFormat($campaign->budget)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{\Auth::user()->dateFormat($campaign->start_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('End Date')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{\Auth::user()->dateFormat($campaign->end_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Target Lists')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{ $campaign->target_lists->name}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Excluding Target Lists')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{ !empty($campaign->target_lists)?$campaign->target_lists->name:'-'}}</span></dd>


                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-md-7"><span class="text-md">{{ $campaign->description }}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-sm-7"><span class="text-sm">{{ !empty($campaign->assign_user)?$campaign->assign_user->name:''}}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-sm-7"><span class="text-sm">{{\Auth::user()->dateFormat($campaign->created_at)}}</span></dd>

                </dl>
            </div>

    </div>

    <div class="w-100 text-end pr-2">
        @can('Edit Campaign')
        <div class="action-btn">
            <a href="{{ route('campaign.edit',$campaign->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white "data-bs-toggle="tooltip" data-title="{{__('Campaign Edit')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endcan
    </div>
</div>

