@php
$settings = App\Models\Utility::settings();
@endphp
{{Form::model($coupon, array('route' => array('coupon.update', $coupon->id), 'method' => 'PUT','class' => 'needs-validation', 'novalidate')) }}
<div class="row">
    @if (!empty($settings['chatgpt_key']))
    <div class="text-end">
        <a href="#" data-size="md" class="btn btn-sm btn-primary" data-ajax-popup-over="true" data-size="md"
            data-title="{{ __('Generate content with AI') }}" data-url="{{ route('generate', ['coupon']) }}"
            data-toggle="tooltip" title="{{ __('Generate') }}">
            <i class="fas fa-robot"></span><span class="robot">{{ __('Generate With AI') }}</span></i>
        </a>
    </div>
    @endif
    <div class="form-group col-md-12">
        <label for="name" class="col-form-label">{{__('Name')}}</label>
        <input type="text" name="name" class="form-control" required value="{{$coupon->name}}" placeholder="Coupon Name">

    </div>
    <div class="form-group col-md-6">
        <label for="discount" class="col-form-label">{{__('Discount')}}</label>
        <input type="number" name="discount" class="form-control" required step="0.01" value="{{$coupon->discount}}" placeholder="Enter Discount">
        <span class="small">{{__('Note: Discount in Percentage')}}</span>
    </div>
    <div class="form-group col-md-6">
        <label for="limit" class="col-form-label">{{__('Limit')}}</label>
        <input type="number" name="limit" class="form-control" required value="{{$coupon->limit}}" placeholder="Enter Limit">
    </div>
    <div class="form-group col-md-12">
        <div class="row">
                <div class="col-md-10">
                    <input class="form-control" name="code" type="text" id="auto-code" value="{{$coupon->code}}" placeholder="Enter Code">
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-primary" id="code-generate" ><i class="ti ti-history"></i></a>
                    {{-- <a href="#" class="btn btn-xs btn-icon-only width-auto" id="code-generate"><i class="fas fa-history"></i> {{__('Generate')}}</a> --}}
                </div>
            </div>
    </div>
    <div class="modal-footer col-md-12">
        {{Form::submit(__('Update'),array('class'=>'btn btn-primary '))}}
    </div>
</div>
{{ Form::close() }}

