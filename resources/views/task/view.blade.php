<div class="row">
    <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $task->name }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Stage')}}</span></dt>
                    <dd class="col-md-5">
                        {{ !empty($task->stages->name)?$task->stages->name:'-' }}
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Priority')}}</span></dt>
                    <dd class="col-md-5">
                        @if($task->priority == 0)
                            <span class="badge bg-success p-2 px-3">{{ __(\App\Models\Task::$priority[$task->priority]) }}</span>
                        @elseif($task->priority == 1)
                            <span class="badge bg-info p-2 px-3">{{ __(\App\Models\Task::$priority[$task->priority]) }}</span>
                        @elseif($task->priority == 2)
                            <span class="badge bg-warning p-2 px-3">{{ __(\App\Models\Task::$priority[$task->priority]) }}</span>
                        @elseif($task->priority == 3)
                            <span class="badge bg-danger p-2 px-3">{{ __(\App\Models\Task::$priority[$task->priority]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($task->start_date)}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Due Date')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($task->due_date  )}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Assigned')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $task->parent }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Assigned Name')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">
                        @if(!empty($task->parent_id))
                        {{  $task->getparent($task->parent,$task->parent_id)}}
                        @endif
                    </span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ $task->description }}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Assigned User')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{ !empty($task->assign_user)?$task->assign_user->name:''}}</span></dd>

                    <dt class="col-md-5"><span class="h6 text-md mb-0">{{__('Created')}}</span></dt>
                    <dd class="col-md-5"><span class="text-md">{{\Auth::user()->dateFormat($task->created_at)}}</span></dd>

                </dl>
            </div>

    </div>

    <div class="w-100 text-end pr-2">
        @can('Edit Task')
        <div class="action-btn ">
            <a href="{{ route('task.edit',$task->id) }}" class="mx-3 btn btn-sm bg-info align-items-center text-white " data-bs-toggle="tooltip" data-title="{{__('Edit Call')}}" title="{{__('Edit')}}"><i class="ti ti-pencil"></i>
            </a>
        </div>

        @endcan
    </div>
</div>
