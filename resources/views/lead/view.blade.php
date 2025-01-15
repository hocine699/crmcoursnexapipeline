<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->name }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Account name')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ !empty($lead->accounts)?$lead->accounts->name:'-'}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Email')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->email }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Phone')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->phone }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Title')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->title }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Website')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->website }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('lead Address')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->lead_address }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('City')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->lead_city }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('State')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->lead_state }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Country')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $lead->lead_country }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ !empty($lead->assign_user)?$lead->assign_user->name:''}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{\Auth::user()->dateFormat($lead->created_at)}}</span></dd>

                    <div class="col-12">
                        <hr class="mt-2 mb-2">
                        <h5>{{__('Details')}}</h5>
                    </div>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">
                            @if($lead  ->status == 0)
                                <span class="badge bg-success p-2 px-3">{{ __(\App\Models\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 1)
                                <span class="badge bg-info p-2 px-3">{{ __(\App\Models\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 2)
                                <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 3)
                                <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 4)
                                <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Lead::$status[$lead->status]) }}</span>
                            @elseif($lead->status == 5)
                                <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Lead::$status[$lead->status]) }}</span>
                            @endif
                        </span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Source')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ !empty($lead->LeadSource)?$lead->LeadSource->name:''}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Opportunity Amount')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{\Auth::user()->priceFormat($lead->opportunity_amount)}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Campaign')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ !empty($lead->campaigns)?$lead->campaigns->name:'-'}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Industry')}}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ !empty($lead->accountIndustry)?$lead->accountIndustry->name:''}}</span></dd>


                </dl>
            </div>

    {{-- </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-footer py-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="row align-items-center">
                            <dt class="col-md-12"><span class="h6 text-md mb-0">{{__('Assigned User')}}</span></dt>
                            <dd class="col-md-12"><span class="text-md">{{ !empty($lead->assign_user)?$lead->assign_user->name:''}}</span></dd>

                            <dt class="col-md-12"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-md-12"><span class="text-md">{{\Auth::user()->dateFormat($lead->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div> --}}
    <div class="w-100 text-end pr-2">
        @can('Edit Lead')
        <div class="action-btn">
            <a href="{{ route('lead.edit',$lead->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white" data-bs-toggle="tooltip"data-title="{{__('Lead Edit')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>
        @endcan
    </div>
</div>

