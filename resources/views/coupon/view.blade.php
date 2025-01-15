@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{__('Coupon Detail')}}
@endsection
@section('title')
    {{__('Coupon Detail')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('coupon.index')}}">{{__('Coupon')}}</a></li>
    <li class="breadcrumb-item">{{$coupon->name}}</li>
@endsection
@section('action-btn')

@endsection
@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    {{$coupon->name}}
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="datatable" class="table datatable align-items-center">
                            <thead class="thead-light">
                                <tr>                      
                                    <th scope="col" class="sort" data-sort="name"> {{__('User')}}</th>
                                <th scope="col" class="sort" data-sort="name"> {{__('Date')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userCoupons as $userCoupon)
                                <tr class="font-style">
                                    <td>{{ !empty($userCoupon->userDetail)?$userCoupon->userDetail->name:'' }}</td>
                                    <td>{{ $userCoupon->created_at }}</td>
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

