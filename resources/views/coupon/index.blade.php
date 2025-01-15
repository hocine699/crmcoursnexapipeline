@extends('layouts.admin')
@push('script-page')
    <script>
        $(document).on('click', '.code', function () {
            var type = $(this).val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');

                // Clear the value of the auto-generated code field
                $('#auto-code').val('');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');

                // Clear the value of the manual code field
                $('#manual').val('');
            }
        });

        $(document).on('click', '#code-generate', function () {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush
@section('page-title')
    {{__('Coupon')}}
@endsection
@section('title')
    {{__('Coupon')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item">{{__('Coupon')}}</li>

@endsection
@section('action-btn')
<div class="action-btn bg-warning ms-2">
    <a href="#" data-url="{{ route('coupon.create') }}" data-size="md" data-ajax-popup="true" data-title="{{__('Create New Coupon')}}" title="{{__('Create')}}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip">
        <i class="ti ti-plus"></i>
    </a>
</div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive overflow_hidden">
                            <table id="datatable" class="table datatable align-items-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name"> {{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Code')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Discount (%)')}}</th>
                                        <th scope="col">{{__('Limit')}}</th>
                                        <th scope="col" class="sort" data-sort="completion"> {{__('Used')}}</th>
                                        <th scope="col" class="action-wid">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($coupons as $coupon)
                                        <tr>
                                            <td class="budget">{{ $coupon->name }} </td>
                                            <td>{{ $coupon->code }}</td>
                                            <td>
                                                {{ $coupon->discount }}
                                            </td>
                                            <td>{{ $coupon->limit }}</td>
                                            <td>{{ $coupon->used_coupon() }}</td>
                                            <td class="">
                                                <div class="actions ml-3">
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('coupon.show',$coupon->id) }}" class="mx-3 btn btn-sm  bg-warning align-items-center text-white" data-bs-toggle="tooltip" data-title="{{__('Coupon Details')}}" title="{{__('View')}}">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>

                                                    <div class="action-btn me-2">
                                                        <a href="#!" class="mx-3 btn btn-sm  align-items-center text-white bg-info " data-size="md" data-url="{{ route('coupon.edit',$coupon->id) }}" data-ajax-popup="true" data-title="{{__('Edit Coupon')}}" data-bs-toggle="tooltip"title="{{__('Edit')}}">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                    </div>

                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['coupon.destroy', $coupon->id]]) !!}
                                                            <a href="#!" class="mx-3 bg-danger btn btn-sm  align-items-center text-white show_confirm" data-bs-toggle="tooltip" title='Delete'>
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

