<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $document->name }}</span></dd>


                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-md-5">
                        @if($document->status == 0)
                            <span class="badge bg-success p-2 px-3 ">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                        @elseif($document->status == 1)
                            <span class="badge bg-info p-2 px-3 ">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                        @elseif($document->status == 2)
                            <span class="badge bg-warning p-2 px-3 ">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                        @elseif($document->status == 3)
                            <span class="badge bg-danger p-2 px-3 ">{{ __(\App\Models\Document::$status[$document->status]) }}</span>
                        @endif
                    </dd>
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Type')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $document->types->name}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Account')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($document->accounts)?$document->accounts->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Opportunities')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($document->opportunitys)?$document->opportunitys->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Publish Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($document->publish_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Expiration Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($document->expiration_date)}}</span></dd>


                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $document->description }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('File')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($document->attachment)?$document->attachment:'No File' }}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-sm-7"><span class="text-sm">{{ !empty($document->assign_user)?$document->assign_user->name:''}}</span></dd>

                    <dt class="col-sm-5"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-sm-7"><span class="text-sm">{{\Auth::user()->dateFormat($document->created_at)}}</span></dd>
                </dl>
            </div>

    </div>

    <div class="w-100 text-end pr-2">
        @can('Edit Document')
        <div class="action-btn">
            <a href="{{ route('document.edit',$document->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white "data-bs-toggle="tooltip" data-title="{{__('Document Edit')}}"  title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endcan
    </div>
</div>


