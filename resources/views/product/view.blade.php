<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $product->name }}</span></dd>


                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-md-5">
                        @if($product->status == 0)
                            <span class="badge status_badge bg-success p-2 px-3">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                        @elseif($product->status == 1)
                            <span class="badge status_badge bg-danger p-2 px-3">{{ __(\App\Models\Product::$status[$product->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Category')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($product->categorys)?$product->categorys->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Brand')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($product->Brands)?$product->Brands->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Price')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->priceFormat($product->price)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Tax')}}</span></dt>
                    <dd class="col-md-5">
                        <span class="text-md">

                            @foreach($product->tax($product->tax) as $tax)
                                <div class="tax1">
                                    @if(!empty($tax))
                                        <h6>
                                            <span class="badge status_badge bg-primary p-2 px-3">{{$tax->tax_name .' ('.$tax->rate.' %)'}}</span>
                                        </h6>
                                    @else
                                        <h6>
                                            <span class="badge status_badge bg-primary p-2 px-3">{{ __('No Tax')}}</span>
                                        </h6>
                                    @endif
                                </div>
                            @endforeach
                        </span>
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Part Number')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $product->part_number}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Weight')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $product->weight}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('URL')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $product->URL}}</span></dd>


                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $product->description }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($product->assign_user)?$product->assign_user->name:'-'}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($product->created_at)}}</span></dd>

                </dl>
            </div>

    </div>
    <div class="w-100 text-end pr-2">
        @can('Edit Product')
        <div class="action-btn">
            <a href="{{ route('product.edit',$product->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white" data-bs-toggle="tooltip"data-title="{{__('product Edit')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
        </a>
        </div>
        @endcan
    </div>
</div>

