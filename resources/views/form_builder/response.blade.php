@extends('layouts.admin')

@section('page-title')
    {{ $form->name . __("'s Response") }}
@endsection
@section('title')
    {{ $form->name . __("'s Response") }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('form_builder.index') }}">{{ __('Form Builder') }}</a></li>
    <li class="breadcrumb-item">{{ __('Response') }}</li>
@endsection
@section('action-btn')
<div class="action-btn  ms-2">
    <a href="{{ route('form_builder.index') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" title="{{__('Back')}}">
        <i class="ti ti-chevron-left"></i>
    </a>
</div>

@endsection


@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable" id="datatable">
                            @if($form->response->count() > 0)
                                @php
                                    $first = null;
                                    $second = null;
                                    $third = null;
                                    $i = 0;
                                @endphp
                                @foreach ($form->response as $response)
                                    @php
                                        $i++;
                                            $resp = json_decode($response->response,true);
                                            if(count($resp) == 1)
                                            {
                                                $resp[''] = '';
                                                $resp[' '] = '';
                                            }
                                            elseif(count($resp) == 2)
                                            {
                                                $resp[''] = '';
                                            }
                                            $firstThreeElements = array_slice($resp, 0, 3);

                                            $thead= array_keys($firstThreeElements);
                                            $head1 = ($first != $thead[0]) ? $thead[0] : '';
                                            $head2 = (!empty($thead[1]) && $second != $thead[1]) ? $thead[1] : '';
                                            $head3 = (!empty($thead[2]) && $third != $thead[2]) ? $thead[2] : '';
                                    @endphp
                                    @if(!empty($head1) || !empty($head2) || !empty($head3) && $head3 != ' ')
                                        <tr>
                                            <td><b>{{ $head1 }}</b></td>
                                            <td><b>{{ $head2 }}</b></td>
                                            <td><b>{{ $head3 }}</b></td>
                                            <td><b>{{__('Action')}}</b></td>
                                        </tr>
                                    @endif
                                    @php
                                        $first =  $thead[0];
                                        $second =  $thead[1];
                                        $third =  $thead[2];
                                    @endphp
                                    <tr>
                                        @foreach(array_values($firstThreeElements) as $ans)
                                            <td>{{$ans}}</td>
                                        @endforeach
                                        <td class="Action">
                                            <div class="action-btn">
                                                <a href="#" data-url="{{ route('response.detail', $response->id) }}"
                                                    data-ajax-popup="true" data-size="md"
                                                    title="{{ __('Response Detail') }}"
                                                    class="mx-3 btn btn-sm bg-warning align-items-center text-white"
                                                    data-bs-toggle="tooltip" data-title="{{ __('Response Detail') }}"
                                                    title="{{ __('Response') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                        @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
