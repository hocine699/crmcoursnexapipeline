@extends('layouts.admin')
@section('page-title')
    {{__('Lead')}}
@endsection
@section('title')
        <div class="page-header-title">
           {{__('Lead')}}
        </div>

@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item ">{{__('Lead')}}</li>
@endsection
@section('action-btn')
        <a href="{{ route('lead.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip" title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
    @can('Create Lead')
        <a href="#" data-url="{{ route('lead.create',['lead',0]) }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{__('Create New Lead')}}"title="{{__('Create')}}" class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@push('script-page')


<script src="{{ asset('assets/js/plugins/dragula.min.js') }}"></script>

<script>
    !function (a) {
        "use strict";
        var t = function () {
            this.$body = a("body")
        };
        t.prototype.init = function () {
            a('[data-plugin="dragula"]').each(function () {
                var t = a(this).data("containers"), n = [];
                if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                var r = a(this).data("handleclass");
                r ? dragula(n, {
                    moves: function (a, t, n) {
                        return n.classList.contains(r)
                    }
                }) : dragula(n).on('drop', function (el, target, source, sibling) {

                    var order = [];
                    $("#" + target.id + " > div").each(function () {
                        order[$(this).index()] = $(this).attr('data-id');
                    });

                    var id = $(el).attr('data-id');

                    var old_status = $("#" + source.id).data('status');
                    var new_status = $("#" + target.id).data('status');
                    var stage_id = $(target).attr('data-id');
                    var pipeline_id = '1';
                    var status_id = $(target).attr('data-id');

                    $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                    $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                    $.ajax({
                        url: '{{route('lead.change.order')}}',
                        type: 'POST',
                        data: {lead_id: id, status_id: status_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                        success: function (data) {
                            show_toastr('Success', 'Lead successfully updated', 'success');
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('{{__("Error")}}', data.error, 'error')


                        }
                    });
                });
            })
        }, a.Dragula = new t, a.Dragula.Constructor = t
    }(window.jQuery), function (a) {
        "use strict";

        a.Dragula.init()

    }(window.jQuery);
</script>

@endpush
@section('filter')
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12">

        @php
            $json = [];
            foreach($statuss as $id => $status){
                $json[] = 'kanban-blacklist-'.$id;
            }
        @endphp

        <div class="row kanban-wrapper horizontal-scroll-cards kanban-board" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
            @foreach($statuss as $id=>$status)
                @php
                    $leads =\App\Models\Lead::leads($id);
                @endphp
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <div class="float-end">
                                    <button class="btn btn-sm btn-primary btn-icon task-header">
                                        <span class="count text-white">{{count($leads)}}</span>
                                    </button>
                                </div>
                                <h4 class="mb-0">{{$status}}</h4>
                            </div>
                            <div class="card-body kanban-box" id="kanban-blacklist-{{$id}}" data-id="{{$id}}">
                                @foreach($leads as $lead)

                                    <div class="card" data-id="{{$lead->id}}">
                                        <div class="pt-3 ps-3">

                                            <div class="card-header border-0 pb-0 position-relative">
                                                <h5>
                                                    <a href="{{ route('lead.edit',$lead->id) }}"
                                                    data-bs-whatever="{{__('Edit Lead Details')}}"
                                                    data-bs-toggle="tooltip"  title data-bs-original-title="{{__('Edit Lead Detail')}}" > {{ ucfirst($lead->name)}}</a></h5>
                                                    <div class="card-header-right">
                                                        <div class="btn-group card-option">
                                                            <button type="button" class="btn dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="ti ti-dots-vertical"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                @if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead'))
                                                                @can('Edit Lead')
                                                                        <a href="{{ route('lead.edit',$lead->id) }}" class="dropdown-item" data-size="lg">
                                                                            <i  class="ti ti-pencil"></i>
                                                                            <span>{{__('Edit')}}</span>
                                                                        </a>
                                                                    @endcan

                                                                    @can('Show Lead')
                                                                    <a href="#!" class="dropdown-item" data-size="md" data-url="{{ route('lead.show', $lead->id) }}" data-ajax-popup="true" data-title="{{__('Lead Details')}}">
                                                                        <i class="ti ti-eye"></i>
                                                                        <span>{{__('View Lead')}}</span>
                                                                    </a>
                                                                    @endcan

                                                                    @can('Delete Lead')

                                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]) !!}
                                                                            <a href="#!" class="dropdown-item show_confirm">
                                                                                <i class="ti ti-trash"></i>{{ __('Delete') }}
                                                                            </a>
                                                                            {!! Form::close() !!}

                                                                    @endcan
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="card-body">
                                                <h6 data-bs-toggle="tooltip" title="{{__('Account name')}}"> {{ucfirst(!empty($lead->accounts)?$lead->accounts->name:'-')}}
                                                </h6>
                                                <p class="text-muted text-sm" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Description') }}">{{ $lead->description }}</p>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <ul class="list-inline mb-0">
                                                        <li class="list-inline-item d-inline-flex align-items-center"><i
                                                                class="f-16 text-primary ti ti-message-2"></i>{{\Auth::user()->priceFormat($lead->opportunity_amount)}}</li>
                                                    </ul>
                                                    <ul class="list-inline mb-0">
                                                        <li class="list-inline-item d-inline-flex align-items-center"><i
                                                                class="f-16 text-primary ti ti-message-2"></i>{{\Auth::user()->dateFormat($lead->created_at)}}</li>
                                                    </ul>
                                                    <div class="user-group">
                                                        @foreach($users as $user)

                                                                    <a href="{{asset('/storage/upload/profile/'.$user->avatar)}}" class="avatar rounded-circle avatar-sm" data-bs-original-title="{{$user->name}}" target="_blank" data-bs-toggle="tooltip">
                                                                        <img class="rounded border-2 border border-primary" @if(!empty($user->avatar)) src="{{asset('/storage/upload/profile/'.$user->avatar)}}" @else avatar="{{$user->username}}" @endif class="">
                                                                    </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- [ sample-page ] end -->
    </div>
</div>
@endsection
