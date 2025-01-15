@extends('layouts.admin')
@section('page-title')
    {{__('Opportunities')}}
@endsection
@section('title')
    {{__('Opportunities')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Opportunities')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('opportunities.index') }}" class="btn btn-sm btn-primary btn-icon me-1" data-bs-toggle="tooltip" title="{{ __('List View') }}">
        <i class="ti ti-list text-white"></i>
    </a>
  
    @can('Create Opportunities')
            <a href="#" data-size="lg" data-url="{{ route('opportunities.create',['opportunities',0]) }}" data-ajax-popup="true" data-bs-toggle="tooltip" data-title="{{__('Create New Opportunities')}}" title="{{__('Create')}}" class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
    @endcan
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{asset('assets/css/plugins/dragula.min.css')}}">
@endpush

@push('script-page')
    <script src="{{asset('assets/js/plugins/dragula.min.js')}}"></script>

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

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                        $.ajax({
                            url: '{{route('opportunities.change.order')}}',
                            type: 'POST',
                            data: {opo_id: id, stage_id: stage_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                                show_toastr('Success', 'Opportunities successfully updated', 'success');
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
                    foreach ($stages as $stage){
                        $json[] = 'kanban-blacklist-'.$stage->id;
                    }
                @endphp
    
            <div class="row kanban-wrapper horizontal-scroll-cards kanban-board" data-containers='{!! json_encode($json) !!}' data-plugin="dragula">
                @foreach($stages as $stage)
                    @php
                        $opportunities =$stage->opportunity($stage->id)
                    @endphp
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="float-end">
                                        <button class="btn btn-sm btn-primary btn-icon task-header">
                                            <span class="count text-white">{{count($opportunities)}}</span>
                                        </button>
                                    </div>
                                    <h4 class="mb-0">{{$stage->name}}</h4>
                                </div>
                                <div class="card-body kanban-box" id="kanban-blacklist-{{$stage->id}}" data-id="{{$stage->id}}">
                                    @foreach($opportunities as $opportunity)
                                       
                                        <div class="card" data-id="{{$opportunity->id}}">
                                            <div class="pt-3 ps-3">
                                                
                                                    <div class="card-header border-0 pb-0 position-relative">
                                                        <h5>
                                                            <a href="{{ route('opportunities.edit',$opportunity->id) }}"
                                                            data-bs-whatever="{{__('Opportunity Edit')}}"
                                                            data-bs-toggle="tooltip"  title data-bs-original-title="{{__('Opportunity Edit')}}" >{{ ucfirst($opportunity->name)}}</a></h5>
                                                            <div class="card-header-right">
                                                                <div class="btn-group card-option">
                                                                    @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                                                        <button type="button" class="btn dropdown-toggle"
                                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i class="ti ti-dots-vertical"></i>
                                                                        </button>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                                @can('Edit Opportunities')
                                                                                    <a href="{{ route('opportunities.edit',$opportunity->id) }}" class="dropdown-item" data-title="{{__('Edit Opportunities')}}">
                                                                                        <i  class="ti ti-pencil"></i>
                                                                                        <span>{{__('Edit')}}</span>
                                                                                    </a>
                                                                                @endcan
            
                                                                                @can('Show Opportunities')
                                                                                <a href="#!" class="dropdown-item" data-size="lg"  data-url="{{ route('opportunities.show', $opportunity->id) }}" 
                                                                                    data-ajax-popup="true" data-title="{{__('opportunities')}}">
                                                                                    <i class="ti ti-eye"></i>
                                                                                    <span>{{__('View Opportunities')}}</span>
                                                                                </a>
                                                                                @endcan
            
                                                                                @can('Delete Opportunities')
                                                                                    
                                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunity->id],'id'=>'task-delete-form-'.$opportunity->id]) !!}
                                                                                        <a href="#!" class="dropdown-item show_confirm" class="dropdown-item">
                                                                                            <i class="ti ti-trash"></i>{{ __('Delete') }}
                                                                                        </a>
                                                                                        {!! Form::close() !!}
                                                                                 
                                                                                @endcan
                                                                            
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                    </div>
                                                <div class="card-body">
                                                   
                                                    {{-- <p class="text-muted text-sm">{{ $opportunity->description }}</p>   --}}
                                                    <h6 data-bs-toggle="tooltip" title="{{__('Account name')}}"> {{ucfirst(!empty($opportunity->accounts)?$opportunity->accounts->name:'-')}}
                                                    </h6>
                                                    <p class="text-muted text-sm" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Description') }}">{{ $opportunity->description }}</p>                                                

                                                    {{-- <p href="#" class="text-sm" data-bs-toggle="tooltip" data-title="{{__('Ladna Barka')}}">{{ucfirst(!empty($opportunity->accounts->name)?$opportunity->accounts->name:'-')}}  </p>                                           --}}
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <ul class="list-inline mb-0">
                                                            <li class="list-inline-item d-inline-flex align-items-center"><i
                                                                    class="f-16 text-primary ti ti-message-2"></i>{{\Auth::user()->priceFormat($opportunity->amount)}}</li>
                                                        </ul>
                                                        <ul class="list-inline mb-0">
                                                            <li class="list-inline-item d-inline-flex align-items-center"><i
                                                                    class="f-16 text-primary ti ti-message-2"></i>{{\Auth::user()->dateFormat($opportunity->close_date)}}</li>
                                                        </ul>

                                                    
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
