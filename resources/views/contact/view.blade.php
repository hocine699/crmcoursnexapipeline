<div class="row">
    <div class="col-lg-12">

            <div class="">


                <dl class="row">
                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->name }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Account')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ !empty($contact->assign_account)?$contact->assign_account->name:'-'}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Email')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->email }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Phone')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->phone }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Billing Address')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->contact_address }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('City')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->contact_city }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('State')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->contact_state }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Country')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ $contact->contact_country }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{ !empty($contact->assign_user)?$contact->assign_user->name:'-'}}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-md-4"><span class="text-md">{{\Auth::user()->dateFormat($contact->created_at)}}</span></dd>
                </dl>
            </div>

    </div>
    <div class="w-100 text-end pr-2">
        @can('Edit Contact')
        <div class="action-btn">
            <a href="{{ route('contact.edit',$contact->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white " data-bs-toggle="tooltip"data-title="{{__('Contact Edit')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>
        @endcan
    </div>
</div>
