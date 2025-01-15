<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $commonCase->name }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Number')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $commonCase->number}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-md-5">
                        @if($commonCase->status == 0)
                            <span class="badge status_badge bg-success p-2 px-3">{{ __(\App\Models\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 1)
                            <span class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 2)
                            <span class="badge status_badge bg-warning p-2 px-3">{{ __(\App\Models\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 3)
                            <span class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 4)
                            <span class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 5)
                            <span class="badge status_badge bg-warning p-2 px-3">{{ __(\App\Models\CommonCase::$status[$commonCase->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Account')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($commonCase->accounts)?$commonCase->accounts->name:'-'  }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md">{{__('Priority')}}</span></dt>
                    <dd class="col-md-5">
                        @if($commonCase->priority == 0)
                            <span class="badge status_badge bg-primary p-2 px-3">{{ __(\App\Models\CommonCase::$priority[$commonCase->status]) }}</span>
                        @elseif($commonCase->priority == 1)
                            <span class="badge status_badge bg-info p-2 px-3">{{ __(\App\Models\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 2)
                            <span class="badge status_badge bg-warning p-2 px-3">{{ __(\App\Models\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 3)
                            <span class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Contacts')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($commonCase->contacts->name)?$commonCase->contacts->name:'-' }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Type')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($commonCase->types)?$commonCase->types->name:'-' }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $commonCase->description }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{ __('Assigned User') }}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($commonCase->assign_user)?$commonCase->assign_user->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($commonCase->created_at)}}</span></dd>
                </dl>
            </div>

    </div>

    <div class="w-100 text-end pr-2">
        @can('Edit CommonCase')
        <div class="action-btn">
            <a href="{{ route('commoncases.edit',$commonCase->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white " data-bs-toggle="tooltip" data-title="{{__('Case Edit')}}"title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endcan
    </div>
</div>


