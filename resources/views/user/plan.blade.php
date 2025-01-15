@foreach($plans as $plan)
    <div class="list-group-item">
        <div class="row align-items-center">
            {{-- <div class="col-auto">
                <span class="badge bg-success p-2 px-3 rounded">{{$plan->name}}</span>
                <a href="#" class="avatar rounded-circle">
                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/plan')).'/'.$plan->image}}" class="">
                </a>
            </div> --}}
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{$plan->name}}</a>
                <div>
                    <span class="text-sm">{{\Auth::user()->priceFormat($plan->price)}} {{' / '. __(\App\Models\Plan::$arrDuration[$plan->duration])}}</span>
                </div>
            </div>
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{__('User')}}</a>
                <div>
                    <span class="text-sm">{{$plan->max_user}}</span>
                </div>
            </div>
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{__('Account')}}</a>
                <div>
                    <span class="text-sm">{{$plan->max_account}}</span>
                </div>
            </div>
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{__('Contact')}}</a>
                <div>
                    <span class="text-sm">{{$plan->max_contact}}</span>
                </div>
            </div>
            <div class="col-auto">
                @if($user->plan==$plan->id)
                    <span class="badge bg-success p-2 px-3">{{__('Active')}}</span>
                @else
                <div class="action-btn bg-warning ms-2">
                    <a href="{{route('plan.active',[$user->id,$plan->id])}}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" title="{{__('Upgrade Plan')}}" data-title="{{__('Click to Upgrade Plan')}}">
                        <span class="btn-inner--icon"><i class="fas fa-cart-plus"></i></span>
                    </a>
                </div>
                 
                @endif
            </div>
        </div>
    </div>
@endforeach
