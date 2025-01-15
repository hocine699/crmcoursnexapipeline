<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $opportunities->name }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Account')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($opportunities->accounts)?$opportunities->accounts->name:'-'  }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Stage')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($opportunities->stages)?$opportunities->stages->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Amount')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{!empty(\Auth::user()->priceFormat( $opportunities->amount))?\Auth::user()->priceFormat( $opportunities->amount):0}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md">{{__('Probability')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $opportunities->probability }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Close Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($opportunities->close_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Contacts')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($opportunities->contacts)?$opportunities->contacts->name:''}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Lead Source')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($opportunities->leadsource)?$opportunities->leadsource->name:''}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $opportunities->description }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($opportunities->assign_user)?$opportunities->assign_user->name:''}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($opportunities->created_at )}}</span></dd>
                </dl>
            </div>

    </div>

    <div class="w-100 text-end pr-2">
        @can('Edit Opportunities')
            <div class="action-btn">
                <a href="{{ route('opportunities.edit',$opportunities->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white" data-title="{{__('Opportunities Edit')}}" data-bs-toggle="tooltip"title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
                </a>
            </div>

        @endcan
    </div>
</div>
