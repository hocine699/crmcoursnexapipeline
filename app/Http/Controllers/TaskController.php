<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CommonCase;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Opportunities;
use App\Exports\TaskExport;
use App\Models\Stream;
use App\Models\Task;
use App\Models\TaskStage;
use App\Models\User;
use App\Models\Utility;
use App\Models\UserDefualtView;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Compound;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage Task')) {
            if (\Auth::user()->type == 'owner') {
                $tasks = Task::with('assign_user','stages')->where('created_by', \Auth::user()->creatorId())->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'task';
                $defualtView->view   = 'list';
                User::userDefualtView($defualtView);
            } else {
                $tasks = Task::with('assign_user','stages')->where('user_id', \Auth::user()->id)->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'task';
                $defualtView->view   = 'list';
            }
            return view('task.index', compact('tasks'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type, $id)
    {
        if (\Auth::user()->can('Create Task')) {
            $stage   = TaskStage::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $priority = Task::$priority;
            $parent   = Task::$parent;

            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $account_name   = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('task.create', compact('stage', 'parent', 'user', 'priority', 'account_name', 'type'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Task')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'stage' => 'required',
                    'attachment' => 'image',
                    //'parent_id'=>'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $task                = new Task();
            $task['user_id']     = $request->user;
            $task['name']        = $request->name;
            $task['stage']       = $request->stage;
            $task['priority']    = $request->priority;
            $task['start_date']  = $request->start_date;
            $task['due_date']    = $request->end_date;
            $task['parent']      = $request->parent;
            $task['parent_id']   = $request->parent_id ?? '0';
            $task['account']     = $request->account;
            $task['description'] = $request->description;

            if (!empty($request->attachment)) {
                $image_size = $request->file('attachment')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if ($result == 1) {
                    $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('attachment')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $dir        = 'upload/profile/';

                    if (\File::exists($dir)) {
                        \File::delete($dir);
                    }
                    $url = '';
                    $task['attachment']  = !empty($request->attachment) ? $fileNameToStore : '';
                    $path = Utility::upload_file($request, 'attachment', $fileNameToStore, $dir, []);

                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }

            $task['created_by']  = \Auth::user()->creatorId();
            $task->save();
            Stream::create(
                [
                    'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'task',
                            'stream_comment' => '',
                            'user_name' => $task->name,
                        ]
                    ),
                ]
            );
            $Assign_user_phone = User::where('id', $request->user)->first();
            $setting  = Utility::settings(\Auth::user()->creatorId());

            $uArr = [
                'task_name' => $request->name,
                'task_start_date' => $request->start_date,
                'task_due_date' => $request->end_date,
                'task_stage' => $request->stage,
                'task_description' => $request->description,
                // 'task_assign_user' => $Assign_user_phone->name??'',
            ];


            $resp = Utility::sendEmailTemplate('task_assigned', [$task->id => $Assign_user_phone->email], $uArr);

            if (isset($setting['twilio_task_create']) && $setting['twilio_task_create'] == 1) {
                // $msg = "New Task " . $request->name . " created by " . \Auth::user()->name . '.';
                $uArr = [
                    'task_name' => $request->name,
                    'task_start_date' => $request->start_date,
                    'task_due_date' => $request->end_date,
                    'user_name' => \Auth::user()->name,
                ];
                Utility::send_twilio_msg($Assign_user_phone->phone, 'new_task', $uArr);
            }

            if ($request->get('is_check')  == '1') {
                $type = 'task';

                $request1 = new Task();
                $request1->title = $request->name;
                $request1->start_date = $request->start_date;
                $request1->end_date = $request->end_date;
                Utility::addCalendarData($request1, $type);
            }
            //webhook
            $module = 'New Task';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($task);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status != true) {
                    $msg = "Webhook call failed.";
                }
            }
            if (\Auth::user()) {
                return redirect()->back()->with('success', __('Task successfully created!') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        if (\Auth::user()->can('Show Task')) {
            return view('task.view', compact('task'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        if (\Auth::user()->can('Edit Task')) {
            $stage   = TaskStage::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $priority = Task::$priority;
            $user     = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', ''); 
            $parent   = Task::$parent;

            // get previous user id
            $previous = Task::where('id', '<', $task->id)->max('id');
            // get next user id
            $next = Task::where('id', '>', $task->id)->min('id');
            $account_name  = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $log_type = 'task comment';
            $streams  = Stream::where('log_type', $log_type)->get();

            return view('task.edit', compact('task', 'stage', 'user', 'priority', 'streams', 'parent', 'previous', 'next', 'account_name'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if (\Auth::user()->can('Edit Task')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $task['user_id']     = $request->user;
            $task['name']        = $request->name;
            $task['stage']       = $request->stage;
            $task['priority']    = $request->priority;
            $task['start_date']  = $request->start_date;
            $task['due_date']    = $request->due_date;
            $task['account']     = $request->account;
            $task['description'] = $request->description;
            $task['created_by']  = \Auth::user()->creatorId();
            $task->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'task',
                            'stream_comment' => '',
                            'user_name' => $task->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Task Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if (\Auth::user()->can('Delete Task')) {
            $file_path = 'upload/profile/' . $task->attachment;
            $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
            $task->delete();

            return redirect()->back()->with('success', 'Task ' . $task->name . ' Deleted!');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $tasks = Task::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'task';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);

        return view('task.grid', compact('tasks'));
    }

    public function getparent(Request $request)
    {
        if ($request->parent == 'Account') {
            $parent = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'Lead') {
            $parent = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'Contact') {
            $parent = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'Opportunities') {
            $parent = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'Case') {
            $parent = CommonCase::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $parent = '';
        }

        return response()->json($parent);
    }

    public function ganttChart(Request $request, $duration = 'Week')
    {

        $tasks = Task::where('created_by', \Auth::user()->creatorId())->first();


        if (!empty($tasks)) {
            $tasks = Task::where('created_by', \Auth::user()->creatorId())->get();


            foreach ($tasks as $task) {

                $tmp                 = [];
                $tmp['id']           = 'task_' . $task->id;
                $tmp['name']         = $task->name;
                $tmp['start']        = $task->start_date;
                $tmp['end']          = $task->due_date;
                $tmp['custom_class'] = strtolower(Task::$priority[$task->priority]);
                $tmp['progress']     = 0;
                $tmp['extra']        = [
                    'priority' => __(Task::$priority[$task->priority]),
                    'stage' => !empty($task->stages) ? $task->stages->name : '',
                    'description' => $task->description,
                    'duration' => Carbon::parse($task->start_date)->format('d M Y H:i A') . ' - ' . Carbon::parse($task->due_date)->format('d M Y H:i A'),
                ];
                $ganttTasks[]        = $tmp;
            }


            return view('task.ganttChart', compact('ganttTasks', 'duration'));
        } else {
            return redirect()->back()->with('error', 'Plaese Enter the Task');
        }
    }

    public function ganttPost(Request $request)
    {

        $id               = trim($request->task_id, 'task_');
        $task             = Task::find($id);
        $task->start_date = $request->start;
        $task->due_date   = $request->end;
        $task->save();

        return response()->json(
            [
                'is_success' => true,
                'message' => __("Time Updated"),
            ],
            200
        );
    }

    public function fileExport()
    {

        $name = 'Task_' . date('Y-m-d i:h:s');
        $data = Excel::download(new TaskExport(), $name . '.xlsx');
        ob_end_clean();


        return $data;
    }
    public function get_task_data(Request $request)
    {
        $arrayJson = [];

        if ($request->get('calender_type') == 'goggle_calender') {
            $type = 'task';
            $arrayJson =  Utility::getCalendarData($type);
        } else {
            $data = Task::where('created_by', \Auth::user()->creatorId())->get();

            foreach ($data as $val) {

                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "url" => route('task.show', $val['id']),
                    "allDay" => true,
                ];
            }
        }

        return $arrayJson;
    }
}
